<?php

use App\Http\Controllers\v1\product\ProductController;
use App\Http\Controllers\v1\product\ProductGroupController;
use App\Http\Controllers\v1\product\ProductStockController;
use Illuminate\Support\Facades\Route;


Route::post('/product-groups/{product_group}', [ProductGroupController::class, 'update']);
Route::resource('/groups', ProductGroupController::class);
Route::get('/stockable-products', [ProductController::class,'_all_stockable_products']);
/**
 * Product & Product stocks Routes
 */
Route::post('/products/{product}', [ProductController::class, 'update']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::post('/product-stocks/{product}', [ProductStockController::class, 'update']);
Route::resource('/product-stocks', ProductStockController::class)->middleware('auth:admin');

// product list global api
Route::get('all', [ProductController::class, 'index']);

Route::get('details/{product}', [ProductController::class, 'show']);
Route::middleware(['auth:admin'])->group(function () {
    Route::delete('delete/{product}', [ProductController::class, 'destroy']);
    Route::post('create', [ProductController::class, 'store']);
    Route::post('update/{product}', [ProductController::class, 'update']);
});
