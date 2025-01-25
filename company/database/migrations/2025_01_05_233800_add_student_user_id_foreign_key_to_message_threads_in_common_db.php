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
                $table->foreign('student_user_id')
                  ->references('id')
                  ->on('student.users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('common')->table('message_threads', function (Blueprint $table) {
            $table->dropForeign(['student_user_id']);
        });
    }
};
