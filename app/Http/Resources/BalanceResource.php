<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => $this->user->name,
            'amount' => 'R$'.number_format($this->amount, 2, ',', '.'),
        ];
    }
}
