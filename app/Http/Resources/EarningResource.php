<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EarningResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => $this->user->name,
            'descrição' => $this->description,
            'data_do_pagamento' => date('d/m/Y', strtotime($this->payment_date)),
            'valor' => 'R$ '.number_format($this->value, 2, ',', '.'),
        ];
    }
}
