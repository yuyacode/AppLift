<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('common')->table('message_threads', function (Blueprint $table) {
            $table->foreign('company_user_id')
                  ->references('id')
                  ->on('company.users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('common')->table('message_threads', function (Blueprint $table) {
            $table->dropForeign(['company_user_id']);
        });
    }
};
