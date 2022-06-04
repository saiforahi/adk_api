<?php

use App\Http\Controllers\v1\brand\BrandController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/brands/{brand}', [BrandController::class, 'update']);
Route::resource('/brands', BrandController::class);

