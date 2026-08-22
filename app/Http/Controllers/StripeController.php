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
    // Called from the Flutter checkout screen when the user picks "Card"
    // =========================
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        // =========================
        // OWNERSHIP CHECK — only the customer who placed this order
        // can create a payment intent for it
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
        // ALREADY PAID — don't let a paid order be charged again
        // =========================
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This order has already been paid',
            ], 400);
        }

        // =========================
        // GUARD — this endpoint is only for orders placed with the
        // "card" payment method. Without this check, any authenticated
        // user could call this on their own EasyPaisa/JazzCash order and
        // silently overwrite that Payment row's type/transaction_id,
        // disconnecting it from the manual screenshot-review flow.
        // =========================
        $payment = $order->payment;

        if (!$payment || $payment->payment_method !== 'card') {
            return response()->json([
                'success' => false,
                'message' => 'This order is not set up for card payment',
            ], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // =========================
        // IDEMPOTENCY — if a usable PaymentIntent already exists for this
        // order (e.g. the user double-tapped "Place Order" or retried
        // after a dropped connection), reuse it instead of creating a
        // second one. Creating a second intent here would overwrite
        // transaction_id and orphan whichever intent the webhook ends up
        // confirming payment for.
        // =========================
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
                    // Stripe already has this charge in flight/succeeded —
                    // don't create a second one. The webhook will (or
                    // already did) mark the order paid.
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment already submitted for this order — confirming now, please check your orders shortly.',
                    ], 409);
                }

                // Any other status (canceled, etc.) — safe to fall through
                // and create a fresh intent below.
            } catch (\Exception $e) {
                // Couldn't retrieve the old intent (e.g. it no longer
                // exists) — fall through and create a new one.
            }
        }

        $intent = PaymentIntent::create([
            'amount' => (int) round($order->total_price * 100), // paisa/cents
            'currency' => 'pkr', // confirm your Stripe account supports PKR settlement — see note below
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        // =========================
        // UPDATE THE EXISTING PAYMENT ROW (created during checkout())
        // rather than inserting a second one for the same order — an
        // order should only ever have one Payment row.
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
    // Stripe calls this directly — no auth token, verified via signature
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

                    // =========================
                    // Stripe has already charged the customer, but we
                    // couldn't fulfil the order (e.g. stock ran out
                    // between checkout and payment). Refund automatically
                    // rather than leaving the order stuck in limbo with
                    // no way for the customer or admin to resolve it.
                    // =========================
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
                        // At this point manual intervention is required —
                        // the log line above is what an admin needs to act on.
                    }

                    // =========================
                    // Return 200 regardless of outcome above. We've fully
                    // handled this event (either fulfilled, or rejected +
                    // refunded/logged) inside our own try/catch. Returning
                    // a 500 here would make Stripe retry the same webhook,
                    // which would re-run stock deduction for whichever
                    // items succeeded before the failure — compounding
                    // the problem instead of fixing it.
                    // =========================
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
