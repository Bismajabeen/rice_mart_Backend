<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// ── Auth Routes ───────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/logout',   [AuthController::class, 'logout']);
Route::get('/me',        [AuthController::class, 'me']);

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
Route::get('/shops',                      [ShopController::class, 'index']);
Route::get('/shops/{id}',                 [ShopController::class, 'show']);
Route::post('/shops',                     [ShopController::class, 'store']);
Route::post('/shops/{id}',                [ShopController::class, 'update']);
Route::get('/seller/shop',                [ShopController::class, 'myShop']);
Route::get('/admin/shops',                [ShopController::class, 'allShops']);
Route::put('/admin/shops/{id}/status',    [ShopController::class, 'updateStatus']);