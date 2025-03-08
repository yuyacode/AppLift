<?php

namespace Tests\Feature;

use App\Models\MessageApiCredential;
use App\Models\User;
use Illuminate\Support\Facades\Http;
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
            ->assertJson(['message' => 'failed to get access token']);
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
            ->assertJson(['access_token' => 'test_access_token']);
    }

    public function test_refresh_access_token_missing_credentials_in_db()
    {
        $user = User::factory()->create();

        MessageApiCredential::factory()->for($user)->create([
            'refresh_token' => null,
            'client_id'     => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('message.access-token.refresh'));

        $response->assertStatus(500)
            ->assertJson(['message' => 'failed to refresh access token']);
    }

    public function test_refresh_access_token_external_api_failure()
    {
        $user = User::factory()->create();

        MessageApiCredential::factory()->for($user)->create([
            'refresh_token' => 'test_refresh_token',
            'client_id'     => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ]);

        $fakeUrl = config('api.message_api_base_url_backend').'/token';

        Http::fake([
            $fakeUrl => Http::response([
                'message' => 'some error message',
                'detail'  => 'details...',
            ], 500),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('message.access-token.refresh'));

        $response->assertStatus(500)
            ->assertJson(['message' => 'failed to refresh access token']);
    }

    public function test_refresh_access_token_external_api_exception()
    {
        $user = User::factory()->create();

        MessageApiCredential::factory()->for($user)->create([
            'refresh_token' => 'test_refresh_token',
            'client_id'     => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ]);

        Http::fake(function () {
            throw new \Exception('Connection error');
        });

        $response = $this
            ->actingAs($user)
            ->postJson(route('message.access-token.refresh'));

        $response->assertStatus(500)
            ->assertJson(['message' => 'failed to refresh access token']);
    }

    public function test_refresh_access_token_success()
    {
        $user = User::factory()->create();

        MessageApiCredential::factory()->for($user)->create([
            'refresh_token' => 'test_refresh_token',
            'client_id'     => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ]);

        $fakeUrl = config('api.message_api_base_url_backend').'/token';

        Http::fake([
            $fakeUrl => Http::response([], 200),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('message.access-token.refresh'));

        $response->assertOk();
        $this->assertSame('""', $response->getContent());
    }
}
