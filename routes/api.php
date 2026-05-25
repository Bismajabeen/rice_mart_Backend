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

//
// =========================
// AUTH ROUTES
// =========================
//

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//
// =========================
// AUTH USER
// =========================
//

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//
// =========================
// PUBLIC RICE CATEGORIES
// =========================
//

Route::get('/rice-categories', [RiceCategoryController::class, 'index']);
Route::get('/all-rice-categories', [RiceCategoryController::class, 'allCategories']);

//
// =========================
// PUBLIC PRODUCTS
// =========================
//

Route::get('/all-products', [ProductController::class, 'allProducts']);
Route::get('/shop-products/{shopId}', [ProductController::class, 'shopProducts']);

//
// =========================
// AUTHENTICATED ROUTES
// =========================
//

Route::middleware('auth:sanctum')->group(function () {

    //
    // =========================
    // CUSTOMER ROUTES
    // =========================
    //

    Route::middleware('role:customer')->group(function () {

        // CREATE SHOP
        Route::post('/shops', [ShopController::class, 'store']);
    });

    //
    // =========================
    // ORDERS (ALL AUTH USERS)
    // =========================
    //

    Route::post('/checkout', [OrderController::class, 'checkout']);

    Route::get('/my-orders', [OrderController::class, 'myOrders']);

     // active-orders
    Route::get('/active-orders', [OrderController::class, 'activeOrders']);
    // /order-history
    Route::get('/order-history', [OrderController::class, 'orderHistory']);

    //
    // =========================
    // SELLER ROUTES
    // =========================
    //

    Route::middleware('role:seller')->group(function () {

        //
        // SHOPS
        //

        Route::put('/shops/{id}', [ShopController::class, 'update']);

        Route::delete('/shops/{id}/delete', [ShopController::class, 'deleteShop']);

        //
        // PRODUCTS
        //

        Route::post('/products', [ProductController::class, 'store']);

        Route::put('/products/{id}', [ProductController::class, 'update']);

        Route::delete('/products/{id}', [ProductController::class, 'delete']);

        //
        // SELLER ORDERS
        //

        Route::get(
            '/seller-orders',
            [SellerOrderController::class, 'sellerOrders']
        );

        Route::put(
            '/seller/order-item/{id}/status',
            [SellerOrderController::class, 'updateStatus']
        );
    });

    //
    // =========================
    // ADMIN + SUPER ADMIN
    // =========================
    //

    Route::middleware('role:admin|super_admin')->group(function () {

        // ADMIN CREATE SELLER

        Route::post(
            '/admin/create-seller',
            [ShopController::class, 'adminCreateSeller']
        );

        //
        // =========================
        // USERS MANAGEMENT
        // =========================

        // ALL USERS
        Route::get('/users', [UserController::class, 'index']);

        // ALL ROLES
        Route::get('/roles', [UserController::class, 'roles']);

        // CREATE USER
        Route::post('/users', [UserController::class, 'store']);

        // UPDATE USER
        Route::put('/users/{id}', [UserController::class, 'update']);

        // DELETE USER
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        //
        // =========================
        // ROLE MANAGEMENT
        // =========================
        //

        // ALL ROLES
        Route::get(
            '/roles-management',
            [RoleController::class, 'index']
        );

        // CREATE ROLE
        Route::post(
            '/roles-management',
            [RoleController::class, 'store']
        );

        // UPDATE ROLE
        Route::put(
            '/roles-management/{id}',
            [RoleController::class, 'update']
        );

        // DELETE ROLE
        Route::delete(
            '/roles-management/{id}',
            [RoleController::class, 'destroy']
        );

        //
        // =========================
        // PERMISSIONS MANAGEMENT
        // =========================
        //

        // DROPDOWN ROLES
        Route::get(
            '/permission-roles',
            [RoleController::class, 'getRoles']
        );

        // GET ALL PERMISSIONS
        Route::get(
            '/permissions',
            [PermissionController::class, 'getPermissions']
        );
        // GET ROLE PERMISSIONS
        Route::get(
           '/roles-management/{id}/permissions',
           [PermissionController::class, 'getRolePermissions']
        );

        // ASSIGN PERMISSIONS
        Route::post(
            '/assign-permissions',
            [PermissionController::class, 'assignPermissions']
        );

        //
        // =========================
        // RICE CATEGORY STATUS
        // =========================
        //

        Route::put(
            '/rice-categories/{id}/status',
            [RiceCategoryController::class, 'updateStatus']
        );

        //
        // =========================
        // SHOPS
        // =========================
        //

        Route::get('/pending-shops', [ShopController::class, 'pendingShops']);

        Route::get('/approved-shops', [ShopController::class, 'approvedShops']);

        Route::post('/shops/{id}/approve', [ShopController::class, 'approve']);

        Route::delete('/shops/{id}', [ShopController::class, 'reject']);

        //
        // =========================
        // ADMIN ORDERS
        // =========================
        //

        Route::get('/admin/orders', [OrderController::class, 'adminOrders']);

        Route::put(
            '/admin/order-item/{id}/status',
            [OrderController::class, 'adminUpdateOrderItemStatus']
        );

        Route::put(
            '/admin/orders/{id}/status',
            [OrderController::class, 'adminUpdateOrderStatus']
        );

        Route::put(
            '/orders/{id}/status',
            [OrderController::class, 'updateStatus']
        );
    });
});

//
// =========================
// DASHBOARDS
// =========================
//

Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/customer/dashboard',
        [DashboardController::class, 'customerDashboard']
    );

    Route::get(
        '/seller/dashboard',
        [DashboardController::class, 'sellerDashboard']
    );

    Route::get(
        '/admin/dashboard',
        [DashboardController::class, 'adminDashboard']
    );
});

//
// =========================
// NOTIFICATIONS
// =========================
//

Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/notifications',
        [NotificationController::class, 'myNotifications']
    );

    Route::put(
        '/notifications/{id}/read',
        [NotificationController::class, 'markAsRead']
    );
});