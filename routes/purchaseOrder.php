<?php

use App\Http\Controllers\v1\purchaseOrder\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::post('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->middleware('auth:admin');
Route::resource('purchase-orders', PurchaseOrderController::class)->middleware('auth:admin');





