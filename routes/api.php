<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
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