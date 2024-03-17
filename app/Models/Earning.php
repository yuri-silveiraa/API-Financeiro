<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'description',
        'payment_date',
        'value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
