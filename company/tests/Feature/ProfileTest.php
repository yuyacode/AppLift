<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class ProfileTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/company/profile');

        $response->assertOk();
    }

    public function test_account_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/company/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/company/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/company/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/company/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch("/company/profile/{$user->id}", [
                'department'   => 'New Department',
                'occupation'   => 'New Occupation',
                'position'     => 'New Position',
                'join_date'    => '2020年4月',
                'introduction' => 'New Introduction',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile_info-updated');

        $user->refresh();

        $this->assertSame('New Department', $user->department);
        $this->assertSame('New Occupation', $user->occupation);
        $this->assertSame('New Position', $user->position);
        $this->assertSame('2020年4月', $user->join_date);
        $this->assertSame('New Introduction', $user->introduction);
    }

    public function test_user_cannot_update_other_users_profile()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $response = $this
            ->actingAs($userA)
            ->patch("/company/profile/{$userB->id}", [
                'department' => 'Should Not Be Updated',
            ]);

        $response->assertForbidden();
    }

    public function test_update_fails_with_invalid_data()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch("/company/profile/{$user->id}", [
                'department' => str_repeat('a', 256),
            ]);

        $response->assertSessionHasErrors(['department']);

        $user->refresh();

        $this->assertNotSame(str_repeat('a', 256), $user->department);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/company/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/company');

        $this->assertGuest();
        $this->assertSoftDeleted($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/company/profile')
            ->delete('/company/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/company/profile');

        $this->assertNull($user->fresh()->deleted_at);
    }
}
