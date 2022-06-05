<?php

use App\Http\Controllers\v1\product\ProductGroupController;
use Illuminate\Support\Facades\Route;


Route::post('/product-groups/{product_group}', [ProductGroupController::class, 'update']);
Route::resource('/product-groups', ProductGroupController::class);

