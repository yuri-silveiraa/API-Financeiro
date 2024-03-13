<?php

use App\Http\Controllers\Api\EarningController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserController::class, 'getUsers']);
Route::get('/user/{id}', [UserController::class, 'getUser']);
Route::post('/user/create', [UserController::class, 'createUser']);
Route::put('/user/{id}', [UserController::class, 'updateUser']);
Route::delete('/user/{id}', [UserController::class, 'deleteUser']);

Route::get('/expenses/{user_id}', [ExpenseController::class, 'getExpenses']);
Route::post('/expense/create/{user_id}', [ExpenseController::class, 'createExpense']);
Route::put('/expense/{user_id}/{id}', [ExpenseController::class, 'updateExpense']);
Route::delete('/expense/{user_id}/{id}', [ExpenseController::class, 'deleteExpense']);

Route::get('/earnings/{user_id}', [EarningController::class, 'getEarnings']);
Route::post('/earning/create', [EarningController::class, 'createEarning']);
Route::put('/earning/{user_id}/{id}', [EarningController::class, 'updateEarning']);
Route::delete('/earning/{user_id}/{id}', [EarningController::class, 'deleteEarning']);
