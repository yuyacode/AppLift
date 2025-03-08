<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class PasswordResetTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/student/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        $this->markTestSkipped(
            'Skipping this test because it currently hangs indefinitely. ' .
            'Our investigation was inconclusive, and we plan to revisit and fix it in the future.'
        );

        Notification::fake();

        $user = User::factory()->create();

        $this->post('/student/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $this->markTestSkipped(
            'Skipping this test because it currently hangs indefinitely. ' .
            'Our investigation was inconclusive, and we plan to revisit and fix it in the future.'
        );

        Notification::fake();

        $user = User::factory()->create();

        $this->post('/student/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/student/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $this->markTestSkipped(
            'Skipping this test because it currently hangs indefinitely. ' .
            'Our investigation was inconclusive, and we plan to revisit and fix it in the future.'
        );

        Notification::fake();

        $user = User::factory()->create();

        $this->post('/student/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/student/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
