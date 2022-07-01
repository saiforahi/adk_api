<?php

use App\Http\Controllers\v1\dashboard\DashboardController;
use Illuminate\Support\Facades\Route;


Route::get('/get-data', [DashboardController::class, 'index']);

