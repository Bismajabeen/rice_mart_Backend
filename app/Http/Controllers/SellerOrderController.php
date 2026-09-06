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

        $items = OrderItem::with([
            'order.user',
            'order.payment',
            'order.items',
            'product',
            'shop'
        ])
        ->where('shop_id', $shop->id)

        // fetch only paid orders, so sellers won't see unpaid orders
        ->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })

        ->latest()
        ->get();

        // =========================
        // ATTACH THIS SHOP'S SHARE OF THE DELIVERY CHARGE
        // order.delivery_charge is the TOTAL across all shops in the
        // order (see OrderController::checkout). Each shop's actual
        // portion is always delivery_charge / distinct shop count.
        // =========================

        foreach ($items as $item) {
            $shopCount = $item->order->items->pluck('shop_id')->unique()->count();
                
            $item->order->shop_delivery_charge = $shopCount > 0
                ? round($item->order->delivery_charge / $shopCount, 2)
                : $item->order->delivery_charge;

        }

        return response()->json([
            'success' => true,
            'orders' => $items
        ]);
    }

    // =========================
    // SELLER UPDATE ORDER ITEM
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
            'status' => 'required|in:processing,shipped,delivered'
        ]);

        $item = OrderItem::with([
            'order',
            'order.items'
        ])
        ->where('id', $id)
        ->where('shop_id', $shop->id)
        ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }

        // =========================
        // PAYMENT MUST BE APPROVED
        // =========================
        if ($item->order->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment not approved yet'
            ], 400);
        }

        // =========================
        // PREVENT CHANGES AFTER DELIVERY
        // =========================
        if ($item->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Delivered order cannot be changed'
            ], 400);
        }

        // =========================
        // UPDATE ITEM STATUS
        // =========================
        $item->update([
            'status' => $request->status
        ]);

        // =========================
        // SYNC MAIN ORDER STATUS
        // =========================
        $order = $item->order;

       $statuses = $order->items()->pluck('status');

        if ($statuses->every(fn ($s) => $s === 'delivered')) {

            $order->status = 'delivered';

        } elseif ($statuses->every(fn ($s) => $s === 'cancelled')) {
            
            $order->status = 'cancelled';

        } elseif ($statuses->contains('shipped')) {

            $order->status = 'shipped';

        } elseif ($statuses->contains('processing')) {

            $order->status = 'processing';

        } else {

            $order->status = 'pending';
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order item status updated successfully',
            'item' => $item->fresh(),
            'order_status' => $order->status
        ]);
    }
}