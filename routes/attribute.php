<?php

use App\Http\Controllers\v1\attribute\AttributeController;
use Illuminate\Support\Facades\Route;

Route::post('attributes/{attribute}', [AttributeController::class, 'update']);;
Route::resource('attributes', AttributeController::class);




