<?php

use App\Http\Controllers\v1\admin\ProductStockOrderController;
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
        Route::get('stock-orders/requests', [ProductStockOrderController::class, 'product_stock_orders']);
        Route::post('/stock-orders/update-status', [ProductStockOrderController::class,'product_stock_order_status_update']);
    });
});