<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;

class OrderController extends Controller
{
    // =========================
    // CHECKOUT (CUSTOMER)
    // =========================
    public function checkout(Request $request)
    {
        $user = $request->user();

        // =========================
        // VALIDATION
        // =========================
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'nullable|string|max:100',
            'address' => 'required|string',

            'payment_method' => 'required|in:easypaisa,jazzcash,card',
            'transaction_id' => 'required_if:payment_method,easypaisa,jazzcash|string|max:255',

            'cart' => 'required|array|min:1',
        ]);

        $cartItems = $request->cart;

        DB::beginTransaction();

        try {

            // =========================
            // PAYMENT SCREENSHOT
            // =========================
            $paymentProof = null;

            if (
                in_array($request->payment_method, [
                    'easypaisa',
                    'jazzcash',
                ])
            ) {

                $request->validate([
                    'payment_proof' => 'required|image|max:2048',
                ]);

              if ($request->hasFile('payment_proof')) {
                 $paymentProof = $request->file('payment_proof')
                 ->store('payments', 'public');
                }
            }

            // =========================
            // PAYMENT STATUS
            // =========================
            // $paymentStatus =
            //     $request->payment_method == 'card'
            //     ? 'paid'
            //     : 'pending';
            $paymentStatus = 'pending';

            // =========================
            // CREATE ORDER
            // =========================
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),

                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'city' => $request->city ?? '',
                'address' => $request->address,
                'total_price' => 0,

                'status' => 'pending',

                'payment_status' => $paymentStatus,
            ]);

            $total = 0;

            // =========================
            // PROCESS CART
            // =========================
            foreach ($cartItems as $item) {

                // VALIDATION
                if (!isset($item['product_id'], $item['quantity'])) {

                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid cart structure',
                    ], 400);
                }

                if ($item['quantity'] <= 0) {

                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Quantity must be greater than 0',
                    ], 400);
                }

                // =========================
                // FETCH PRODUCT
                // =========================
                $product = Product::where('id', $item['product_id'])
                    ->whereHas('shop', function ($q) {
                        $q->where('is_approved', 1);
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$product) {

                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found or inactive shop',
                    ], 400);
                }

                // =========================
                // STOCK CHECK
                // =========================
                if ($item['quantity'] > $product->stock) {

                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => $product->name . ' stock not available',
                    ], 400);
                }

                // =========================
                // CALCULATE SUBTOTAL
                // =========================
                $subtotal = $product->price * $item['quantity'];

                $total += $subtotal;

                // =========================
                // CREATE ORDER ITEM
                // =========================
                OrderItem::create([
                    'order_id' => $order->id,

                    'shop_id' => $product->shop_id,

                    'product_id' => $product->id,

                    'quantity' => $item['quantity'],

                    'price' => $product->price,

                    'status' => 'pending',
                ]);
            }

            // =========================
            // UPDATE TOTAL
            // =========================
            $order->update([
                'total_price' => $total,
            ]);
                // =========================
                // CREATE PAYMENT RECORD
                // =========================
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method,
                    // 'payment_type' => in_array($request->payment_method, ['easypaisa', 'jazzcash'])
                    //     ? 'manual'
                    //     : 'gateway',
                    'payment_type' => 'manual',
                    'amount' => $total,
                    'transaction_id' => $request->transaction_id,
                    'screenshot_path' => $paymentProof,
                    'status' => $paymentStatus,
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $order,
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
    // CUSTOMER ORDERS
    // =========================
    public function myOrders(Request $request)
    {
        return response()->json([
            'success' => true,

            'orders' => Order::with(
                'payment',
                'items.product',
                'items.shop'
            )
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get(),
        ]);
    }

    // =========================
    // ACTIVE ORDERS
    // =========================
    public function activeOrders(Request $request)
    {
        return response()->json([
            'success' => true,

            'orders' => Order::with(
                'payment',
                'items.product',
                'items.shop'
            )
            ->where('user_id', $request->user()->id)

            ->whereHas('items', function ($q) {
                $q->whereNotIn('status', [
                    'delivered',
                    'cancelled',
                ]);
            })

            ->latest()
            ->get(),
        ]);
    }

    // =========================
    // ORDER HISTORY
    // =========================
    public function orderHistory(Request $request)
    {
        return response()->json([
            'success' => true,

            'orders' => Order::with(
                'payment',
                'items.product',
                'items.shop'
            )
            ->where('user_id', $request->user()->id)

            ->whereDoesntHave('items', function ($q) {
                $q->whereNotIn('status', [
                    'delivered',
                    'cancelled',
                ]);
            })

            ->latest()
            ->get(),
        ]);
    }

    // =========================
    // CUSTOMER CANCEL ORDER
    // =========================
    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('items.product', 'payment')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {

            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized',
            ], 404);
        }

        if ($order->status !== 'pending') {

           return response()->json([
            'success' => false,
            'message' => 'Order cannot be cancelled now',
        ], 400);
        }

        $request->validate([
            'status' => 'required|in:cancelled',
        ]);

        // // =========================
        // // UPDATE ITEMS + RESTORE STOCK
        // // =========================
        foreach ($order->items as $item) {

            $item->update([
                'status' => 'cancelled',
            ]);
        }

        // =========================
        // UPDATE ORDER
        // =========================
        $order->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'order' => $order,
        ]);
    }

    // =========================
    // ADMIN ORDERS
    // =========================
    public function adminOrders(Request $request)
    {
        if (
            !$request->user()->hasAnyRole([
                'admin',
                'super_admin',
            ])
        ) {

            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,

            'orders' => Order::with([
                'user',
                'payment',
                'items.product',
                'items.shop',
            ])
            -> whereNotIn('status', [
                    'delivered',
                    'cancelled',
                ])
            ->latest()
            ->get(),
        ]);
    }


    // =========================
    // ADMIN ORDER HISTORY
    // =========================
    public function adminOrderHistory(Request $request)
    {
        if (
            !$request->user()->hasAnyRole([
                'admin',
                'super_admin',
            ])
        ) {

            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,

            'orders' => Order::with([
                'user',
                'payment',
                'items.product',
                'items.shop',
            ])
            -> whereIn('status', [
                    'delivered',
                    'cancelled',
                ])
            ->latest()
            ->get(),
        ]);
    }


    // =========================
    // ADMIN UPDATE ORDER STATUS
    // =========================
    // public function adminUpdateOrderStatus(Request $request, $id)
    // {
    //     if (
    //         !$request->user()->hasAnyRole([
    //             'admin',
    //             'super_admin',
    //         ])
    //     ) {

    //         return response()->json([
    //             'message' => 'Unauthorized',
    //         ], 403);
    //     }

    //     $order = Order::with('items')->findOrFail($id);

    //     $request->validate([
    //         'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
    //     ]);

    //     if (
    //         $order->payment_status !== 'paid' &&
    //         $request->status !== 'cancelled')
    //     {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Payment not approved yet'
    //         ], 400);
    //     }

    //     if ($request->status === 'cancelled') {

    //     foreach ($order->items as $item) {

    //     if ($item->status !== 'cancelled' && $item->product)
    //     {
    //         $item->product->increment(
    //             'stock',
    //             $item->quantity
    //         );
    //     }

    //     $item->update([
    //         'status' => 'cancelled',
    //     ]);
    //     }
    //     }

    //     $order->update([
    //         'status' => $request->status,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Order status updated',
    //         'order' => $order,
    //     ]);
    // }

    // =========================
    // ADMIN UPDATE ORDER ITEM
    // =========================
    public function adminUpdateOrderItemStatus(Request $request, $id)
    {
        if (
            !$request->user()->hasAnyRole([
                'admin',
                'super_admin',
            ])
        ) {

            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $item = OrderItem::with(
            'order',
            'product'
        )->findOrFail($id);

        // =========================
        // PAYMENT CHECK
        // =========================

        if ($item->order->payment_status !== 'paid' && $request->status !== 'cancelled') {

             return response()->json([
                'success' => false,
                'message' => 'Payment not approved yet'
            ], 400);
        }

        // =========================
        // UPDATE ITEM STATUS
        // =========================
        $item->update([
            'status' => $request->status,
        ]);

        // =========================
        // SYNC ORDER STATUS
        // =========================
        $order = $item->order;

        $statuses = $order->items()->pluck('status');

        if ($statuses->every(fn($s) => $s == 'delivered')) {

            $order->status = 'delivered';

        } elseif ($statuses->every(fn($s) => $s == 'cancelled')) {

            $order->status = 'cancelled';

        } elseif ($statuses->contains('processing')) {

            $order->status = 'processing';

        } elseif ($statuses->contains('shipped')) {

            $order->status = 'shipped';

        } else {

            $order->status = 'pending';
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order item updated',

            'item' => $item,

            'order_status' => $order->status,
        ]);
    }

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
    public function updatePaymentStatus(Request $request,$id)
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
        'rejection_reason' =>'required_if:payment_status,rejected|string|max:1000',
        ]);

        DB::beginTransaction();
        try {

        // =========================
        // GET PAYMENT
        // =========================
        $payment = Payment::with([
            'order.items.product'
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
        if (in_array($payment->status, ['paid', 'rejected']))
        {

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
