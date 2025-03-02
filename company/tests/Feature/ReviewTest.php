<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Review;
use App\Models\ReviewAnswer;
use App\Models\ReviewItem;
use Database\Seeders\ReviewItemSeeder;
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
}
