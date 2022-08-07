<?php

use App\Http\Controllers\v1\product\ProductController;
use App\Http\Controllers\v1\product\ProductGroupController;
use App\Http\Controllers\v1\product\ProductStockController;
use Illuminate\Support\Facades\Route;


Route::post('/product-groups/{product_group}', [ProductGroupController::class, 'update']);
Route::resource('/groups', ProductGroupController::class);

/**
 * Product & Product stocks Routes
 */
Route::post('/products/{product}', [ProductController::class, 'update']);
Route::resource('/list', ProductController::class);
Route::post('/product-stocks/{product}', [ProductStockController::class, 'update']);
Route::resource('/product-stocks', ProductStockController::class);
