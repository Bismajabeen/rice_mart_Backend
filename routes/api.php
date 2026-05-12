<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RiceCategoryController;

// =========================
// AUTH
// =========================

Route::post('/register',
[AuthController::class,'register']);

Route::post('/login',
[AuthController::class,'login']);

// =========================
// RICE CATEGORIES
// =========================

// ACTIVE CATEGORIES
Route::get('/rice-categories',
[RiceCategoryController::class,'index']);

// ALL CATEGORIES (ADMIN)
Route::get('/all-rice-categories',
[RiceCategoryController::class,'allCategories']);

// UPDATE CATEGORY STATUS
Route::middleware('auth:sanctum')->group(function(){

    Route::put('/rice-categories/{id}/status',
    [RiceCategoryController::class,'updateStatus']);
});

// =========================
// SHOPS
// =========================

Route::middleware('auth:sanctum')->group(function(){

    // CREATE SHOP
    Route::post('/shops',
    [ShopController::class,'store']);

    // UPDATE SHOP
    Route::put('/shops/{id}',
    [ShopController::class,'update']);

    // DELETE SHOP
    Route::delete('/shops/{id}/delete',
    [ShopController::class,'deleteShop']);

    // PENDING SHOPS
    Route::get('/pending-shops',
    [ShopController::class,'pendingShops']);

    // APPROVED SHOPS
    Route::get('/approved-shops',
    [ShopController::class,'approvedShops']);

    // APPROVE SHOP
    Route::post('/shops/{id}/approve',
    [ShopController::class,'approve']);

    // REJECT SHOP
    Route::delete('/shops/{id}',
    [ShopController::class,'reject']);
});

// =========================
// PRODUCTS
// =========================

Route::middleware('auth:sanctum')->group(function(){

    // ADD PRODUCT
    Route::post('/products',
    [ProductController::class,'store']);

    // UPDATE PRODUCT
    Route::put('/products/{id}',
    [ProductController::class,'update']);

    // DELETE PRODUCT
    Route::delete('/products/{id}',
    [ProductController::class,'delete']);
});

// =========================
// FETCH PRODUCTS
// =========================

// ALL PRODUCTS
Route::get('/all-products',
[ProductController::class,'allProducts']);

// SHOP PRODUCTS
Route::get('/shop-products/{shopId}',
[ProductController::class,'shopProducts']);