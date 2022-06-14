<?php

use App\Http\Controllers\v1\warehouse\WarehouseController;
use Illuminate\Support\Facades\Route;


Route::post('/warehouses/{warehouse}', [WarehouseController::class, 'update']);
Route::resource('/warehouses', WarehouseController::class);

