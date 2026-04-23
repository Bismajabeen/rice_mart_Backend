<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\RiceCategoryController;


Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/shops',               [ShopController::class, 'create']);
    Route::get('/shops/my-shop',        [ShopController::class, 'myShop']);
    Route::post('/shops/{id}',          [ShopController::class, 'update']);   // POST + _method=PUT
    Route::delete('/shops/{id}',        [ShopController::class, 'destroy']);
});
