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
        Schema::connection('common')->table('review_items', function (Blueprint $table) {
            $table->tinyInteger('is_default')
                  ->default(0)
                  ->unsigned()
                  ->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('common')->table('review_items', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
