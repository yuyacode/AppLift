<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessageApiCredential>
 */
class MessageApiCredentialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id'     => fake()->regexify('[A-Za-z0-9]{30}'),
            'client_secret' => fake()->sha256(),
            'access_token'  => fake()->sha256(),
            'refresh_token' => fake()->sha256(),
            'expires_at'    => fake()->dateTimeBetween('now', '+15 minutes'),
        ];
    }
}
