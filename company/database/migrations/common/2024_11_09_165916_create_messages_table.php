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
        Schema::connection('common')->create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_thread_id');
            $table->tinyInteger('is_from_company')->default(0)->unsigned();
            $table->tinyInteger('is_from_student')->default(0)->unsigned();
            $table->text('content');
            $table->tinyInteger('is_unread')->default(0)->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('common')->dropIfExists('messages');
    }
};
