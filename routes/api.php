<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/customers/{email}/orders', [OrderController::class, 'history']);
Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
