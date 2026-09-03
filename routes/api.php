<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Api\RiceDetectionController;
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
Route::get('/admin/shops/pending',      [ShopController::class, 'pendingShops']);
Route::get('/admin/shops',              [ShopController::class, 'allShops']);
Route::put('/admin/shops/{id}/status',  [ShopController::class, 'updateStatus']);
Route::get('/seller/shop',              [ShopController::class, 'myShop']);
Route::get('/shops',                    [ShopController::class, 'index']);
Route::post('/shops',                   [ShopController::class, 'store']);
Route::get('/shops/{id}',               [ShopController::class, 'show']);
Route::put('/shops/{id}',               [ShopController::class, 'update']);

// ── Chat Routes ───────────────────────────────────────────────
Route::post('/chat/start',              [ChatController::class, 'startConversation']);
Route::get('/chat/{conversationId}',    [ChatController::class, 'getMessages']);
Route::post('/chat/send',               [ChatController::class, 'sendMessage']);
Route::get('/conversations',            [ChatController::class, 'getConversations']);

// ── Rice Detection Routes ────────────────────────────────────
Route::post('/rice/detect',             [RiceDetectionController::class, 'detect']);
Route::get('/rice/history',             [RiceDetectionController::class, 'history']);