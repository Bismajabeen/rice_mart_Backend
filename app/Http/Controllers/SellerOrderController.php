<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;

class SellerOrderController extends Controller
{
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
        ->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })

        ->latest()
        ->get();

        $grouped = $items->groupBy('order_id')->map(function ($shopItems) use ($shop) {
            $order = $shopItems->first()->order;

            
            $shopDeliveryCharge = \App\Models\OrderShopCharge::where('order_id', $order->id)
                ->where('shop_id', $shop->id)
                ->value('delivery_charge') ?? 0;

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

        if ($order->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment not approved yet'
            ], 400);
        }

        if ($items->contains(fn ($i) => $i->status === 'delivered')) {
            return response()->json([
                'success' => false,
                'message' => 'Delivered order cannot be changed'
            ], 400);
        }

        foreach ($items as $item) {
            $item->update([
                'status' => $request->status
            ]);
        }

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