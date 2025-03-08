<?php

namespace Tests\Feature;

use App\Models\CompanyInfo;
use App\Models\CompanyInfoViewLog;
use App\Models\CompanyUser;
use App\Models\Review;
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
}
