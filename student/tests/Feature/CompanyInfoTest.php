<?php

namespace Tests\Feature;

use App\Models\CompanyInfo;
use App\Models\CompanyInfoViewLog;
use App\Models\User;
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
}
