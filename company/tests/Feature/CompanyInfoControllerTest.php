<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class CompanyInfoControllerTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_test_insert(): void
    {
        $this->assertDatabaseMissing('message_api_credentials', [
            'user_id'       => 1,
            'client_id'     => "hoge",
            'client_secret' => "hoge",
            'access_token'  => "hoge",
            'refresh_token' => "hoge",
            'expires_at'    => "2025-01-26 14:26:18",
        ], 'company');
    
        $this->assertDatabaseMissing('message_threads', [
            'company_user_id' => 1,
            'student_user_id' => 1,
        ], 'common');

        $controller = new \App\Http\Controllers\CompanyInfoController;
        $controller->test_insert();

        $this->assertDatabaseHas('message_api_credentials', [
            'user_id'       => 2,
            'client_id'     => "hoge",
            'client_secret' => "hoge",
            'access_token'  => "hoge",
            'refresh_token' => "hoge",
            'expires_at'    => "2025-01-26 14:26:18",
        ], 'company');

        $this->assertDatabaseHas('message_threads', [
            'company_user_id' => 2,
            'student_user_id' => 1,
        ], 'common');
    }

    public function test_test_insert2(): void
    {
        $this->assertDatabaseMissing('message_api_credentials', [
            'user_id'       => 1,
            'client_id'     => "hoge",
            'client_secret' => "hoge",
            'access_token'  => "hoge",
            'refresh_token' => "hoge",
            'expires_at'    => "2025-01-26 14:26:18",
        ], 'company');
    
        $this->assertDatabaseMissing('message_threads', [
            'company_user_id' => 1,
            'student_user_id' => 1,
        ], 'common');

        $controller = new \App\Http\Controllers\CompanyInfoController;
        $controller->test_insert();

        $this->assertDatabaseHas('message_api_credentials', [
            'user_id'       => 2,
            'client_id'     => "hoge",
            'client_secret' => "hoge",
            'access_token'  => "hoge",
            'refresh_token' => "hoge",
            'expires_at'    => "2025-01-26 14:26:18",
        ], 'company');

        $this->assertDatabaseHas('message_threads', [
            'company_user_id' => 2,
            'student_user_id' => 1,
        ], 'common');
    }
    
}
