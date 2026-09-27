<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Webhook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    // =========================
    // CREATE PAYMENT INTENT
    // =========================
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        // =========================
        // OWNERSHIP CHECK
        // =========================
        $order = Order::with('items', 'payment')
            ->where('id', $request->order_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized',
            ], 404);
        }

        // =========================
        // ALREADY PAID 
        // =========================
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This order has already been paid',
            ], 400);
        }

        // =========================
        // GUARD endpoint is only for orders placed with the "card" payment method
        // =========================
        $payment = $order->payment;

        if (!$payment || $payment->payment_method !== 'card') {
            return response()->json([
                'success' => false,
                'message' => 'This order is not set up for card payment',
            ], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        if ($payment->transaction_id) {
            try {
                $existing = PaymentIntent::retrieve($payment->transaction_id);

                if (in_array($existing->status, [
                    'requires_payment_method',
                    'requires_confirmation',
                    'requires_action',
                ])) {
                    return response()->json([
                        'success' => true,
                        'clientSecret' => $existing->client_secret,
                    ]);
                }

                if (in_array($existing->status, ['succeeded', 'processing'])) {
                     
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment already submitted for this order — confirming now, please check your orders shortly.',
                    ], 409);
                }

            } catch (\Exception $e) {
                
            }
        }

        $intent = PaymentIntent::create([
            'amount' => (int) round($order->total_price * 100), 
            'currency' => 'pkr', 
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        // =========================
        // UPDATE THE EXISTING PAYMENT ROW 
        // =========================
        $payment->update([
            'payment_type' => 'stripe',
            'amount' => $order->total_price,
            'transaction_id' => $intent->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'clientSecret' => $intent->client_secret,
        ]);
    }

    // =========================
    // STRIPE WEBHOOK
    // Stripe calls this directly no auth token, verified via signature
    // =========================
    public function webhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // =========================
        // PAYMENT SUCCEEDED
        // =========================
        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $payment = Payment::where('transaction_id', $intent->id)->first();

            if ($payment && $payment->status === 'pending') {

                DB::beginTransaction();

                try {
                    $payment->update([
                        'gateway_response' => $intent->toArray(),
                    ]);

                    app(PaymentController::class)->markPaymentSuccessfulPublic($payment);

                    DB::commit();

                } catch (\Exception $e) {

                    DB::rollBack();

                    Log::error(
                        'Stripe webhook: failed to finalize payment for intent '
                        . $intent->id . ': ' . $e->getMessage()
                    );

    
                    try {
                        Refund::create(['payment_intent' => $intent->id]);

                        $payment->update([
                            'status' => 'rejected',
                            'rejection_reason' => 'Order could not be fulfilled (' . $e->getMessage() . '). Payment refunded automatically.',
                            'gateway_response' => $intent->toArray(),
                        ]);

                        $payment->order()->update(['payment_status' => 'rejected']);

                    } catch (\Exception $refundException) {
                        Log::error(
                            'Stripe webhook: refund also failed for intent '
                            . $intent->id . ': ' . $refundException->getMessage()
                        );
                       
                    }
                    return response()->json(['success' => true]);
                }
            }
        }

        // =========================
        // PAYMENT FAILED
        // =========================
        if ($event->type === 'payment_intent.payment_failed') {
            $intent = $event->data->object;

            Payment::where('transaction_id', $intent->id)->update([
                'status' => 'rejected',
                'rejection_reason' => 'Card payment failed',
                'gateway_response' => $intent->toArray(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
