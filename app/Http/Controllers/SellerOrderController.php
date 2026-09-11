<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;

class SellerOrderController extends Controller
{
    // =========================
    // SELLER ORDERS LIST (grouped by order, not by item)
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
        // GROUP THIS SHOP'S ITEMS BY ORDER
        // Each group becomes ONE "order card" for the seller,
        // even if the customer bought several items from this shop.
        // =========================
        $grouped = $items->groupBy('order_id')->map(function ($shopItems) {
            $order = $shopItems->first()->order;

            // this shop's share of the delivery charge
            $shopCount = $order->items->pluck('shop_id')->unique()->count();
            $shopDeliveryCharge = $shopCount > 0
                ? round($order->delivery_charge / $shopCount, 2)
                : $order->delivery_charge;

            // overall status for THIS SHOP's items within the order
            $statuses = $shopItems->pluck('status');

            if ($statuses->every(fn ($s) => $s === 'delivered')) {
                $shopOrderStatus = 'delivered';
            } elseif ($statuses->every(fn ($s) => $s === 'cancelled')) {
                $shopOrderStatus = 'cancelled';
            } elseif ($statuses->contains('shipped')) {
                $shopOrderStatus = 'shipped';
            } elseif ($statuses->contains('processing')) {
                $shopOrderStatus = 'processing';
            } else {
                $shopOrderStatus = 'pending';
            }

            return [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'phone' => $order->phone,
                'address' => $order->address,
                'shop' => $shopItems->first()->shop,
                'shop_delivery_charge' => $shopDeliveryCharge,
                'status' => $shopOrderStatus,
                'items' => $shopItems->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'orders' => $grouped
        ]);
    }

    // =========================
    // SELLER UPDATE ORDER STATUS (updates ALL of this shop's items
    // in the order at once, keyed by order_id instead of item id)
    // =========================
    public function updateStatus(Request $request, $orderId)
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

        $items = OrderItem::with(['order', 'order.items'])
            ->where('order_id', $orderId)
            ->where('shop_id', $shop->id)
            ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order = $items->first()->order;

        // =========================
        // PAYMENT MUST BE APPROVED
        // =========================
        if ($order->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment not approved yet'
            ], 400);
        }

        // =========================
        // PREVENT CHANGES AFTER DELIVERY
        // =========================
        if ($items->contains(fn ($i) => $i->status === 'delivered')) {
            return response()->json([
                'success' => false,
                'message' => 'Delivered order cannot be changed'
            ], 400);
        }

        // =========================
        // UPDATE ALL OF THIS SHOP'S ITEMS IN THE ORDER
        // =========================
        foreach ($items as $item) {
            $item->update([
                'status' => $request->status
            ]);
        }

        // =========================
        // SYNC MAIN ORDER STATUS (across ALL shops in the order —
        // same logic as before, untouched)
        // =========================
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
            'message' => 'Order status updated successfully',
            'items' => $items->fresh(),
            'order_status' => $order->status
        ]);
    }
}