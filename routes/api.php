<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RiceCategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerOrderController;

//
// =========================
// AUTH ROUTES
// =========================
// (Login / Register - no login required)
//

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


//
// =========================
// AUTH USER (Sanctum protected)
// =========================
// (who is logged in user)
//

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//
// =========================
// RICE CATEGORIES
// =========================
//

Route::get('/rice-categories', [RiceCategoryController::class, 'index']);
Route::get('/all-rice-categories', [RiceCategoryController::class, 'allCategories']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/rice-categories/{id}/status', [RiceCategoryController::class, 'updateStatus']);
});


//
// =========================
// SHOPS
// =========================
//

Route::middleware('auth:sanctum')->group(function () {

    // CREATE SHOP
    Route::post('/shops', [ShopController::class, 'store']);

    // UPDATE SHOP
    Route::put('/shops/{id}', [ShopController::class, 'update']);

    // DELETE SHOP
    Route::delete('/shops/{id}/delete', [ShopController::class, 'deleteShop']);

    // SELLER APPROVAL FLOW (ADMIN SIDE)
    Route::get('/pending-shops', [ShopController::class, 'pendingShops']);
    Route::get('/approved-shops', [ShopController::class, 'approvedShops']);

    // APPROVE SHOP
    Route::post('/shops/{id}/approve', [ShopController::class, 'approve']);

    // REJECT SHOP
    Route::delete('/shops/{id}', [ShopController::class, 'reject']);
});


//
// =========================
// PRODUCTS
// =========================
//

Route::middleware('auth:sanctum')->group(function () {

    // CREATE PRODUCT (rice item)
    Route::post('/products', [ProductController::class, 'store']);

    // UPDATE PRODUCT
    Route::put('/products/{id}', [ProductController::class, 'update']);

    // DELETE PRODUCT
    Route::delete('/products/{id}', [ProductController::class, 'delete']);
});


// PUBLIC PRODUCTS (NO LOGIN REQUIRED)
Route::get('/all-products', [ProductController::class, 'allProducts']);
Route::get('/shop-products/{shopId}', [ProductController::class, 'shopProducts']);


//
// =========================
// ORDERS
// =========================
//

Route::middleware('auth:sanctum')->group(function () {

    // PLACE ORDER (checkout)
    Route::post('/checkout', [OrderController::class, 'checkout']);

});

Route::middleware('auth:sanctum')->get(
    '/my-orders',
    [OrderController::class, 'myOrders']
);

// UPDATE ORDER STATUS
Route::middleware('auth:sanctum')->put(
    '/orders/{id}/status',
    [OrderController::class, 'updateStatus']
);

Route::middleware('auth:sanctum')->group(function(){

    // SELLER ORDERS
    Route::get('/seller-orders',
    [SellerOrderController::class,'sellerOrders']);

});