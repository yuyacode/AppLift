<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Review;
use App\Models\ReviewItem;
use App\Models\ReviewAnswer;
use Database\Seeders\ReviewItemSeeder;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class RegistrationTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/company/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->seed(ReviewItemSeeder::class);

        $fakeUrl = config('api.message_api_base_url_backend').'/register';

        Http::fake([
            $fakeUrl => Http::response([
                'message' => 'OAuth registration was successful',
                'detail'  => '',
            ], 200),
        ]);

        $response = $this->post('/company/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseCount('company_infos', 1, 'common');

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user->company_info_id);
        $this->assertEquals(1, $user->is_master, 'is_master of user is not set to 1');

        $review = Review::where('company_user_id', $user->id)->first();
        $this->assertNotNull($review, 'Review not created');
        $this->assertEquals(0, $review->status, 'Review status is not set to 0');

        $defaultReviewItemCount = ReviewItem::where('is_default', 1)->count();
        $this->assertEquals($defaultReviewItemCount, ReviewAnswer::where('review_id', $review->id)->count());

        Http::assertSent(function ($request) use ($fakeUrl, $user) {
            return $request->url() === $fakeUrl
                && $request['user_id'] === $user->id
                && $request['app_kind'] === 'company';
        });

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
