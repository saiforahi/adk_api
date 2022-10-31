<?php

use App\Http\Controllers\v1\admin\BalanceTransferController;
use App\Http\Controllers\v1\admin\ProductStockOrderController;
use App\Http\Controllers\v1\admin\TopUpRequestController;
use App\Http\Controllers\v1\admin\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:admin'])->group(function () {
    Route::group(['prefix' => 'product'], function () {
        Route::get('stock', [ProductStockOrderController::class, 'admin_product_stocks']);
        Route::get('stock-orders/requests', [ProductStockOrderController::class, 'product_stock_orders']);
        Route::post('/stock-orders/update-status', [ProductStockOrderController::class,'product_stock_order_status_update']);
    });
    Route::group(['prefix' => 'topup-requests'], function () {
        Route::get('all/{type}', [TopUpRequestController::class, 'all_topup_requests']);
        Route::post('status/update', [TopUpRequestController::class, 'update_status']);
    });
    Route::group(['prefix' => 'withdraw'], function () {
        Route::get('requests', [WalletController::class, 'all_dealer_withdraw_requests']);
        Route::post('dealer/status/update', [WalletController::class, 'update_dealer_withdraw_request_status']);
    });
});