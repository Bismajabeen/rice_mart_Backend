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
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
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

        $item->status = $request->status;
        $item->save();

        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            if ($item->product) {
                $item->product->stock += $item->quantity;
                $item->product->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'item' => $item
        ]);
    }
}