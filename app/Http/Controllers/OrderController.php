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

class OrderController extends Controller
{
    // =========================
    // CHECKOUT (CUSTOMER)
    // =========================

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

         $deliveryChargePerShop = (float) $city->courierCharge->charge;

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

          // =========================
          // CREATE ORDER (delivery_charge/total_price finalized below,
          // once we know how many distinct shops are in the cart)
          // =========================
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

             OrderItem::create([
                 'order_id' => $order->id,
                 'shop_id' => $product->shop_id,
                 'product_id' => $product->id,
                 'quantity' => $item['quantity'],
                 'price' => $product->price,
                 'status' => 'pending',
                ]);
            }

          // =========================
         // DELIVERY CHARGE — one charge PER DISTINCT SHOP, not per order
         // =========================
         $shopCount = count(array_unique($shopIds));
         $deliveryCharge = $deliveryChargePerShop * $shopCount;

         $order->update([
             'total_price' => $total + $deliveryCharge,
             'delivery_charge' => $deliveryCharge,
            ]);

         Payment::create([
             'order_id' => $order->id,
             'payment_method' => $request->payment_method,
             'payment_type' => 'manual',
             'amount' => $total + $deliveryCharge,
             'transaction_id' => $request->transaction_id,
             'screenshot_path' => $paymentProof,
             'status' => $paymentStatus,
            ]);

         DB::commit();

         return response()->json([
             'success' => true,
             'message' => 'Order placed successfully',
             'order' => $order,
            ]);

     } catch (\Exception $e) {
         DB::rollBack();

         return response()->json([
             'success' => false,
             'message' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // CUSTOMER ORDERS
    // =========================
    public function myOrders(Request $request)
    {
        return response()->json([
            'success' => true,
            'orders' => Order::with('payment', 'items.product', 'items.shop')
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get(),
        ]);
    }

    // =========================
    // ACTIVE ORDERS
    // =========================
    public function activeOrders(Request $request)
    {
        return response()->json([
            'success' => true,
            'orders' => Order::with('payment', 'items.product', 'items.shop')
                ->where('user_id', $request->user()->id)
                ->whereHas('items', function ($q) {
                    $q->whereNotIn('status', ['delivered', 'cancelled']);
                })
                ->latest()
                ->get(),
        ]);
    }

    // =========================
    // ORDER HISTORY
    // =========================
    public function orderHistory(Request $request)
    {
        return response()->json([
            'success' => true,
            'orders' => Order::with('payment', 'items.product', 'items.shop')
                ->where('user_id', $request->user()->id)
                ->whereDoesntHave('items', function ($q) {
                    $q->whereNotIn('status', ['delivered', 'cancelled']);
                })
                ->latest()
                ->get(),
        ]);
    }

    // =========================
    // CUSTOMER CANCEL ORDER
    // =========================
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

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'order' => $order,
        ]);
    }

    // =========================
    // ADMIN ORDERS
    // =========================
    public function adminOrders(Request $request)
    {
        if (!$request->user()->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'orders' => Order::with(['user', 'payment', 'items.product', 'items.shop'])
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->latest()
                ->get(),
        ]);
    }

    // =========================
    // ADMIN ORDER HISTORY
    // =========================
    public function adminOrderHistory(Request $request)
    {
        if (!$request->user()->hasAnyRole(['admin', 'super_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'orders' => Order::with(['user', 'payment', 'items.product', 'items.shop'])
                ->whereIn('status', ['delivered', 'cancelled'])
                ->latest()
                ->get(),
        ]);
    }

    // =========================
    // ADMIN UPDATE ORDER ITEM STATUS
    // =========================
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

    // =========================
    // CUSTOMER CONFIRMS THEY RECEIVED AN ITEM
    // =========================
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

        // If every item from this shop, in this order, is now confirmed,
        // the payout for that shop becomes eligible for admin to pay out.
        $stillUnconfirmed = OrderItem::where('order_id', $item->order_id)
            ->where('shop_id', $item->shop_id)
            ->whereNull('customer_confirmed_at')
            ->exists();

        if (!$stillUnconfirmed) {
            SellerPayout::where('order_id', $item->order_id)
                ->where('shop_id', $item->shop_id)
                ->where('status', 'pending')
                ->update(['status' => 'ready']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thanks for confirming!',
            'item' => $item->fresh(),
        ]);
    }

    // =========================
    // SHARED HELPER: RECALCULATE ORDER STATUS FROM ITS ITEMS
    // =========================
    private function syncOrderStatus(Order $order): void
    {
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
    }
}