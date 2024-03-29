<?php

use App\Http\Controllers\v1\setting\CommissionController;
use Illuminate\Support\Facades\Route;

Route::get('all', [CommissionController::class,'_all']);
Route::get('admin/all', [CommissionController::class,'_all_admin']);
Route::post('dealer/update/{commission}', [CommissionController::class, '_update']);
Route::post('tycoon/update/{commission}', [CommissionController::class, '_tycoonUpdate']);




