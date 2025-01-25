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
        Schema::connection('company')->table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_info_id')
                  ->after('id');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('company')->table('users', function (Blueprint $table) {
            $table->dropColumn('company_info_id');
        });
    }
};
