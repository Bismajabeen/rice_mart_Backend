<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\SellerPayout;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;

class OrderController extends Controller
{

    public function checkout(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required|string',
            'payment_method' => 'required|in:easypaisa,jazzcash,card',
            'transaction_id' => 'required_if:payment_method,easypaisa,jazzcash|string|max:255',
            'cart' => 'required|array|min:1',
        ]);

        $cartItems = $request->cart;

        DB::beginTransaction();

        try {

            $city = \App\Models\City::with('courierCharge')->find($request->city_id);

            if (!$city || !$city->courierCharge) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Delivery is not available for the selected city',
                ], 400);
            }

            $deliveryBaseCharge = (float) $city->courierCharge->charge;
            $deliveryExtraPercent = (float) $city->courierCharge->extra_percent;
            $deliveryPerKgExtra = $deliveryBaseCharge * ($deliveryExtraPercent / 100);

            $paymentProof = null;

            if (in_array($request->payment_method, ['easypaisa', 'jazzcash'])) {

                $request->validate([
                    'payment_proof' => 'required|image|max:2048',
                ]);

                if ($request->hasFile('payment_proof')) {
                    $paymentProof = $request->file('payment_proof')->store('payments', 'public');
                }
            }

            $paymentStatus = 'pending';

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'city' => $city->name,
                'city_id' => $city->id,
                'address' => $request->address,
                'total_price' => 0,
                'delivery_charge' => 0,
                'status' => 'pending',
                'payment_status' => $paymentStatus,
            ]);

            $total = 0;
            $shopIds = [];
            $shopWeights = [];

            foreach ($cartItems as $item) {

                if (!isset($item['product_id'], $item['quantity'])) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid cart structure',
                    ], 400);
                }

                if ($item['quantity'] <= 0) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Quantity must be greater than 0',
                    ], 400);
                }

                $product = Product::where('id', $item['product_id'])
                    ->whereHas('shop', function ($q) {
                        $q->where('is_approved', 1);
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found or inactive shop',
                    ], 400);
                }

                if ($item['quantity'] > $product->stock) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => $product->name . ' stock not available',
                    ], 400);
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $shopIds[] = $product->shop_id;
                $shopWeights[$product->shop_id] = ($shopWeights[$product->shop_id] ?? 0) + $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'shop_id' => $product->shop_id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'status' => 'pending',
                ]);
            }

            $deliveryCharge = 0;

            foreach ($shopWeights as $shopId => $weight) {
                $extraKg = $weight > 1 ? $weight - 1 : 0;
                $shopCharge = $deliveryBaseCharge + ($deliveryPerKgExtra * $extraKg);
                $deliveryCharge += $shopCharge;

                \App\Models\OrderShopCharge::create([
                    'order_id' => $order->id,
                    'shop_id' => $shopId,
                    'weight_kg' => $weight,
                    'delivery_charge' => round($shopCharge, 2),
                ]);
            }
            

            $order->update([
                'total_price' => $total + $deliveryCharge,
                'delivery_charge' => $deliveryCharge,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'payment_type' => $request->payment_method === 'card' ? 'stripe' : 'manual',
                'amount' => $total + $deliveryCharge,
                'transaction_id' => $request->transaction_id,
                'screenshot_path' => $paymentProof,
                'status' => $paymentStatus,
            ]);

            
            if ($request->payment_method !== 'card') {
                NotificationService::sendToAdmins(
                    'payment_pending',
                    'New payment submitted',
                    'Order ' . $order->order_number . ' has a payment awaiting approval.',
                    ['order_id' => $order->id]
                );
            }   


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $order->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelUnpaidCardOrder(Request $request, $id)
    {
        $order = Order::with('payment', 'items')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized',
            ], 404);
        }

        $payment = $order->payment;

       

        if (!$payment ||$payment->payment_method !== 'card' ||$payment->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled',
            ], 400);
        }

        DB::beginTransaction();

        try {
            OrderItem::where('order_id', $order->id)->delete();
            $payment->delete();
            $order->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Unpaid card order cancelled successfully',
            ]);
        } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
    }


    public function myOrders(Request $request)
    {
        return response()->json([
            'success' => true,
            'orders' => Order::with('payment', 'items.product', 'items.shop', 'items.review')
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get(),
        ]);
    }

    public function activeOrders(Request $request)
    {
        return response()->json([
            'success' => true,
            'orders' => Order::with('payment', 'items.product', 'items.shop', 'items.review')
                ->where('user_id', $request->user()->id)
                ->where('payment_status', '!=', 'rejected')
                ->whereHas('items', function ($q) {
                    $q->whereNotIn('status', ['delivered', 'cancelled']);
                })
                ->latest()
                ->get(),
        ]);
    }

    public function orderHistory(Request $request)
    {
        return response()->json([
            'success' => true,
            'orders' => Order::with('payment', 'items.product', 'items.shop', 'items.review')
                ->where('user_id', $request->user()->id)
                ->where(function ($q) {
                    $q->where('payment_status', 'rejected')
                       ->orWhereDoesntHave('items', function ($q2) {
                           $q2->whereNotIn('status', ['delivered', 'cancelled']);
                       });
                })
                ->latest()
                ->get(),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('items.product', 'payment')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized',
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled now',
            ], 400);
        }

        $request->validate([
            'status' => 'required|in:cancelled',
        ]);

        foreach ($order->items as $item) {
            $item->update(['status' => 'cancelled']);
        }

        $order->update(['status' => 'cancelled']);

        Mail::to($order->user->email)->send(new OrderStatusUpdated($order));

        NotificationService::send(
            $order->user,
            'order_status',
            'Order cancelled',
            'Your order ' . $order->order_number . ' has been cancelled.',
            ['order_id' => $order->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'order' => $order,
        ]);
    }

    public function adminOrders(Request $request)
    {
        if (!$request->user()->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'orders' => Order::with(['user', 'payment', 'items.product', 'items.shop'])
                ->where('payment_status', 'paid')
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->latest()
                ->get(),
        ]);
    }

    public function adminOrderHistory(Request $request)
    {
        if (!$request->user()->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'orders' => Order::with(['user', 'payment', 'items.product', 'items.shop'])
            ->where(function ($q) {
                $q->whereIn('status', ['delivered', 'cancelled'])
                    ->orWhere('payment_status', 'rejected');
            })
                ->latest()
                ->get(),
        ]);
    }

    public function adminUpdateOrderItemStatus(Request $request, $id)
    {
        if (!$request->user()->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $item = OrderItem::with('order', 'product')->findOrFail($id);

        if ($item->order->payment_status !== 'paid' && $request->status !== 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Payment not approved yet'
            ], 400);
        }

        if ($item->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Delivered order cannot be changed'
            ], 400);
        }

        $item->update(['status' => $request->status]);

        $order = $item->order;
        $this->syncOrderStatus($order);

        return response()->json([
            'success' => true,
            'message' => 'Order item updated',
            'item' => $item->fresh(),
            'order_status' => $order->status,
        ]);
    }

    public function confirmReceived(Request $request, $id)
    {
        $item = OrderItem::with('order')
            ->where('id', $id)
            ->whereHas('order', fn ($q) => $q->where('user_id', $request->user()->id))
            ->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        if ($item->status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Item is not marked delivered yet',
            ], 400);
        }

        if ($item->customer_confirmed_at) {
            return response()->json(['success' => false, 'message' => 'Already confirmed'], 400);
        }

        $item->update(['customer_confirmed_at' => now()]);

        $stillUnconfirmed = OrderItem::where('order_id', $item->order_id)
            ->where('shop_id', $item->shop_id)
            ->whereNull('customer_confirmed_at')
            ->exists();

        if (!$stillUnconfirmed) {
            $updated = SellerPayout::where('order_id', $item->order_id)
                ->where('shop_id', $item->shop_id)
                ->where('status', 'pending')
                ->update(['status' => 'ready']);

            if ($updated) {
                $payout = SellerPayout::where('order_id', $item->order_id)
                    ->where('shop_id', $item->shop_id)
                    ->first();

                NotificationService::sendToAdmins(
                    'payment_release',
                    'Payout ready',
                    'Customer confirmed receipt for order ' . ($item->order->order_number ?? $item->order_id) . ' — ready to pay seller.',
                    ['order_id' => $item->order_id, 'payout_id' => $payout?->id]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Thanks for confirming!',
            'item' => $item->fresh(),
        ]);
    }

    private function syncOrderStatus(Order $order): void
    {
        $oldStatus = $order->status; 

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

       
        if ($order->status !== $oldStatus && $order->status !== 'pending') {

            Mail::to($order->user->email)->send(new OrderStatusUpdated($order));

            if ($order->status === 'processing') {
                NotificationService::send(
                    $order->user,
                    'order_status',
                    'Order processing',
                    'Your order ' . $order->order_number . ' is now being processed.',
                    ['order_id' => $order->id]
                );
            }

            if ($order->status === 'shipped') {
                NotificationService::send(
                    $order->user,
                    'order_status',
                    'Order shipped',
                    'Your order ' . $order->order_number . ' has been shipped.',
                    ['order_id' => $order->id]
                );
            }

            if ($order->status === 'delivered') {
                NotificationService::send(
                    $order->user,
                    'order_status',
                    'Order delivered',
                    'Your order ' . $order->order_number . ' has been delivered.',
                    ['order_id' => $order->id]
                );

                NotificationService::sendToAdmins(
                    'payment_release',
                    'Release payment to seller',
                    'Order ' . $order->order_number . ' was delivered. Release payment to the seller.',
                    ['order_id' => $order->id]
                );
            }

            if ($order->status === 'cancelled') {
                NotificationService::send(
                    $order->user,
                    'order_status',
                    'Order cancelled',
                    'Your order ' . $order->order_number . ' has been cancelled.',
                    ['order_id' => $order->id]
                );
            }
        }
    }

    public function confirmShopReceived(Request $request, $orderId, $shopId)
    {
     $items = OrderItem::where('order_id', $orderId)
        ->where('shop_id', $shopId)
        ->whereHas('order', fn ($q) => $q->where('user_id', $request->user()->id))
        ->get();

     if ($items->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'Items not found'], 404);
     }

     if ($items->contains(fn ($i) => $i->status !== 'delivered')) {
        return response()->json([
            'success' => false,
            'message' => 'All items must be delivered before confirming',
        ], 400);
     }

     if ($items->every(fn ($i) => $i->customer_confirmed_at !== null)) {
        return response()->json(['success' => false, 'message' => 'Already confirmed'], 400);
     }

     foreach ($items as $item) {
        if (!$item->customer_confirmed_at) {
            $item->update(['customer_confirmed_at' => now()]);
        }
     }

     $updated = SellerPayout::where('order_id', $orderId)
        ->where('shop_id', $shopId)
        ->where('status', 'pending')
        ->update(['status' => 'ready']);

     if ($updated) {
        $payout = SellerPayout::where('order_id', $orderId)
            ->where('shop_id', $shopId)
            ->first();

        $order = $items->first()->order;

        NotificationService::sendToAdmins(
            'payment_release',
            'Payout ready',
            'Customer confirmed receipt for order ' . ($order->order_number ?? $orderId) . ' — ready to pay seller.',
            ['order_id' => $orderId, 'payout_id' => $payout?->id]
        );
     }

     return response()->json([
        'success' => true,
        'message' => 'Thanks for confirming!',
        'items' => $items->fresh(),
        ]);
    }
}
