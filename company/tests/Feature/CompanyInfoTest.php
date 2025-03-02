<?php

namespace Tests\Feature;

use App\Models\CompanyInfo;
use App\Models\Review;
use App\Models\ReviewItem;
use App\Models\User;
use Database\Seeders\ReviewItemSeeder;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class CompanyInfoTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_non_master_user_cannot_access_company_info()
    {
        $user = User::factory()->sub_account()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('company_info.index'));

        $response->assertStatus(403);
    }

    public function test_master_user_can_access_index_and_see_company_info_and_members()
    {
        $companyInfo = CompanyInfo::factory()->create();

        $masterAccount = User::factory()->master_account()->create();
        $masterAccount->companyInfo()->associate($companyInfo);
        $masterAccount->save();

        $subAccount = User::factory()->sub_account()->create();
        $subAccount->companyInfo()->associate($companyInfo);
        $subAccount->save();

        $response = $this
            ->actingAs($masterAccount)
            ->get(route('company_info.index'));

        $response->assertStatus(200);

        $response->assertViewIs('company_info.index');

        $response->assertViewHas('company_info', function ($viewCompanyInfo) use ($companyInfo) {
            return $viewCompanyInfo->id === $companyInfo->id;
        });

        $response->assertViewHas('members', function ($members) use ($masterAccount, $subAccount) {
            return $members->contains($masterAccount) && $members->contains($subAccount);
        });
    }

    public function test_master_user_cannot_access_another_company_info_edit()
    {
        $companyInfo = CompanyInfo::factory()->create();

        $user = User::factory()->master_account()->create([
            'company_info_id' => 999, // テスト用にあえて異なる企業IDを設定
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('company_info.basic_info.edit',$companyInfo->id));

        $response->assertStatus(403);
    }

    public function test_master_user_can_access_own_company_info_edit()
    {
        $companyInfo = CompanyInfo::factory()->create();

        $user = User::factory()->master_account()->create([
            'company_info_id' => $companyInfo->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('company_info.basic_info.edit', $companyInfo->id));

        $response->assertStatus(200);

        $response->assertViewIs('company_info.edit');

        $response->assertViewHas('company_info', function ($viewCompanyInfo) use ($companyInfo) {
            return $viewCompanyInfo->id === $companyInfo->id;
        });

        // old() の値が無い場合、dataには $companyInfo オブジェクトがセットされることを確認
        $response->assertViewHas('data', $companyInfo);
    }

    public function test_edit_shows_old_data_if_present()
    {
        $companyInfo = CompanyInfo::factory()->create();

        $user = User::factory()->master_account()->create([
            'company_info_id' => $companyInfo->id,
        ]);

        // セッションに old データを設定してリクエスト
        $oldInput = [
            'name'     => 'Old Company Name',
            'address'  => 'Old Address',
            'homepage' => 'https://old-homepage.com',
        ];

        $response = $this
            ->actingAs($user)
            ->withSession(['_old_input' => $oldInput])
            ->get(route('company_info.basic_info.edit', $companyInfo->id));

        $response->assertStatus(200);

        $response->assertViewHas('data', function ($viewData) use ($oldInput) {
            return $viewData['name'] === $oldInput['name']
                && $viewData['address'] === $oldInput['address']
                && $viewData['homepage'] === $oldInput['homepage'];
        });
    }

    public function test_master_user_cannot_update_other_company_info()
    {
        $companyInfo = CompanyInfo::factory()->create();

        $user = User::factory()->master_account()->create([
            'company_info_id' => 999,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('company_info.basic_info.update', $companyInfo->id), [
                'name'     => 'Test Corp',
                'address'  => '123 Test St.',
                'homepage' => 'https://example.com',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_master_user_can_update_own_company_info()
    {
        $companyInfo = CompanyInfo::factory()->create([
            'name'     => 'Old Name',
            'address'  => 'Old Address',
            'homepage' => 'https://old.example.com'
        ]);

        $user = User::factory()->master_account()->create([
            'company_info_id' => 1,
        ]);

        $postData = [
            'name'     => 'New Name',
            'address'  => 'New Address',
            'homepage' => 'https://new.example.com',
        ];

        $response = $this
            ->actingAs($user)
            ->post(route('company_info.basic_info.update', $companyInfo->id), $postData);

        $response->assertRedirect(route('company_info.index'));

        $response->assertSessionHas('status_company-basic-info', '基本情報を変更しました');

        $this->assertDatabaseHas('company_infos', [
            'id'       => $companyInfo->id,
            'name'     => $postData['name'],
            'address'  => $postData['address'],
            'homepage' => $postData['homepage'],
        ], 'common');
    }

    public function test_update_fails_when_validation_fails()
    {
        $companyInfo = CompanyInfo::factory()->create();

        $user = User::factory()
            ->master_account()
            ->create([
                'company_info_id' => $companyInfo->id,
        ]);

        $invalidData = [
            'name'     => '',
            'address'  => '',
            'homepage' => '',
        ];

        $response = $this
            ->actingAs($user)
            ->post(route('company_info.basic_info.update', $companyInfo->id), $invalidData);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['name', 'address', 'homepage']);

        $this->assertDatabaseHas('company_infos', [
            'id'       => $companyInfo->id,
            'name'     => $companyInfo->name,
            'address'  => $companyInfo->address,
            'homepage' => $companyInfo->homepage,
        ], 'common');
    }

    public function test_only_master_user_can_view_member_create_page()
    {
        $masterAccount = User::factory()->master_account()->create();
        
        $response = $this
            ->actingAs($masterAccount)
            ->get(route('company_info.member.create'));

        $response->assertOk();

        $response->assertViewIs('user.create');

        $response->assertViewHas('data', []);
    }

    public function test_old_input_is_passed_to_data_when_exists()
    {
        $masterAccount = User::factory()->master_account()->create();

        $oldInput = ['name' => 'Old Input Test'];

        $response = $this
            ->actingAs($masterAccount)
            ->withSession(['_old_input' => $oldInput])
            ->get(route('company_info.member.create'));

        $response->assertOk();

        $response->assertViewIs('user.create');

        $response->assertViewHas('data', $oldInput);
    }

    public function test_validation_fails_when_required_fields_are_missing()
    {
        $masterUser = User::factory()->master_account()->create();
        
        $response = $this
            ->actingAs($masterUser)
            ->from(route('company_info.member.create'))
            ->post(route('company_info.member.store'), []);

        $response->assertStatus(302);
        $response->assertRedirect(route('company_info.member.create'));
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_storing_member_with_valid_data_and_api_successfully_returns_ok()
    {
        $this->seed(ReviewItemSeeder::class);

        $defaultReviewItems = ReviewItem::all();

        $fakeUrl = config('api.message_api_base_url_backend').'/register';

        Http::fake([
            $fakeUrl => Http::response([
                'message' => 'OAuth registration was successful',
                'detail'  => '',
            ], 200),
        ]);

        $companyInfo = CompanyInfo::factory()->create();

        $masterUser = User::factory()->master_account()->create([
            'company_info_id' => $companyInfo->id
        ]);

        $formData = [
            'name'     => 'Test User',
            'email'    => 'testuser@example.com',
            'password' => 'Password',
        ];

        $response = $this
            ->actingAs($masterUser)
            ->post(route('company_info.member.store'), $formData);

        $response->assertRedirect(route('company_info.index'));
        $response->assertSessionHas('status_company-member', 'メンバーを追加しました');

        $this->assertDatabaseHas('users', [
            'company_info_id' => $companyInfo->id,
            'name'            => 'Test User',
            'email'           => 'testuser@example.com',
            'is_master'       => 0,
        ], 'company');

        $createdUser = User::where('email', 'testuser@example.com')->first();

        $this->assertDatabaseHas('reviews', [
            'company_user_id' => $createdUser->id,
            'status'          => 0,
        ], 'common');

        $createdReview = Review::where('company_user_id', $createdUser->id)->first();

        $this->assertDatabaseCount('review_answers', $defaultReviewItems->count(), 'common');

        foreach ($defaultReviewItems as $item) {
            $this->assertDatabaseHas('review_answers', [
                'review_id'      => $createdReview->id,
                'review_item_id' => $item->id,
            ], 'common');
        }

        Http::assertSent(function ($request) use ($fakeUrl, $createdUser) {
            return $request->url() === $fakeUrl
                && $request['user_id'] === $createdUser->id
                && $request['app_kind'] === 'company';
        });
    }
}
