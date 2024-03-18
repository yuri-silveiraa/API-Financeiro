<?php

use App\Http\Controllers\Api\EarningController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/users', UserController::class);
Route::middleware(['auth:sanctum', 'ability:all-index,all-create,all-update,all-delete'])->group(function () {
    Route::apiResource('/expenses', ExpenseController::class);
    Route::apiResource('/earnings', EarningController::class);
});

Route::post('/login', [AuthController::class, 'login']);
