<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceFactory extends Factory
{
    private static $userIdCounter = 0;

    public function definition(): array
    {
        self::$userIdCounter++;
        $userId = self::$userIdCounter;

        return [
            'user_id' => $userId,
            'amount' => $this->faker->numberBetween(0, 100000),
        ];
    }
}
