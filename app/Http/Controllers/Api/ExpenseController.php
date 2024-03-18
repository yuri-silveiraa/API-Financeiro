<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Http\Resources\ExpenseResource;
use App\Models\Balance;
use App\Models\Expense;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    use HttpResponses;

    public function index(Request $req)
    {
        $expenses = (new Expense())->filter($req);
        $totalValue = 0;
        foreach ($expenses as $expense) {
            $totalValue += $expense->value;
        }

        return $this->response('Sucessfully', 200, [$expenses, 'totalValue' => 'R$'.number_format($totalValue, 2, ',', '.')]);
    }

    public function store(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'user_id' => 'required',
            'description' => 'nullable|max:50',
            'category' => 'required|max:20',
            'payment_method' => 'required|max:1',
            'payment_date' => 'nullable',
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

        if($created->paid === true) {
            $balance = Balance::where('user_id', $req->get('user_id'))->first();
            $balance->amount -= $created->value;
            $balance->save();
        }

        return $this->response('Sucessfully', 200, [new ExpenseResource($created->load('user')), new BalanceResource($balance)]);
    }

    public function update(Request $req, Expense $expense)
    {
        $paidBefore = $expense->paid;
        $valueBefore = $expense->value;

        $validator = Validator::make($req->all(), [
            'description' => 'nullable|max:50',
            'category' => 'nullabe|max:20',
            'payment_method' => 'nullable|max:1|in:'.implode(',', ['D', 'C', 'P']),
            'payment_date' => 'nullable',
            'paid' => 'nullable|boolean',
            'value' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $validated = $validator->validated();

        $updated = $expense->update([
            'description' => $validated['description'] ?? $expense->description,
            'category' => $validated['category'] ?? $expense->category,
            'payment_method' => $validated['payment_method'] ?? $expense->payment_method,
            'payment_date' => $validated['payment_date'] ?? $expense->payment_date,
            'paid' => $validated['paid'] ?? $expense->paid,
            'value' => $validated['value'] ?? $expense->value,
        ]);
        if (! $updated) {
            return $this->error('Something wrong', 422);
        }

        $diff = $valueBefore - $expense->value;
        $balance = Balance::where('user_id', $expense->user_id)->first();

        switch(true) {
            case $expense->paid === true && $expense->paid != $paidBefore && $expense->value == $valueBefore:
                $balance->amount -= $valueBefore;
                break;

            case $expense->paid === true && $expense->paid !== $paidBefore && $expense->value != $valueBefore:
                $balance->amount -= $expense->value;
                break;

            case $expense->paid === true && $expense->paid === $paidBefore:
                $balance->amount += $diff;
                break;

            case $expense->paid === false && $expense->paid != $paidBefore:
                $balance->amount += $valueBefore;
                break;
        }
        $balance->save();

        return $this->response('Updated Sucess', 200, [new ExpenseResource($expense), new BalanceResource($balance)]);
    }

    public function destroy(Expense $expense)
    {
        $value = 0;

        if($expense->paid === true) {
            $value = $expense->value;
        }
        $balance = Balance::where('user_id', $expense->user_id)->first();

        if (! $expense->delete()) {
            return $this->error('Failed to delete expense', 400);
        }

        $balance->amount += $value;
        $balance->save();

        return $this->response('Expense Deleted', 200, new BalanceResource($balance));
    }
}
