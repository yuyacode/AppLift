<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class RegistrationTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $fakeUrl = config('api.message_api_base_url_backend').'/register';

        Http::fake([
            $fakeUrl => Http::response([
                'message' => 'OAuth registration was successful',
                'detail'  => '',
            ], 200),
        ]);

        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        Http::assertSent(function ($request) use ($fakeUrl) {
            return $request->url() === $fakeUrl
                && $request['user_id'] === 1
                && $request['app_kind'] === 'student';
        });
    }
}
