<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

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