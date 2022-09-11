<?php

use App\Http\Controllers\v1\balanceTransfer\BalanceTransferController;
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
    Route::group(['prefix' => 'admin'], function () {
        Route::get('transfer-history', [BalanceTransferController::class, 'index']);
        Route::post('/add-balance', [BalanceTransferController::class, 'add_balance']);
    });
});

Route::middleware(['auth:tycoon'])->group(function () {
    Route::group(['prefix' => 'tycoon'], function () {
        Route::get('transfer-history', [BalanceTransferController::class, 'tycoonHistory']);
        Route::post('/add-balance', [BalanceTransferController::class, 'tycoonAddBalance']);
    });
});