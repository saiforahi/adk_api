<?php

use App\Http\Controllers\v1\dealer\DealerController;
use App\Http\Controllers\v1\dealer\WalletController;
use App\Http\Controllers\v1\product\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\product\ProductStockController;

Route::post('create', [DealerController::class, '_store']);
Route::get('all', [DealerController::class,'_all']);
Route::post('update/{dealer}', [DealerController::class, '_update']);
Route::get('details/{dealer}', [DealerController::class,'_details']);
Route::delete('delete/{dealer}',[DealerController::class,'_delete']);
Route::put('product/stock',[DealerController::class,'_product_stock_order'])->middleware('auth:dealer');
Route::put('wallet/update/product-balance',[DealerController::class,'_update_product_balance']);
Route::post('wallet/add-balance',[DealerController::class,'_update_product_balance']);


Route::middleware('auth:dealer')->group(function () {
    Route::group(['prefix' => 'product'], function () {
        Route::get('stockable/list', [ProductController::class,'stockable_product_list_dealer']);
        Route::get('/stock-orders/{type}', [ProductStockController::class,'product_stock_orders']);
        Route::get('/stock', [DealerController::class,'product_stocks']);
        Route::post('/stock-orders/update-status', [ProductStockController::class,'product_stock_order_status_update']);
        Route::group(['prefix' => 'courier-app'], function () {
            
        });
    });
    Route::group(['prefix' => 'wallet'], function () {
        Route::post('/topup-request', [WalletController::class,'submit_topup_request']);
    });
});