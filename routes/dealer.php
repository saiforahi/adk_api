<?php

use App\Http\Controllers\v1\dealer\DealerController;
use Illuminate\Support\Facades\Route;

Route::post('create', [DealerController::class, '_store']);
Route::get('all', [DealerController::class,'_all']);
Route::post('update/{dealer}', [DealerController::class, '_update']);
Route::get('details/{dealer}', [DealerController::class,'_details']);
Route::delete('delete/{dealer}',[DealerController::class,'_delete']);




