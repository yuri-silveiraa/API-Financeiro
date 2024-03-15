<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    private array $types = ['C' => 'Credito', 'D' => 'Debito', 'P' => 'Pix'];

    public function toArray(Request $request): array
    {

        return [
            'user' => $this->user->name,
            'descrição' => $this->description,
            'categoria' => $this->category,
            'metodo_de_pagamento' => $this->types[$this->payment_method],
            'data_do_pagamento' => date('d/m/Y', strtotime($this->payment_date)),
            'pago' => $this->paid ? 'Pago' : 'Não pago',
            'valor' => 'R$ '.number_format($this->value, 2, ',', '.'),
        ];
    }
}
