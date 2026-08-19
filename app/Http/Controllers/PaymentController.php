<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\OrderItem;
use App\Models\SellerPayout;
use Illuminate\Support\Facades\DB;

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

                // STOCK CHECK AGAIN
                foreach ($order->items as $item) {

                    if (!$item->product) {

                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Product not found',
                        ], 400);
                    }

                    if ($item->quantity > $item->product->stock) {

                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => $item->product->name .
                                ' is out of stock',
                        ], 400);
                    }
                }

                // =========================
                // DEDUCT STOCK
                // =========================
                foreach ($order->items as $item) {

                    $item->product->decrement(
                        'stock',
                        $item->quantity
                    );
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
                        'status' => 'pending', // waiting on customer confirmation
                    ]);
                }

                // =========================
                // UPDATE PAYMENT
                // =========================
                $payment->update([
                    'status' => 'paid',
                    'verified_by' => $request->user()->id,
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
}