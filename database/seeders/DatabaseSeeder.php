<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::factory(3)->create();
        \App\Models\Earning::factory(5)->create();
        \App\Models\Expense::factory(20)->create();
    }
}
