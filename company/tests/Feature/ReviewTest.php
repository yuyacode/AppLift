<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Review;
use App\Models\ReviewAnswer;
use App\Models\ReviewItem;
use Database\Seeders\ReviewItemSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class ReviewTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_authenticated_user_with_review_can_access_edit()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'company_user_id' => $user->id,
        ]);

        $this->seed(ReviewItemSeeder::class);
        $defaultReviewItems = ReviewItem::all();

        foreach ($defaultReviewItems as $item) {
            ReviewAnswer::factory()->create([
                'review_id'      => $review->id,
                'review_item_id' => $item->id,
            ]);
        }

        $response = $this
            ->actingAs($user)
            ->get(route('review.edit'));

        $response->assertOk();
        $response->assertViewIs('review.edit');

        $response->assertViewHas('review');
        $response->assertViewHas('data');

        $retrievedReview = $response->viewData('review');
        $retrievedData   = $response->viewData('data');

        $this->assertEquals($review->id, $retrievedReview->id);
        $this->assertEquals($review->id, $retrievedData->id);

        $this->assertNotEmpty($retrievedReview->reviewAnswer);
        foreach ($retrievedReview->reviewAnswer as $answer) {
            $this->assertNotNull($answer->reviewItem);
        }
    }

    public function test_old_input_overrides_review_data()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'company_user_id' => $user->id,
        ]);

        $this->seed(ReviewItemSeeder::class);
        $defaultReviewItems = ReviewItem::all();

        foreach ($defaultReviewItems as $item) {
            ReviewAnswer::factory()->create([
                'review_id'      => $review->id,
                'review_item_id' => $item->id,
            ]);
        }

        $oldData = [
            'title'  => 'Old Input Title',
            'status' => 1,
        ];

        $response = $this
            ->actingAs($user)
            ->withSession(['_old_input' => $oldData])
            ->get(route('review.edit'));

        $response->assertOk();
        $response->assertViewIs('review.edit');

        $retrievedReview = $response->viewData('review');
        $retrievedData   = $response->viewData('data');

        $this->assertNotNull($retrievedReview);

        $this->assertIsArray($retrievedData);
        $this->assertEquals('Old Input Title', $retrievedData['title']);
        $this->assertEquals(1, $retrievedData['status']);
    }

    public function test_unauthorized_user_cannot_update_review()
    {
        $owner = User::factory()->create();

        $review = Review::factory()->create([
            'company_user_id' => $owner->id,
        ]);

        $anotherUser = User::factory()->create();

        $response = $this
            ->actingAs($anotherUser)
            ->post(route('review.update', $review->id), [
                'title' => 'Updated Title',
                'status' => 1,
                'answers' => [],
            ]);

        $response->assertStatus(403);

        $this->assertNotEquals('Updated Title', $review->fresh()->title);
    }

    public function test_validation_fails_if_required_fields_are_missing()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'company_user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('review.edit'))
            ->post(route('review.update', $review->id), [
                'answers' => [],
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('review.edit'));
        $response->assertSessionHasErrors(['title', 'status']);
    }

    public function test_validation_fails_if_status_is_not_in_0_or_1()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'company_user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('review.edit'))
            ->post(route('review.update', $review->id), [
                'title'   => 'Test Title',
                'status'  => 2,
                'answers' => [],
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('review.edit'));
        $response->assertSessionHasErrors(['status']);
    }

    public function test_validation_fails_if_answers_is_not_array()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'company_user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('review.edit'))
            ->post(route('review.update', $review->id), [
                'title'   => 'Test Title',
                'status'  => 1,
                'answers' => 'not-array',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('review.edit'));
        $response->assertSessionHasErrors(['answers']);
    }

    public function test_successful_update()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'company_user_id' => $user->id,
            'title'  => 'Original Title',
            'status' => 0,
        ]);

        $answer1 = ReviewAnswer::factory()->create([
            'review_id' => $review->id,
            'score'     => 10,
            'answer'    => 'Old answer 1',
        ]);
        $answer2 = ReviewAnswer::factory()->create([
            'review_id' => $review->id,
            'score'     => 20,
            'answer'    => 'Old answer 2',
        ]);

        $formData = [
            'title'  => 'Updated Title',
            'status' => 1,
            'answers' => [
                $answer1->id => ['score' => 99, 'answer' => 'Updated answer 1'],
                $answer2->id => ['score' => 50, 'answer' => 'Updated answer 2'],
            ],
        ];

        $response = $this
            ->actingAs($user)
            ->post(route('review.update', $review->id), $formData);

        $response->assertRedirect(route('review.edit'));
        $response->assertSessionHas('status', 'レビューを保存しました');

        $this->assertDatabaseHas('reviews', [
            'id'     => $review->id,
            'title'  => 'Updated Title',
            'status' => 1,
        ], 'common');

        $this->assertDatabaseHas('review_answers', [
            'id'     => $answer1->id,
            'score'  => 99,
            'answer' => 'Updated answer 1',
        ], 'common');
        $this->assertDatabaseHas('review_answers', [
            'id'     => $answer2->id,
            'score'  => 50,
            'answer' => 'Updated answer 2',
        ], 'common');
    }

    public function test_exception_in_transaction_redirects_back_with_error()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'company_user_id' => $user->id,
        ]);

        $answer = ReviewAnswer::factory()->create([
            'review_id' => $review->id,
        ]);

        $formData = [
            'title'  => 'Will fail',
            'status' => 1,
            'answers' => [
                $answer->id => ['score' => 99, 'answer' => 'some answer'],
            ],
        ];

        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(new \Exception('Test DB error'));

        $response = $this
            ->actingAs($user)
            ->from(route('review.edit'))
            ->post(route('review.update', $review->id), $formData);

        $response->assertRedirect(route('review.edit'));
        $response->assertSessionHasErrors();

        $this->assertNotEquals('Will fail', $review->fresh()->title);
    }
}
