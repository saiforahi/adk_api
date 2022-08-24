<?php

use App\Http\Controllers\v1\TycoonPanel\TycoonController;
use Illuminate\Support\Facades\Route;

Route::prefix('tycoon')->group(function () {
    Route::post('create', [TycoonController::class, '_store']);
    Route::get('all', [TycoonController::class,'_all']);
    Route::post('update/{dealer}', [TycoonController::class, '_update']);
    Route::get('details/{dealer}', [TycoonController::class,'_details']);
    Route::delete('delete/{dealer}',[TycoonController::class,'_delete']);
});

// top up requests
Route::group(['prefix' => 'wallet'], function () {
    Route::post('/topup-request', [TycoonController::class,'submit_topup_request']);
});


