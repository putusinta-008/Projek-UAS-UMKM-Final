<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
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