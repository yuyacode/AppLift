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
        Schema::connection('common')->table('review_answers', function (Blueprint $table) {
            $table->foreign('review_id')
                  ->references('id')
                  ->on('reviews');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('common')->table('review_answers', function (Blueprint $table) {
            $table->dropForeign(['review_id']);
        });
    }
};
