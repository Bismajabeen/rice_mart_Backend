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

        $orders = Order::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_orders' => $orders->count(),
                'active_orders' => $orders->where('status', '!=', 'delivered')->count(),
                'completed_orders' => $orders->where('status', 'delivered')->count(),
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

        $products = Product::where('shop_id', $shop->id)->get();
        $orderItems = OrderItem::where('shop_id', $shop->id)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_products' => $products->count(),
                'active_products' => $products->where('stock', '>', 0)->count(),

                'total_orders' => $orderItems->groupBy('order_id')->count(),

                'pending_orders' => $orderItems->where('status', 'pending')->count(),
                'processing_orders' => $orderItems->where('status', 'processing')->count(),
                'delivered_orders' => $orderItems->where('status', 'delivered')->count(),
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