<?php

namespace Database\Factories;

use App\Models\ToDo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ToDoFactory extends Factory
{
    protected $model = ToDo::class;

    public function definition(): array
    {
        return [
            'task' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'user_id' => User::factory(),
            'is_recurring' => false,
            'recurrence' => null,
            'last_generated_at' => null,
        ];
    }
}
