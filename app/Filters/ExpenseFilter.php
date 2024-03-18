<?php

namespace App\Filters;

class ExpenseFilter extends Filter
{
    protected array $allowedOperatorsFields = [
        'user_id' => ['eq'],
        'category' => ['eq', 'ne'],
        'payment_method' => ['eq', 'ne', 'in'],
        'payment_date' => ['gt', 'eq', 'lt', 'gte', 'lte', 'ne'],
        'paid' => ['eq', 'ne'],
        'value' => ['gt', 'eq', 'lt', 'gte', 'lte', 'ne'],
    ];
}
