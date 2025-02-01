<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\RefreshMultipleDatabases;

class CompanyInfoControllerTest extends TestCase
{
    use RefreshMultipleDatabases;

    public function test_test_insert(): void
    {
        $this->assertDatabaseMissing('company_info_view_logs', [
            'user_id'         => 1,
            'company_info_id' => 1,
        ], 'student');
    
        $this->assertDatabaseMissing('message_threads', [
            'company_user_id' => 1,
            'student_user_id' => 1,
        ], 'common');

        $controller = new \App\Http\Controllers\CompanyInfoController;
        $controller->test_insert();

        $this->assertDatabaseHas('company_info_view_logs', [
            'user_id'         => 1,
            'company_info_id' => 1,
        ], 'student');

        $this->assertDatabaseHas('message_threads', [
            'company_user_id' => 1,
            'student_user_id' => 1,
        ], 'common');
    }

    public function test_test_insert2(): void
    {
        $this->assertDatabaseMissing('company_info_view_logs', [
            'user_id'         => 1,
            'company_info_id' => 1,
        ], 'student');
    
        $this->assertDatabaseMissing('message_threads', [
            'company_user_id' => 1,
            'student_user_id' => 1,
        ], 'common');

        $controller = new \App\Http\Controllers\CompanyInfoController;
        $controller->test_insert();

        $this->assertDatabaseHas('company_info_view_logs', [
            'user_id'         => 1,
            'company_info_id' => 1,
        ], 'student');

        $this->assertDatabaseHas('message_threads', [
            'company_user_id' => 1,
            'student_user_id' => 1,
        ], 'common');
    }
}
