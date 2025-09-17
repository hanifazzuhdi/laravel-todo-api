<?php

namespace Database\Factories;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'        => fake()->sentence(),
            'assignee'     => fake()->firstName(),
            'status'       => fake()->randomElement(Todo::STATUSES),
            'priority'     => fake()->randomElement(Todo::PRIORITIES),
            'due_date'     => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'time_tracked' => fake()->numberBetween(0, 480),
        ];
    }
}
