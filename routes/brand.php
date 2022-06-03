<?php

use App\Http\Controllers\v1\brand\BrandController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::resource('/brands', BrandController::class);

