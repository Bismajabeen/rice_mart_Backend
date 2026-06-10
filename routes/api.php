<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RiceCategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
// For testing image upload
use App\Http\Controllers\TestImageController;

//
// =========================================
// AUTH ROUTES
// =========================================
//

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

//
// =========================================
// AUTHENTICATED USER
// =========================================
//

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

//
// =========================================
// PUBLIC RICE CATEGORIES
// =========================================
//

Route::get('/rice-categories', [
    RiceCategoryController::class,
    'index'
]);

Route::get('/all-rice-categories', [
    RiceCategoryController::class,
    'allCategories'
]);

//
// =========================================
// PUBLIC PRODUCTS
// =========================================
//

Route::get('/all-products', [
    ProductController::class,
    'allProducts'
]);

Route::get('/shop-products/{shopId}', [
    ProductController::class,
    'shopProducts'
]);

//
// =========================================
// AUTHENTICATED ROUTES
// =========================================
//

Route::middleware('auth:sanctum')->group(function () {

    //
    // =========================================
    // CUSTOMER FEATURES
    // =========================================
    //

    Route::post('/shops', [
        ShopController::class,
        'store'
    ])->middleware('permission:create shop');

    //
    // =========================================
    // ORDERS
    // =========================================
    //

    Route::post('/checkout', [
        OrderController::class,
        'checkout'
    ])->middleware('permission:checkout orders');

    Route::get('/my-orders', [
        OrderController::class,
        'myOrders'
    ])->middleware('permission:view own orders');

    Route::get('/active-orders', [
        OrderController::class,
        'activeOrders'
    ])->middleware('permission:view own orders');

    Route::get('/order-history', [
        OrderController::class,
        'orderHistory'
    ])->middleware('permission:view own orders');

    //
    // =========================================
    // SHOPS
    // =========================================
    //
    Route::middleware('auth:sanctum')->get('/my-shop', [ShopController::class, 'myShop']);

    Route::put('/shops/{id}', [
        ShopController::class,
        'update'
    ])->middleware('permission:update own shop');

    Route::delete('/shops/{id}/delete', [
        ShopController::class,
        'deleteShop'
    ])->middleware('permission:delete own shop');

    //
    // =========================================
    // PRODUCTS
    // =========================================
    //

    Route::post('/products', [
        ProductController::class,
        'store'
    ])->middleware('permission:create products');

    Route::put('/products/{id}', [
        ProductController::class,
        'update'
    ])->middleware('permission:update own products');

    Route::delete('/products/{id}', [
        ProductController::class,
        'delete'
    ])->middleware('permission:delete own products');

    //
    // =========================================
    // SELLER ORDERS
    // =========================================
    //

    Route::get('/seller-orders', [
        SellerOrderController::class,
        'sellerOrders'
    ])->middleware('permission:view shop orders');

    Route::put('/seller/order-item/{id}/status', [
        SellerOrderController::class,
        'updateStatus'
    ])->middleware('permission:update order status');

    //
    // =========================================
    // ADMIN CREATE SELLER
    // =========================================
    //

    Route::post('/admin/create-seller', [
        ShopController::class,
        'adminCreateSeller'
    ])->middleware('permission:create sellers');

    //
    // =========================================
    // USERS MANAGEMENT
    // =========================================
    //

    Route::get('/users', [
        UserController::class,
        'index'
    ])->middleware('permission:view users');

    Route::get('/roles', [
        UserController::class,
        'roles'
    ])->middleware('permission:view users');

    Route::post('/users', [
        UserController::class,
        'store'
    ])->middleware('permission:create users');

    Route::put('/users/{id}', [
        UserController::class,
        'update'
    ])->middleware('permission:update users');

    Route::delete('/users/{id}', [
        UserController::class,
        'destroy'
    ])->middleware('permission:delete users');

    //
    // =========================================
    // ROLE MANAGEMENT
    // =========================================
    //

    Route::get('/roles-management', [
        RoleController::class,
        'index'
    ])->middleware('permission:create roles');

    Route::post('/roles-management', [
        RoleController::class,
        'store'
    ])->middleware('permission:create roles');

    Route::put('/roles-management/{id}', [
        RoleController::class,
        'update'
    ])->middleware('permission:update roles');

    Route::delete('/roles-management/{id}', [
        RoleController::class,
        'destroy'
    ])->middleware('permission:delete roles');

    //
    // =========================================
    // PERMISSION MANAGEMENT
    // =========================================
    //

    Route::get('/permission-roles', [
        RoleController::class,
        'getRoles'
    ])->middleware('permission:assign permissions');

    Route::get('/permissions', [
        PermissionController::class,
        'getPermissions'
    ])->middleware('permission:assign permissions');

    Route::get('/roles-management/{id}/permissions', [
        PermissionController::class,
        'getRolePermissions'
    ])->middleware('permission:assign permissions');

    Route::post('/assign-permissions', [
        PermissionController::class,
        'assignPermissions'
    ])->middleware('permission:assign permissions');

    //
    // =========================================
    // CATEGORY MANAGEMENT
    // =========================================
    //

    Route::put('/rice-categories/{id}/status', [
        RiceCategoryController::class,
        'updateStatus'
    ])->middleware('permission:update categories');

    //
    // =========================================
    // SHOP APPROVAL MANAGEMENT
    // =========================================
    //

    Route::get('/pending-shops', [
        ShopController::class,
        'pendingShops'
    ])->middleware('permission:view all shops');

    Route::get('/approved-shops', [
        ShopController::class,
        'approvedShops'
    ])->middleware('permission:view all shops');

    Route::post('/shops/{id}/approve', [
        ShopController::class,
        'approve'
    ])->middleware('permission:approve shops');

    Route::delete('/shops/{id}', [
        ShopController::class,
        'reject'
    ])->middleware('permission:reject shops');

    //
    // =========================================
    // ADMIN ORDERS
    // =========================================
    //

    Route::get('/admin/orders', [
        OrderController::class,
        'adminOrders'
    ])->middleware('permission:view all orders');

    Route::get('/admin/order-history',[
        OrderController::class, 
        'adminOrderHistory'
    ])->middleware('permission:view all orders');

    Route::put('/admin/order-item/{id}/status', [
        OrderController::class,
        'adminUpdateOrderItemStatus'
    ])->middleware('permission:update any order status');

    // Route::put('/admin/orders/{id}/status', [
    //     OrderController::class,
    //     'adminUpdateOrderStatus'
    // ])->middleware('permission:update any order status');

    Route::put('/orders/{id}/status', [
        OrderController::class,
        'updateStatus'
    ])->middleware('permission:update any order status');
});

