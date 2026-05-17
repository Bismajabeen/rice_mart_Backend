<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // =========================
    // CHECKOUT
    // =========================
    public function checkout(Request $request)
    {
        $user = $request->user();
        $cartItems = $request->cart;

        if (!$cartItems || count($cartItems) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        DB::beginTransaction();

        try {

            $total = 0;

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => 0,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            foreach ($cartItems as $item) {

                $product = Product::find($item['id']);
                if (!$product) continue;

                $qty = $item['quantity'];

                if ($qty > $product->stock) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => $product->name . ' stock not available'
                    ], 400);
                }

                $subtotal = $product->price * $qty;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'shop_id' => $product->shop_id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->price,
                    'status' => 'pending',
                ]);

                $product->stock -= $qty;
                $product->save();
            }

            $order->update([
                'total_price' => $total
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // =========================
    // MY ORDERS (BUYER)
    // =========================
    public function myOrders(Request $request)
    {
        $user = $request->user();

        $orders = Order::with('items.product', 'items.shop')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    // =========================
    // UPDATE ORDER STATUS (MASTER FIX)
    // =========================
    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $status = $request->status;

        // update MASTER order status
        $order->status = $status;
        $order->save();

        // OPTIONAL: sync all items too
        foreach ($order->items as $item) {
            $item->status = $status;
            $item->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }

   // =========================
// ACTIVE ORDERS
// =========================
public function activeOrders(Request $request)
{
    $user = $request->user();

    $orders = Order::with('items.product', 'items.shop')
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    $filtered = $orders->filter(function ($order) {

        $items = $order->items;

        // ACTIVE if ANY item is NOT finished
        return $items->contains(function ($item) {
            return !in_array($item->status, ['delivered', 'cancelled']);
        });
    });

    return response()->json([
        'success' => true,
        'orders' => $filtered->values()
    ]);
}

// =========================
// ORDER HISTORY
// =========================
public function orderHistory(Request $request)
{
    $user = $request->user();

    $orders = Order::with('items.product', 'items.shop')
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    $filtered = $orders->filter(function ($order) {

        $items = $order->items;

        // HISTORY if ALL items are finished
        return $items->every(function ($item) {
            return in_array($item->status, ['delivered', 'cancelled']);
        });
    });

    return response()->json([
        'success' => true,
        'orders' => $filtered->values()
    ]);
}

// ADMIN: GET ALL ORDERS
public function adminOrders()
{
    $orders = Order::with([
        'user',
        'items.product',
        'items.shop'
    ])
    ->latest()
    ->get();

    return response()->json([
        'success' => true,
        'orders' => $orders
    ]);
}
// ADD ADMIN STATUS UPDATE FUNCTION
public function adminUpdateOrderStatus(Request $request, $id)
{
    $order = Order::with('items')->find($id);

    if (!$order) {
        return response()->json([
            'success' => false,
            'message' => 'Order not found'
        ], 404);
    }

    $status = $request->status;

    // VALID STATUSES (safety)
    $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    if (!in_array($status, $allowed)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid status'
        ], 400);
    }

    // UPDATE MASTER ORDER
    $order->status = $status;
    $order->save();

    // SYNC ALL ITEMS
    foreach ($order->items as $item) {
        $item->status = $status;
        $item->save();
    }

    return response()->json([
        'success' => true,
        'message' => 'Order status updated by admin',
        'order' => $order
    ]);
}

}