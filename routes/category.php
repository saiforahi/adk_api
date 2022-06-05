<?php

use App\Http\Controllers\v1\category\CategoryController;
use App\Http\Controllers\v1\category\SubCategoryController;
use App\Http\Controllers\v1\category\SubSubCategoryController;
use Illuminate\Support\Facades\Route;

Route::post('categories/{category}', [CategoryController::class, 'update']);
Route::resource('categories', CategoryController::class);
Route::post('sub-categories/{sub_category}', [SubCategoryController::class, 'update']);
Route::resource('sub-categories', SubCategoryController::class);
Route::post('sub-sub-categories/{sub_sub_category}', [SubSubCategoryController::class, 'update']);
Route::resource('sub-sub-categories', SubSubCategoryController::class);




