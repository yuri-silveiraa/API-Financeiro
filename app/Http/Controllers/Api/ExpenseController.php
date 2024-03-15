<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Http\Resources\ExpenseResource;
use App\Models\Balance;
use App\Models\Expense;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    use HttpResponses;

    public function getExpenses()
    {
        return response()->json(ExpenseResource::collection(Expense::with('user')->get()), 200);
    }

    public function getExpensesToUser(User $user)
    {
        $expenses = ExpenseResource::collection(Expense::where('user_id', $user->id)->with('user')->get());
        $totalValue = 0;
        foreach ($expenses as $expense) {
            $totalValue += $expense->value;
        }

        return $this->response('Sucessfully', 200, [$expenses, 'totalValue' => $totalValue]);
    }

    public function createExpense(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'user_id' => 'required',
            'description' => 'nullable|max:50',
            'category' => 'required|max:20',
            'payment_method' => 'required|max:1',
            'payment_date' => 'required',
            'paid' => 'required|boolean',
            'value' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $created = Expense::create($validator->validated());

        if(! $created) {
            return $this->error('Something Wrong', 400);
        }

        $balance = Balance::where('user_id', $r->get('user_id'))->first();
        if($created->paid === true) {
            $balance->amount -= $created->value;
            $balance->save();
        }

        return $this->response('Sucessfully', 200, [new ExpenseResource($created->load('user')), new BalanceResource($balance)]);
    }

    public function updateExpense(Request $r, Expense $expense)
    {
        $validator = Validator::make($r->all(), [
            'description' => 'nullable|max:50',
            'category' => 'required|max:20',
            'payment_method' => 'required|max:1|in:'.implode(',', ['D', 'C', 'P']),
            'payment_date' => 'required',
            'paid' => 'required|boolean',
            'value' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $validated = $validator->validated();

        $paid = $expense->paid;
        $value = $expense->value;
        $diff = $expense->value - $r->value;

        $expense->update([
            'description' => $validated['description'],
            'category' => $validated['category'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'paid' => $validated['paid'],
            'value' => $validated['value'],
        ]);

        $balance = Balance::where('user_id', $expense->user_id)->first();
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

        return response()->json([new ExpenseResource($expense), new BalanceResource($balance)], 200);
    }

    public function deleleExpense($id)
    {
        $deletedExpense = Expense::destroy($id);

        return response()->json(['message' => 'Expense deleted'], 200);
    }
}
