<?php

namespace App\Models;

use App\Filters\ExpenseFilter;
use App\Http\Resources\ExpenseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Expense extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'user_id',
        'description',
        'category',
        'payment_method',
        'payment_date',
        'paid',
        'value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function filter(Request $req)
    {
        $queryFilter = (new ExpenseFilter)->filter($req);

        if(empty($queryFilter)) {
            return ExpenseResource::collection(Expense::with('user')->get());
        }

        $data = Expense::with('user');

        if(! empty($queryFilter['whereIn'])) {
            foreach($queryFilter['whereIn'] as $value) {
                $data->whereIn($value[0], $value[1]);
            }
        }

        $resource = $data->where($queryFilter['where'])->get();

        return ExpenseResource::collection($resource);
    }
}
