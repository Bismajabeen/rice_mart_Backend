<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class DashboardController extends Controller
{
    // =========================
    // CUSTOMER DASHBOARD
    // =========================
    public function customerDashboard(Request $request)
    {
        $user = $request->user();

        $totalOrders = Order::where('user_id', $user->id)->count();

        $activeOrders = Order::where('user_id', $user->id)
            ->where('status', '!=', 'delivered')
            ->count();

        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_orders' => $totalOrders,
                'active_orders' => $activeOrders,
                'completed_orders' => $completedOrders,
            ]
        ]);
    }

    // =========================
    // SELLER DASHBOARD
    // =========================
    public function sellerDashboard(Request $request)
    {
        $user = $request->user();

        $shop = $user->shop;

        if (!$shop) {
            return response()->json([
                'success' => false,
                'message' => 'Shop not found'
            ], 404);
        }

        $totalProducts = Product::where('shop_id', $shop->id)->count();

        $activeProducts = Product::where('shop_id', $shop->id)
            ->where('stock', '>', 0)
            ->count();

        $totalOrders = OrderItem::where('shop_id', $shop->id)
            ->distinct('order_id')
            ->count('order_id');

        $pendingOrders = OrderItem::where('shop_id', $shop->id)
            ->where('status', 'pending')
            ->count();

        $processingOrders = OrderItem::where('shop_id', $shop->id)
            ->where('status', 'processing')
            ->count();

        $deliveredOrders = OrderItem::where('shop_id', $shop->id)
            ->where('status', 'delivered')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,

                'total_orders' => $totalOrders,

                'pending_orders' => $pendingOrders,
                'processing_orders' => $processingOrders,
                'delivered_orders' => $deliveredOrders,
            ]
        ]);
    }

    // =========================
    // ADMIN DASHBOARD
    // =========================
    public function adminDashboard()
    {
        $totalUsers = User::count();

        $totalSellers = User::role('seller')->count();

        $totalCustomers = User::role('customer')->count();

        $totalShops = Shop::count();

        $totalOrders = Order::count();

        $totalRevenue = Order::sum('total_price');

        $activeProducts = Product::where('stock', '>', 0)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'total_sellers' => $totalSellers,
                'total_customers' => $totalCustomers,
                'total_shops' => $totalShops,
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'active_products' => $activeProducts,
            ]
        ]);
    }
}