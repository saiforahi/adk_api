<?php

use App\Http\Controllers\v1\purchaseOrder\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::post('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update']);
Route::resource('purchase-orders', PurchaseOrderController::class);





