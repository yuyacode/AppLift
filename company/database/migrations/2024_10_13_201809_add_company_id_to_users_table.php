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
            $table->unsignedBigInteger('company_id')
                  ->after('id');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('company_id')
                  ->index()
                  ->references('id')
                  ->on('common.company_infos')
                  ->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
