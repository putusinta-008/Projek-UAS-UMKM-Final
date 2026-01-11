<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    // AUTH
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */

    // USER & STAFF: lihat produk
    Route::get('/products', [ProductController::class, 'index']);

    // STAFF ONLY: kelola produk
    Route::middleware('role:staff')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */

    // USER: buat order api
    Route::post('/orders', [OrderController::class, 'store']);

    // USER: riwayat order sendiri
    Route::get('/my-orders', [OrderController::class, 'myOrders']);

    // STAFF: lihat semua order
    Route::middleware('role:staff')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
    });

    // USER: cancel order
    Route::delete('/orders/{id}', [OrderController::class, 'cancel']);
});