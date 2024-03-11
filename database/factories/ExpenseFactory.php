<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'description' => $this->faker->realText(30),
            'category' => $this->faker->realText(15),
            'payment_method' => $this->faker->randomElement(['C', 'D', 'P']),
            'payment_date' => $this->faker->randomElement([$this->faker->dateTimeThisMonth()]),
            'paid' => $this->faker->boolean(),
            'value' => $this->faker->numberBetween(0, 10000),
        ];
    }
}
