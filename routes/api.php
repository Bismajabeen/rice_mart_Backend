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
use App\Http\Controllers\AdminController;
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

// CREATE SHOP
Route::middleware('role:customer')->group(function () {

    Route::post('/shops', [ShopController::class, 'store']);

});


// =========================
// ORDERS (ALL AUTH USERS)
// =========================

Route::post('/checkout', [OrderController::class, 'checkout']);

Route::get('/my-orders', [OrderController::class, 'myOrders']);



    //
    // =========================
    // SELLER ROUTES
    // =========================
    //

    Route::middleware('role:seller')->group(function () {

        //
        // SHOPS
        //

        // UPDATE SHOP
        Route::put('/shops/{id}', [ShopController::class, 'update']);

        // DELETE SHOP
        Route::delete('/shops/{id}/delete', [ShopController::class, 'deleteShop']);



        //
        // PRODUCTS
        //

        // CREATE PRODUCT
        Route::post('/products', [ProductController::class, 'store']);

        // UPDATE PRODUCT
        Route::put('/products/{id}', [ProductController::class, 'update']);

        // DELETE PRODUCT
        Route::delete('/products/{id}', [ProductController::class, 'delete']);



        //
        // SELLER ORDERS
        //

        // SELLER ORDERS
        Route::get(
            '/seller-orders',
            [SellerOrderController::class, 'sellerOrders']
        );

        // UPDATE SELLER ORDER ITEM STATUS
        Route::put(
            '/seller/order-item/{id}/status',
            [SellerOrderController::class, 'updateStatus']
        );



        //
        // ACTIVE + HISTORY
        //

        Route::get('/active-orders', [OrderController::class, 'activeOrders']);

        Route::get('/order-history', [OrderController::class, 'orderHistory']);
    });




    //
    // =========================
    // ADMIN + SUPER ADMIN
    // =========================
    //

    Route::middleware('role:admin|super-admin')->group(function () {

        //
        // RICE CATEGORY STATUS
        //

        Route::put(
            '/rice-categories/{id}/status',
            [RiceCategoryController::class, 'updateStatus']
        );

        Route::post(
             '/admin/create-seller',
              [ShopController::class, 'adminCreateSeller']
        );



        //
        // SHOPS
        //

        // PENDING SHOPS
        Route::get('/pending-shops', [ShopController::class, 'pendingShops']);

        // APPROVED SHOPS
        Route::get('/approved-shops', [ShopController::class, 'approvedShops']);

        // APPROVE SHOP
        Route::post('/shops/{id}/approve', [ShopController::class, 'approve']);

        // REJECT SHOP
        Route::delete('/shops/{id}', [ShopController::class, 'reject']);



        //
        // ADMIN ORDERS
        //

        Route::get('/admin/orders', [OrderController::class, 'adminOrders']);

        Route::put(
            '/admin/orders/{id}/status',
            [OrderController::class, 'adminUpdateOrderStatus']
        );

        // GENERAL ORDER STATUS UPDATE
        Route::put(
            '/orders/{id}/status',
            [OrderController::class, 'updateStatus']
        );
    });

});
      // routes for all dashboards 
Route::middleware('auth:sanctum')->group(function () {

    // CUSTOMER DASHBOARD
    Route::get('/customer/dashboard', [DashboardController::class, 'customerDashboard']);

    // SELLER DASHBOARD
    Route::get('/seller/dashboard', [DashboardController::class, 'sellerDashboard']);

    // ADMIN DASHBOARD
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard']);
});


    Route::middleware('auth:sanctum')->group(function () {

    // NOTIFICATIONS
    Route::get(
        '/notifications',
        [NotificationController::class, 'myNotifications']
    );

    Route::put(
        '/notifications/{id}/read',
        [NotificationController::class, 'markAsRead']
    );

});