<?php

use App\Http\Controllers\v1\pre_n_sub\PreSubController;
use Illuminate\Support\Facades\Route;

Route::post('create', [PreSubController::class, '_store']);
Route::get('all', [PreSubController::class,'_all']);
Route::post('update/{dealer}', [PreSubController::class, '_update']);
Route::get('details/{dealer}', [PreSubController::class,'_details']);
Route::delete('delete/{dealer}',[PreSubController::class,'_delete']);
Route::get('sub_dealer_types',[PreSubController::class,'_all_sub_dealer_types']);
Route::post('wallet/add-balance',[PreSubController::class,'_update_product_balance']);




