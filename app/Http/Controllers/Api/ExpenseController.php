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

    public function getExpense(User $user)
    {
        return response()->json(ExpenseResource::collection(Expense::where('user_id', $user->id)->get()));
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

    public function updateExpense(Request $r, User $user, Expense $expense)
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

    public function deleleExpense($id)
    {
        $deletedExpense = Expense::destroy($id);

        return response()->json(['message' => 'Expense deleted'], 200);
    }
}
