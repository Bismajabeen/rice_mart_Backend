<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

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
            'city' => 'required|string|max:100',
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
            $paymentStatus = 'pending';

            // =========================
            // CREATE ORDER
            // =========================
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),

                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'city' => $request->city,
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
            // NOTE: This stays here (not in PaymentController) because it must
            // commit/rollback atomically with the order inside this same transaction.
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
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

        // =========================
        // UPDATE ITEMS + RESTORE STOCK
        // =========================
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
    public function adminUpdateOrderStatus(Request $request, $id)
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

        $this->syncOrderStatus($order);

        return response()->json([
            'success' => true,
            'message' => 'Order item updated',

            'item' => $item,

            'order_status' => $order->status,
        ]);
    }

    // =========================
    // SELLER ORDERS
    // =========================
    public function sellerOrders(Request $request)
    {
        if (!$request->user()->hasAnyRole(['seller'])) {

            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $shop = Shop::where('user_id', $request->user()->id)->first();

        if (!$shop) {

            return response()->json([
                'success' => false,
                'message' => 'No shop found for this account',
            ], 404);
        }

        // Only orders that contain at least one item from this seller's shop.
        // Items are scoped to this shop only, so a seller never sees another
        // shop's items even if they share the same multi-vendor order.
        $orders = Order::with([
                'user',
                'payment',
                'items' => function ($q) use ($shop) {
                    $q->where('shop_id', $shop->id)->with('product', 'shop');
                },
            ])
            ->whereHas('items', function ($q) use ($shop) {
                $q->where('shop_id', $shop->id);
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    // =========================
    // SELLER UPDATE ORDER ITEM
    // =========================
    public function sellerUpdateItemStatus(Request $request, $id)
    {
        if (!$request->user()->hasAnyRole(['seller'])) {

            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $shop = Shop::where('user_id', $request->user()->id)->first();

        if (!$shop) {

            return response()->json([
                'success' => false,
                'message' => 'No shop found for this account',
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $item = OrderItem::with('order', 'product')->findOrFail($id);

        // =========================
        // OWNERSHIP CHECK
        // =========================
        if ($item->shop_id !== $shop->id) {

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

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

        $this->syncOrderStatus($order);

        return response()->json([
            'success' => true,
            'message' => 'Order item updated',

            'item' => $item,

            'order_status' => $order->status,
        ]);
    }

    // =========================
    // SHARED HELPER: RECALCULATE ORDER STATUS FROM ITS ITEMS
    // =========================
    private function syncOrderStatus(Order $order): void
    {
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
    }
}
