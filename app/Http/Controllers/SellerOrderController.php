<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;

class SellerOrderController extends Controller
{
    // =========================
    // SELLER ORDERS LIST
    // =========================
    public function sellerOrders(Request $request)
    {
        $shop = $request->user()->shop()->first();

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        $items = OrderItem::with(['order.user', 'product', 'shop'])
            ->where('shop_id', $shop->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $items
        ]);
    }

    // =========================
    // UPDATE ORDER ITEM STATUS
    // =========================
    public function updateStatus(Request $request, $id)
    {
        $shop = $request->user()->shop()->first();

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        $request->validate([
              'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled',
              'payment_status' => 'nullable|in:pending,paid,rejected'
        ]);

        $item = OrderItem::where('id', $id)
            ->where('shop_id', $shop->id)
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found or unauthorized'
            ], 404);
        }

        $oldStatus = $item->status;
        $oldPaymentStatus = $item->order->payment_status;

        // Update status if provided
        if ($request->has('status')) {
            $item->status = $request->status;
            }
        // Update payment status if provided
        if ($request->has('payment_status')) {
            $item->order->payment_status = $request->payment_status;
            $item->order->save();
        }

        $item->save();

        if ($request->has('status') && $request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            if ($item->product) {
                $item->product->stock += $item->quantity;
                $item->product->save();
            }
        }

          // =========================
           // SYNC MAIN ORDER STATUS
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
            'message' => 'Order status updated successfully',
            'item' => $item
             ]);
        }
}