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

    public function updatePaymentStatus(Request $request, $id)
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
        $request->validate([
            'payment_status' => 'required|in:paid,rejected',
            'rejection_reason' => 'required_if:payment_status,rejected|string|max:1000',
        ]);

        DB::beginTransaction();
        try {

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

            if (in_array($payment->status, ['paid', 'rejected'])) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment already processed',
                ], 400);
            }

            if ($request->payment_status === 'paid') {
                $this->markPaymentSuccessful($payment, $request->user()->id);
            }

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

    private function markPaymentSuccessful(Payment $payment, ?int $verifiedBy = null)
    {
        $order = $payment->order;

        if (in_array($payment->status, ['paid', 'rejected'])) {
            return; 
        }
        foreach ($order->items as $item) {

            if (!$item->product) {
                throw new \Exception('Product not found');
            }

            if ($item->quantity > $item->product->stock) {
                throw new \Exception($item->product->name . ' is out of stock');
            }
        }
        foreach ($order->items as $item) {
            $item->product->decrement('stock', $item->quantity);
        }

        $commissionPercent = \App\Models\CommissionSetting::current();

        foreach ($order->items as $item) {
            $lineTotal = $item->price * $item->quantity;
            $commission = round($lineTotal * ($commissionPercent / 100), 2);

            $item->update([
                'commission_amount' => $commission,
                'net_amount' => $lineTotal - $commission,
            ]);
        }
        $order->refresh()->load('items');


        $shopChargesById = $order->shopCharges()->get()->keyBy('shop_id');
        
        foreach ($order->items->groupBy('shop_id') as $shopId => $shopItems) {
            $gross = $shopItems->sum(fn ($i) => $i->price * $i->quantity);
            $commission = $shopItems->sum('commission_amount');
            $net = $shopItems->sum('net_amount');
            $deliveryForShop = $shopChargesById->get($shopId)?->delivery_charge ?? 0;

            SellerPayout::create([
                'order_id' => $order->id,
                'shop_id' => $shopId,
                'gross_amount' => $gross,
                'commission_amount' => $commission,
                'net_amount' => $net,
                'delivery_charge' => $deliveryForShop,
                'status' => 'pending', 
            ]);
        }

        $payment->update([
            'status' => 'paid',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

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

                NotificationService::send(
                    $shop->user,
                    'payment_status',
                    'Payment verified',
                    'Payment for order ' . $order->order_number . ' has been verified. You can start preparing the order.',
                    ['order_id' => $order->id]
                );
            }
        }


        NotificationService::send(
            $order->user,
            'payment_status',
            'Payment confirmed',
            'Your payment for order ' . $order->order_number . ' has been confirmed.',
            ['order_id' => $order->id]
        );
    }


    public function markPaymentSuccessfulPublic(Payment $payment)
    {
        $this->markPaymentSuccessful($payment, null);
    }
}
