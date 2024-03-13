<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function getExpenses($user_id)
    {
        $expenses = Expense::where('user_id', $user_id)->get();

        return response()->json($expenses, 200);
    }

    public function createExpense(Request $r, $user_id)
    {
        $user = User::Find($user_id);
        if (! $user) {
            return response()->json(['message' => 'Not Found User: '.$user_id], 404);
        }

        $expense = Expense::create($r->all());
        if (! $expense) {
            return response()->json(['message' => 'Could not create'], 400);
        }

        $balance = Balance::where('user_id', $user->id)->first();
        if($expense->paid === true) {
            $balance->amount -= $expense->value;
            $balance->save();
        }

        return response()->json([$expense, $balance], 200);
    }

    public function updateExpense(Request $r, $user_id, $id)
    {
        $user = User::Find($user_id);
        if (! $user) {
            return response()->json(['message' => 'Not Found User: '.$user_id], 404);
        }

        $expense = Expense::find($id);
        if (! $expense) {
            return response()->json(['message' => 'Not Found Expense: '.$id], 404);
        }

        $paid = $expense->paid;
        $value = $expense->value;
        $diff = $expense->value - $r->value;

        $expense->description = $r->description ?? $expense->description;
        $expense->category = $r->category ?? $expense->category;
        $expense->paid = $r->paid ?? $expense->paid;
        $expense->payment_date = $r->payment_date ?? $expense->payment_date;
        $expense->payment_method = $r->payment_method ?? $expense->payment_method;
        $expense->value = $r->value ?? $expense->value;
        $expense->save();

        $balance = Balance::where('user_id', $user->id)->first();

        if ($expense->paid === true) {
            if($expense->paid != $paid) {
                $balance->amount -= $value;
            } else {
                $balance->amount += $diff;
            }
        } elseif ($expense->paid != $paid && $expense->paid === false) {
            $balance->amount += $value;
        }

        $balance->save();

        return response()->json([$expense, $balance], 200);
    }
}
