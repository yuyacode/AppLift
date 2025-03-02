<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReviewAnswer>
 */
class ReviewAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'review_id'      => fake()->randomNumber(),
            'review_item_id' => fake()->randomNumber(),
            'score'          => fake()->numberBetween(0, 100),
            'answer'         => fake()->sentence(),
        ];
    }
}
