<?php

use App\Http\Controllers\v1\product\ProductController;
use App\Http\Controllers\v1\product\ProductGroupController;
use App\Http\Controllers\v1\product\ProductStockController;
use Illuminate\Support\Facades\Route;


Route::post('/product-groups/{product_group}', [ProductGroupController::class, 'update']);
Route::resource('/product-groups', ProductGroupController::class);

/**
 * Product & Product stocks Routes
 */
Route::post('/products/{product}', [ProductController::class, 'update']);
Route::resource('/products', ProductController::class);
Route::post('/product-stocks/{product}', [ProductStockController::class, 'update']);
Route::resource('/product-stocks', ProductStockController::class);
Route::get('/stock-orders-history', [ProductStockController::class,'product_stock_orders'])->middleware('auth:dealer');
Route::get('/stock-order/update-status', [ProductStockController::class,'product_stock_order_status_update'])->middleware('auth:dealer');
