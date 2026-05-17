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

        // =========================
        // GET SELLER SHOP
        // =========================
        $shop = $user->shop;

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        // =========================
        // GET ORDER ITEMS OF SHOP
        // =========================
        $orders = OrderItem::with([
            'order',
            'product',
        ])
        ->where('shop_id', $shop->id)
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    // =========================
    // UPDATE ITEM STATUS
    // =========================
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        // =========================
        // GET SELLER SHOP
        // =========================
        $shop = $user->shop;

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        // =========================
        // FIND ONLY SELLER ITEM
        // =========================
        $item = OrderItem::where('id', $id)
            ->where('shop_id', $shop->id)
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }

        // =========================
        // SAVE OLD STATUS
        // =========================
        $oldStatus = $item->status;

        // =========================
        // UPDATE STATUS
        // =========================
        $item->status = $request->status;

        $item->save();

        // =========================
        // RESTORE STOCK IF CANCELLED
        // =========================
        if (
            $request->status == 'cancelled'
            &&
            $oldStatus != 'cancelled'
        ) {

            $product = $item->product;

            if ($product) {

                $product->stock =
                    $product->stock + $item->quantity;

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