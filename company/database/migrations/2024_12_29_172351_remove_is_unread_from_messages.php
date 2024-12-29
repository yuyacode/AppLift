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
            $table->dropColumn('is_unread');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('common')->table('messages', function (Blueprint $table) {
            $table->tinyInteger('is_unread')->default(0)->unsigned();
        });
    }
};
