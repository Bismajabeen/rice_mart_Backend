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
}