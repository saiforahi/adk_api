<?php

use App\Http\Controllers\v1\product\ProductController;
use App\Http\Controllers\v1\TycoonPanel\TycoonController;
use App\Http\Controllers\v1\TycoonPanel\OrderController;
use App\Http\Controllers\v1\TycoonPanel\CommissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('tycoon')->group(function () {
    Route::post('create', [TycoonController::class, '_store']);
    Route::get('all', [TycoonController::class,'_all']);
    Route::post('update/{dealer}', [TycoonController::class, '_update']);
    Route::get('details/{dealer}', [TycoonController::class,'_details']);
    Route::delete('delete/{dealer}',[TycoonController::class,'_delete']);
});

// top up requests
Route::group(['prefix' => 'wallet'], function () {
    Route::post('/topup-request', [TycoonController::class,'submit_topup_request']);
    Route::post('/add-balance', [TycoonController::class,'submit_topup_request']);
    Route::get('/topup-request/history', [TycoonController::class,'all_topup_request'])->middleware('auth:tycoon');
});


// product order
Route::middleware('auth:tycoon')->group(function () {
    Route::group(['prefix' => 'product'], function () {
        Route::get('stockable/list', [ProductController::class,'stockable_product_list_tycoon']);
        Route::get('/orders', [OrderController::class,'product_orders']);
        Route::post('/order/store', [OrderController::class,'_store_order']);
    });
    Route::group(['prefix' => 'commissions'], function () {
        Route::get('/history', [CommissionController::class,'_all']);
    });
});

