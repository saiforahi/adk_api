<?php

use App\Http\Controllers\v1\supplier\SupplierController;
use Illuminate\Support\Facades\Route;

Route::post('create', [SupplierController::class, '_store']);
Route::get('all', [SupplierController::class,'_all']);
Route::post('update/{supplier}', [SupplierController::class, '_update']);
Route::get('details/{supplier}', [SupplierController::class,'_details']);
Route::delete('delete/{supplier}',[SupplierController::class,'_delete']);




