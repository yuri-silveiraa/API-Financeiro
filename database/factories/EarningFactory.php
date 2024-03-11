<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EarningFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'description' => fake()->realText(20),
            'payment_date' => fake()->dateTimeThisMonth(),
            'value' => fake()->numberBetween(0, 10000),
        ];
    }
}
