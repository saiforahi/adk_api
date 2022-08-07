<?php

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

Route::middleware('auth:admin')->group(function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::group(['prefix' => 'courier-app'], function () {
            Route::get('shipments', [CourierManagement::class, 'shipments']);
            Route::get('shipment-details/{merchant_id}/{shipment_status}', [CourierManagement::class, 'shipmentDetails']);
            Route::get('receive-shipment/{id}', [CourierManagement::class, 'receiveShipment']);
            Route::get('receive-all-parcel/{user}', [CourierManagement::class, 'receiveAllParcel']);
        });
    });
});