<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3), // <— ovde je 'name', ne 'title'
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'type' => $this->faker->randomElement(['income','expense']),
            'date' => $this->faker->date(),
            'user_id' => User::factory(),
        ];
    }
}
