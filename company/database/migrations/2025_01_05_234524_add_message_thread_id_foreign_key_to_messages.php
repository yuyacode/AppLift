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
        Schema::connection('common')->table('messages', function (Blueprint $table) {
            $table->foreign('message_thread_id')
                  ->references('id')
                  ->on('message_threads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('common')->table('messages', function (Blueprint $table) {
            $table->dropForeign(['message_thread_id']);
        });
    }
};
