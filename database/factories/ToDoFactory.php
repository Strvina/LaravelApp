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
            'task' => $this->faker->sentence(4),
            'status' => 'pending',
            'priority' => $this->faker->randomElement(['low','medium','high']),
            'user_id' => User::factory(),
            'is_recurring' => $this->faker->boolean(),
            'recurrence' => $this->faker->randomElement([null, 'daily','weekly','monthly']),
            'last_generated_at' => null,
        ];
    }
}
