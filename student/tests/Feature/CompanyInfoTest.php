<?php

namespace Tests\Feature;

use App\Models\CompanyInfo;
use App\Models\CompanyInfoViewLog;
use App\Models\CompanyUser;
use App\Models\Review;
use App\Models\ReviewAnswer;
use App\Models\ReviewItem;
use App\Models\User;
use Database\Seeders\ReviewItemSeeder;
use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class CompanyInfoTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_index_displays_recent_viewed_companies_in_desc_order()
    {
        $user = User::factory()->create();

        $companyA = CompanyInfo::factory()->create();
        $companyB = CompanyInfo::factory()->create();

        CompanyInfoViewLog::factory()->create([
            'user_id'         => $user->id,
            'company_info_id' => $companyA->id,
        ]);
        CompanyInfoViewLog::factory()->create([
            'user_id'         => $user->id,
            'company_info_id' => $companyB->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('company_info.index'));

        $response->assertOk()
            ->assertViewIs('company_info.index')
            ->assertViewHas('recent_viewed_companies', function ($collection) use ($companyA, $companyB) {
                if ($collection->count() !== 2) {
                    return false;
                }
                return $collection->get(0)->id === $companyB->id && $collection->get(1)->id === $companyA->id;
            });
    }

    public function test_index_displays_empty_collection_when_no_logs()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('company_info.index'));

        $response->assertOk()
            ->assertViewIs('company_info.index')
            ->assertViewHas('recent_viewed_companies', function ($collection) {
                return $collection->isEmpty();
            });
    }

    public function test_show_creates_new_view_log_and_displays_view()
    {
        $user = User::factory()->create();

        $companyInfo = CompanyInfo::factory()->create();
        $companyUser = CompanyUser::factory()->for($companyInfo)->create();
        Review::factory()->for($companyUser)->create();

        $existingLog = CompanyInfoViewLog::factory()->create([
            'user_id'         => $user->id,
            'company_info_id' => $companyInfo->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('company_info.show', ['company_info' => $companyInfo->id]));

        $response->assertOk()
            ->assertViewIs('company_info.show')
            ->assertViewHas('company_info', $companyInfo)
            ->assertViewHas('members', function ($members) use ($companyUser) {
                return $members->contains($companyUser) && $members->first()->relationLoaded('review');
            });

        $this->assertDatabaseMissing('company_info_view_logs', [
            'id' => $existingLog->id,
        ], 'student');

        $this->assertDatabaseHas('company_info_view_logs', [
            'user_id'         => $user->id,
            'company_info_id' => $companyInfo->id,
        ], 'student');
    }

    public function test_show_does_not_remove_any_record_if_user_has_less_than_10_logs()
    {
        $user = User::factory()->create();
        $companyInfo = CompanyInfo::factory()->create();

        $fakeCompanyInfoIds = [100, 200, 300];  // $companyInfo->id（1）は、delete対象となってしまい、最終的な件数の検証で失敗するため、$companyInfo->id（1）だけは避ける

        foreach ($fakeCompanyInfoIds as $id) {
            CompanyInfoViewLog::factory()->create([
                'user_id'         => $user->id,
                'company_info_id' => $id,
            ]);
        }

        $response = $this
            ->actingAs($user)
            ->get(route('company_info.show', ['company_info' => $companyInfo->id]));

        $response->assertOk()
            ->assertViewIs('company_info.show');

        foreach ($fakeCompanyInfoIds as $id) {
            $this->assertDatabaseHas('company_info_view_logs', [
                'user_id'         => $user->id,
                'company_info_id' => $id,
            ], 'student');
        }

        $this->assertCount(
            4,  // 既存３件＋新規１件
            CompanyInfoViewLog::where('user_id', $user->id)->get()
        );

        $this->assertDatabaseHas('company_info_view_logs', [
            'user_id'         => $user->id,
            'company_info_id' => $companyInfo->id,
        ], 'student');
    }

    public function test_member_displays_company_info_user_and_review()
    {
        $companyInfo = CompanyInfo::factory()->create([
            'name' => 'Test Company',
        ]);

        $companyUser = CompanyUser::factory()->create([
            'company_info_id' => $companyInfo->id,
            'name'           => 'Taro Yamada',
            'department'     => 'Sales',
            'occupation'     => 'Engineer',
            'position'       => 'Manager',
            'join_date'      => '2020-01-01',
            'introduction'   => 'Joined recently.',
        ]);

        $review = Review::factory()->create([
            'company_user_id' => $companyUser->id,
            'title'           => 'Review Title',
            'status'          => 1,
        ]);

        $this->seed(ReviewItemSeeder::class);
        $defaultReviewItems = ReviewItem::all();

        foreach ($defaultReviewItems as $item) {
            ReviewAnswer::factory()->create([
                'review_id'      => $review->id,
                'review_item_id' => $item->id,
            ]);
        }

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('company_info.member', [
                'company_info' => $companyInfo->id,
                'company_user' => $companyUser->id,
            ]));

        $response->assertOk()
            ->assertViewIs('company_info.member')
            ->assertViewHas('company_info')
            ->assertViewHas('company_user')
            ->assertViewHas('review');

        $viewCompanyInfo = $response->viewData('company_info');
        $this->assertEquals($companyInfo->id, $viewCompanyInfo['id']);
        $this->assertEquals('Test Company', $viewCompanyInfo['name']);
        $this->assertCount(2, $viewCompanyInfo);

        $viewCompanyUser = $response->viewData('company_user');
        $this->assertEquals($companyUser->id, $viewCompanyUser['id']);
        $this->assertEquals('Taro Yamada', $viewCompanyUser['name']);
        $this->assertEquals('Sales', $viewCompanyUser['department']);
        $this->assertEquals('Engineer', $viewCompanyUser['occupation']);
        $this->assertEquals('Manager', $viewCompanyUser['position']);
        $this->assertEquals('2020-01-01', $viewCompanyUser['join_date']);
        $this->assertEquals('Joined recently.', $viewCompanyUser['introduction']);
        $this->assertCount(7, $viewCompanyUser);

        $viewReview = $response->viewData('review');
        $this->assertEquals($review->id, $viewReview->id);
        $this->assertEquals('Review Title', $viewReview->title);
        $this->assertEquals(1, $viewReview->status);

        $this->assertTrue($viewReview->relationLoaded('reviewAnswers'));
        $this->assertCount(count($defaultReviewItems), $viewReview->reviewAnswers);

        $actualReviewItemNames = $viewReview->reviewAnswers->map(function ($answer) {
            return $answer->reviewItem->name;
        });
        $expectedReviewItemNames = $defaultReviewItems->pluck('name');
        $this->assertEquals(
            $expectedReviewItemNames->sort()->values(),
            $actualReviewItemNames->sort()->values()
        );
    }

    public function test_search_returns_400_when_no_keyword_is_provided()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson(route('company_info.search'));

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Keyword is required.',
            ]);
    }

    public function test_search_returns_empty_array_when_no_matches()
    {
        $user = User::factory()->create();

        CompanyInfo::factory()->create([
            'name' => 'Laravel Inc',
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson(route('company_info.search', ['keyword' => 'test']));

        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function test_search_returns_matching_companies()
    {
        $user = User::factory()->create();

        CompanyInfo::factory()->create([
            'name' => 'Test Company',
        ]);
        CompanyInfo::factory()->create([
            'name' => 'Other Company',
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson(route('company_info.search', ['keyword' => 'test']));

        $response->assertOk();

        $response->assertJsonStructure([
            '*' => ['id', 'name', 'homepage'],
        ]);

        $response->assertJsonCount(1);

        $responseData = $response->json();
        $this->assertEquals('Test Company', $responseData[0]['name']);
    }
}
