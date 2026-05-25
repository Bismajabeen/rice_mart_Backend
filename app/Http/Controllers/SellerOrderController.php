<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;

class SellerOrderController extends Controller
{
    // =========================
    // SELLER ORDERS
    // =========================
    public function sellerOrders(Request $request)
    {
        $user = $request->user();

        $shop = $user->shop;

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        // ONLY THIS SELLER'S ITEMS
        $items = OrderItem::with([
                'order.user',
                'product',
                'shop'
            ])
            ->where('shop_id', $shop->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $items
        ]);
    }

    // =========================
    // UPDATE ITEM STATUS
    // =========================
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        $shop = $user->shop;

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        // VALIDATE STATUS
        $request->validate([
            'status' => 'required'
        ]);

        $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($request->status, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status'
            ], 400);
        }

        // FIND ONLY SELLER'S ITEM
        $item = OrderItem::where('id', $id)
            ->where('shop_id', $shop->id)
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }

        $oldStatus = $item->status;

        // UPDATE STATUS
        $item->status = $request->status;
        $item->save();

        // RESTORE STOCK IF CANCELLED
        if (
            $request->status == 'cancelled'
            && $oldStatus != 'cancelled'
        ) {
            $product = $item->product;

            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'item' => $item
        ]);
    }
}