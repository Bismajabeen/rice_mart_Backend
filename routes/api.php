<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// ── Auth Routes ───────────────────────────────────────────────
Route::post('/register',    [AuthController::class, 'register']);
Route::post('/verify-otp',  [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp',  [AuthController::class, 'resendOtp']);
Route::post('/login',       [AuthController::class, 'login']);
Route::post('/logout',      [AuthController::class, 'logout']);
Route::get('/me',           [AuthController::class, 'me']);

// ── Product Routes ────────────────────────────────────────────
Route::get('/products',                   [ProductController::class, 'index']);
Route::get('/products/{id}',              [ProductController::class, 'show']);
Route::post('/products',                  [ProductController::class, 'store']);
Route::put('/products/{id}',              [ProductController::class, 'update']);
Route::delete('/products/{id}',           [ProductController::class, 'destroy']);
Route::get('/seller/products',            [ProductController::class, 'myProducts']);
Route::get('/admin/products',             [ProductController::class, 'allProducts']);
Route::put('/admin/products/{id}/status', [ProductController::class, 'updateStatus']);

// ── Shop Routes ───────────────────────────────────────────────
// NOTE: specific/static routes (pending, seller/shop, admin/*) must come
// BEFORE the '/shops/{id}' route, otherwise Laravel will treat words
// like "pending" as an {id} and call show() instead of pendingShops().

Route::get('/admin/shops/pending',      [ShopController::class, 'pendingShops']); // admin: pending shops
Route::get('/admin/shops',              [ShopController::class, 'allShops']);     // admin: all shops
Route::put('/admin/shops/{id}/status',  [ShopController::class, 'updateStatus']); // admin: approve/reject

Route::get('/seller/shop',              [ShopController::class, 'myShop']);       // seller: apni shop

Route::get('/shops',                    [ShopController::class, 'index']);        // public: approved shops list
Route::post('/shops',                   [ShopController::class, 'store']);        // seller: create shop
Route::get('/shops/{id}',               [ShopController::class, 'show']);         // public: single shop + products
Route::put('/shops/{id}',               [ShopController::class, 'update']);       // seller: update shop