<?php

use App\Http\Controllers\v1\category\CategoryController;
use App\Http\Controllers\v1\category\SubCategoryController;
use Illuminate\Support\Facades\Route;

Route::post('categories/{category}', [CategoryController::class, 'update']);
Route::resource('categories', CategoryController::class);
Route::post('sub-categories/{sub_category}', [SubCategoryController::class, 'update']);
Route::resource('sub-categories', SubCategoryController::class);