//
// =========================================
// DASHBOARDS
// =========================================
//

Route::middleware([
    'auth:sanctum',
    'permission:view customer dashboard'
])->get('/customer/dashboard', [
    DashboardController::class,
    'customerDashboard'
]);

Route::middleware([
    'auth:sanctum',
    'permission:view seller dashboard'
])->get('/seller/dashboard', [
    DashboardController::class,
    'sellerDashboard'
]);

Route::middleware([
    'auth:sanctum',
    'permission:view admin dashboard'
])->get('/admin/dashboard', [
    DashboardController::class,
    'adminDashboard'
]);

//
// =========================================
// NOTIFICATIONS
// =========================================
//

Route::middleware([
    'auth:sanctum',
    'permission:view own notifications'
])->group(function () {

    Route::get('/notifications', [
        NotificationController::class,
        'myNotifications'
    ]);

    Route::put('/notifications/{id}/read', [
        NotificationController::class,
        'markAsRead'
    ]);
});

// For testing image upload

Route::post('/test-image', [TestImageController::class, 'upload']);

Route::middleware(['auth:sanctum'])->group(function () {

    // =========================
    // ADMIN PAYMENT MANAGEMENT
    // =========================
    Route::get(
        '/admin/payments',
        [OrderController::class, 'adminPayments']
    );

    Route::put(
        '/admin/payments/{id}/status',
        [OrderController::class, 'updatePaymentStatus']
    );
});