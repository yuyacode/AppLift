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
}
