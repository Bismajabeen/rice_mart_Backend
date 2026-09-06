<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\OrderItem;
use App\Models\SellerPayout;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class PaymentController extends Controller
{
    // =========================
    // ADMIN PAYMENT LIST
    // =========================
    public function adminPayments(Request $request)
    {
        if (
            !$request->user()->hasAnyRole([
                'admin',
                'super_admin',
            ])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $payments = Payment::with([
            'order.user',
            'order.items.product',
            'order.items.shop',
        ])
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments,
        ]);
    }

    // =========================
    // ADMIN UPDATE PAYMENT STATUS
    // =========================
    public function updatePaymentStatus(Request $request, $id)
    {
        // =========================
        // ADMIN CHECK
        // =========================
        if (
            !$request->user()->hasAnyRole([
                'admin',
                'super_admin',
            ])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // =========================
        // VALIDATION
        // =========================
        $request->validate([
            'payment_status' => 'required|in:paid,rejected',
            'rejection_reason' => 'required_if:payment_status,rejected|string|max:1000',
        ]);

        DB::beginTransaction();
        try {

            // =========================
            // GET PAYMENT
            // =========================
            $payment = Payment::with([
                'order.items.product',
                'order.items.shop',
            ])->find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                ], 404);
            }

            $order = $payment->order;

            // =========================
            // PREVENT DOUBLE APPROVAL
            // =========================
            if (in_array($payment->status, ['paid', 'rejected'])) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment already processed',
                ], 400);
            }

            // =========================
            // PAYMENT APPROVED
            // =========================
            if ($request->payment_status === 'paid') {
                $this->markPaymentSuccessful($payment, $request->user()->id);
            }

            // =========================
            // PAYMENT REJECTED
            // =========================
            if ($request->payment_status === 'rejected') {

                $payment->update([
                    'status' => 'rejected',
                    'verified_by' => $request->user()->id,
                    'verified_at' => now(),
                    'rejection_reason' => $request->rejection_reason,
                ]);

                $order->update([
                    'payment_status' => 'rejected',
                ]);

                // =========================
                // NOTIFY BUYER — payment rejected
                // =========================
                NotificationService::send(
                    $order->user,
                    'payment_status',
                    'Payment rejected',
                    'Your payment for order ' . $order->order_number . ' was rejected: ' . $request->rejection_reason,
                    ['order_id' => $order->id]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully',
                'payment' => $payment->fresh(),
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // SHARED "MARK PAID" LOGIC
    // Used by both the admin manual-approval path above (EasyPaisa/
    // JazzCash) and the Stripe webhook (card payments, auto-verified).
    // $verifiedBy is null for Stripe since no human reviewed it.
    // =========================
    private function markPaymentSuccessful(Payment $payment, ?int $verifiedBy = null)
    {
        $order = $payment->order;

        if (in_array($payment->status, ['paid', 'rejected'])) {
            return; // already processed, don't double-run
        }

        // =========================
        // STOCK CHECK AGAIN
        // =========================
        foreach ($order->items as $item) {

            if (!$item->product) {
                throw new \Exception('Product not found');
            }

            if ($item->quantity > $item->product->stock) {
                throw new \Exception($item->product->name . ' is out of stock');
            }
        }

        // =========================
        // DEDUCT STOCK
        // =========================
        foreach ($order->items as $item) {
            $item->product->decrement('stock', $item->quantity);
        }

        // =========================
        // COMMISSION — 5% of item price only, never delivery charge
        // =========================
        foreach ($order->items as $item) {
            $lineTotal = $item->price * $item->quantity;
            $commission = round($lineTotal * 0.05, 2);

            $item->update([
                'commission_amount' => $commission,
                'net_amount' => $lineTotal - $commission,
            ]);
        }

        // =========================
        // ONE PAYOUT ROW PER SHOP IN THIS ORDER
        // =========================
        $order->refresh()->load('items');


        // Delivery is charged once per distinct shop in the order (see
        // OrderController::checkout) — same share every shop in this
        // order gets, same math as SellerOrderController::sellerOrders().

        $shopCount = $order->items->pluck('shop_id')->unique()->count();
        $deliveryPerShop = $shopCount > 0 ? round($order->delivery_charge / $shopCount, 2) : 0;
        
        foreach ($order->items->groupBy('shop_id') as $shopId => $shopItems) {
            $gross = $shopItems->sum(fn ($i) => $i->price * $i->quantity);
            $commission = $shopItems->sum('commission_amount');
            $net = $shopItems->sum('net_amount');

            SellerPayout::create([
                'order_id' => $order->id,
                'shop_id' => $shopId,
                'gross_amount' => $gross,
                'commission_amount' => $commission,
                'net_amount' => $net,
                'delivery_charge' => $deliveryPerShop,
                'status' => 'pending', // waiting on customer confirmation
            ]);
        }

        // =========================
        // UPDATE PAYMENT
        // =========================
        $payment->update([
            'status' => 'paid',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        // =========================
        // UPDATE ORDER
        // =========================
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        // =========================
        // NOTIFY SELLER(S) — new order ready to prepare, and
        // payment verified (an order can contain items from
        // multiple shops, so notify every distinct shop owner
        // involved, once each)
        // =========================
        $shopIds = $order->items->pluck('shop_id')->unique();

        foreach ($shopIds as $shopId) {
            $shop = Shop::find($shopId);

            if ($shop) {
                NotificationService::send(
                    $shop->user,
                    'order_placed',
                    'New order received',
                    'You have a new order: ' . $order->order_number,
                    ['order_id' => $order->id]
                );

                // =========================
                // NOTIFY SELLER — payment verified, ready to prepare
                // =========================
                NotificationService::send(
                    $shop->user,
                    'payment_status',
                    'Payment verified',
                    'Payment for order ' . $order->order_number . ' has been verified. You can start preparing the order.',
                    ['order_id' => $order->id]
                );
            }
        }

        // =========================
        // NOTIFY BUYER — payment confirmed
        // =========================
        NotificationService::send(
            $order->user,
            'payment_status',
            'Payment confirmed',
            'Your payment for order ' . $order->order_number . ' has been confirmed.',
            ['order_id' => $order->id]
        );
    }

    // =========================
    // PUBLIC WRAPPER — called by StripeController's webhook handler,
    // since markPaymentSuccessful() itself is private to this class.
    // =========================
    public function markPaymentSuccessfulPublic(Payment $payment)
    {
        $this->markPaymentSuccessful($payment, null);
    }
}
