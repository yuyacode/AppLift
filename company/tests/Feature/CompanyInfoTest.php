<?php

namespace Tests\Feature;

use App\Models\CompanyInfo;
use App\Models\User;
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
}
