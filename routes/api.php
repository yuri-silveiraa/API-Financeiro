<?php

use App\Http\Controllers\Api\EarningController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/users', UserController::class);

Route::apiResource('/expenses', ExpenseController::class);

Route::apiResource('/earnings', EarningController::class);
