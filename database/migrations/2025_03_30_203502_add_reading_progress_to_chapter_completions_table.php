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
        Schema::table('chapter_completions', function (Blueprint $table) {
            $table->integer('reading_progress')->default(0);
            $table->json('reading_progress_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapter_completions', function (Blueprint $table) {
            $table->dropColumn(['reading_progress', 'reading_progress_data']);
        });
    }
};
