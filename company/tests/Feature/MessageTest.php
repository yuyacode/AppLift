<?php

namespace Tests\Feature;

use App\Models\MessageApiCredential;
use App\Models\User;
use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class MessageTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_get_access_token_returns_500_when_no_token()
    {
        $user = User::factory()->create();

        MessageApiCredential::factory()->for($user)->create([
            'access_token' => null,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson(route('message.access-token.get'));

        $response->assertStatus(500)
            ->assertJson(['message' => 'failed to get access token',]);
    }

    public function test_get_access_token_successfully()
    {
        $user = User::factory()->create();

        MessageApiCredential::factory()->for($user)->create([
            'access_token' => 'test_access_token',
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson(route('message.access-token.get'));

        $response->assertOk()
            ->assertJson(['access_token' => 'test_access_token',]);
    }
}
