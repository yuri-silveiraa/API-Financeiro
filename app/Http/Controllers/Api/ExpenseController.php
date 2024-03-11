<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function getExpenses($user_id)
    {
        $expenses = Expense::where('user_id', $user_id)->get();

        return response()->json($expenses, 200);
    }

    public function createExpense($user_id)
    {

    }
}
