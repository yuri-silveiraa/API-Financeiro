<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Http\Resources\EarningResource;
use App\Models\Balance;
use App\Models\Earning;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EarningController extends Controller
{
    use HttpResponses;

    public function index()
    {
        return $this->response('Sucessfully', 200, EarningResource::collection(Earning::with('user')->get()));
    }

    public function show(string $id)
    {
        $earnings = EarningResource::collection(Earning::where('user_id', $id)->with('user')->get());
        $totalValue = 0;
        foreach ($earnings as $earning) {
            $totalValue += $earning->value;
        }

        return $this->response('Sucessfully', 200, [$earnings, 'totalValue' => 'R$'.number_format($totalValue, 2, ',', '.')]);
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => 'required',
            'description' => 'nullable|max:30',
            'payment_date' => 'nullable',
            'value' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $created = Earning::create($validator->validated());

        if(! $created) {
            return $this->error('Something Wrong', 400);
        }

        $balance = Balance::where('user_id', $req->get('user_id'))->first();
        $balance->amount += $created->value;
        $balance->save();

        return $this->response('Sucessfully', 200, [
            new EarningResource($created->load('user')),
            new BalanceResource($balance),
        ]);

    }

    public function update(Request $req, Earning $earning)
    {
        $valueBefore = $earning->value;

        $validator = Validator::make($req->all(), [
            'description' => 'nullable|max:50',
            'payment_date' => 'nullable',
            'value' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $validated = $validator->validated();

        $updated = $earning->update([
            'description' => $validated['description'] ?? $earning->description,
            'payment_date' => $validated['payment_date'] ?? $earning->payment_date,
            'value' => $validated['value'] ?? $earning->value,
        ]);
        if (! $updated) {
            return $this->error('Something wrong', 422);
        }

        $balance = Balance::find($earning->user_id);
        if($earning->value != $valueBefore) {
            $diff = $earning->value - $valueBefore;
            $balance->amount += $diff;
            $balance->save();
        }

        return $this->response('Updated Sucess', 200, [new EarningResource($earning), new BalanceResource($balance)]);
    }

    public function destroy(Earning $earning)
    {
        $value = $earning->value;

        $balance = Balance::where('user_id', $earning->user_id)->first();

        if (! $earning->delete()) {
            return $this->error('Failed to delete earning', 400);
        }

        $balance->amount -= $value;
        $balance->save();

        return $this->response('Expense Deleted', 200, new BalanceResource($balance));
    }
}
