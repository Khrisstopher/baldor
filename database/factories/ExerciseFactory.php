<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'topic_id' => Topic::factory(),
            'statement' => 'Ejercicio de practica: '.fake()->sentence(),
            'answer' => (string) fake()->numberBetween(1, 20),
            'position' => 1,
        ];
    }
}
