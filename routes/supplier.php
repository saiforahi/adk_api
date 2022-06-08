<?php

use App\Http\Controllers\v1\category\CategoryController;
use App\Http\Controllers\v1\category\SubCategoryController;
use App\Http\Controllers\v1\category\SubSubCategoryController;
use App\Http\Controllers\v1\supplier\SupplierController;
use Illuminate\Support\Facades\Route;

Route::post('supplier', [SupplierController::class, 'store']);
Route::get('supplier', [SupplierController::class,'_all']);
Route::post('supplier/{supplier}', [SupplierController::class, '_update']);
Route::get('supplier/{supplier}', [SupplierController::class,'_details']);
Route::delete('supplier/{supplier}',[SupplierController::class,'_delete']);




