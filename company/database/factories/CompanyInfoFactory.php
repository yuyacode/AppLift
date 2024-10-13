<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyInfo>
 */
class CompanyInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->prefecture(). fake()->city(). fake()->ward(). fake()->randomNumber(1). '-'. fake()->randomNumber(1). '-'. fake()->randomNumber(1),
            'homepage' => fake()->url(),
        ];
    }
}
