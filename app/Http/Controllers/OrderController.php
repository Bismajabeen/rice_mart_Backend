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
    // CREATE ORDER FROM CART
    // =========================
    public function checkout(Request $request)
    {
        $user = $request->user();

        $cartItems = $request->cart; 
        // cart must come from Flutter

        if (!$cartItems || count($cartItems) == 0) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        DB::beginTransaction();

        try {

            $total = 0;

            // 1. Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => 0,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // 2. Create Order Items
            foreach ($cartItems as $item) {

                $product = Product::find($item['id']);

                if (!$product) continue;

                $price = $product->price;
                $qty = $item['quantity'];

                $subtotal = $price * $qty;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'shop_id' => $product->shop_id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                ]);
            }

            // 3. Update total
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

            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // =========================
// GET USER ORDERS
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
// UPDATE ORDER STATUS
// =========================
public function updateStatus(Request $request, $id)
{
    // FIND ORDER
    $order = Order::find($id);

    // CHECK ORDER EXISTS
    if (!$order) {
        return response()->json([
            'message' => 'Order not found'
        ], 404);
    }

    // UPDATE STATUS
    $order->status = $request->status;

    $order->save();

    return response()->json([
        'success' => true,
        'message' => 'Order status updated successfully',
        'order' => $order
    ]);
}
}