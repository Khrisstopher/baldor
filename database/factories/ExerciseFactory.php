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
        $answer = (string) fake()->numberBetween(1, 20);

        $options = [
            $answer,
            (string) fake()->numberBetween(21, 40),
            (string) fake()->numberBetween(41, 60),
            (string) fake()->numberBetween(61, 80),
        ];

        shuffle($options);

        return [
            'topic_id' => Topic::factory(),
            'statement' => 'Ejercicio de práctica: '.fake()->sentence(),
            'options' => $options,
            'answer' => $answer,
            'position' => 1,
        ];
    }
}
