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
        Schema::connection('company')->table('users', function (Blueprint $table) {
            $table->string('department')->nullable()->after('remember_token');
            $table->string('occupation')->nullable()->after('department');
            $table->string('position')->nullable()->after('occupation');
            $table->string('join_date')->nullable()->after('position');
            $table->text('introduction')->nullable()->after('join_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('company')->table('users', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->dropColumn('occupation');
            $table->dropColumn('position');
            $table->dropColumn('join_date');
            $table->dropColumn('introduction');
        });
    }
};
