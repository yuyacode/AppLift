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
        // after()を機能させるために、先にカラムを追加してから、外部キーを設定する
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_info_id')
                  ->after('id');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('company_info_id')
                  ->references('id')
                  ->on('common.company_infos');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_info_id']);
            $table->dropColumn('company_info_id');
        });
    }
};
