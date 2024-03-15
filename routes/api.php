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

Route::get('/expenses', [ExpenseController::class, 'getExpenses']);
Route::get('/expenses/{user}', [ExpenseController::class, 'getExpense']);
Route::post('/expense/create/{user}', [ExpenseController::class, 'createExpense']);
Route::put('/expense/{user}/{expense}', [ExpenseController::class, 'updateExpense']);
Route::delete('/expense/{id}', [ExpenseController::class, 'deleteExpense']);

Route::get('/earnings/{user_id}', [EarningController::class, 'getEarnings']);
Route::post('/earning/create', [EarningController::class, 'createEarning']);
Route::put('/earning/{user_id}/{id}', [EarningController::class, 'updateEarning']);
Route::delete('/earning/{id}', [EarningController::class, 'deleteEarning']);
