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
            $table->integer('time_spent')->nullable()->default(0)->comment('Time spent in minutes');
            $table->timestamp('last_accessed_at')->nullable();
            $table->integer('access_count')->nullable()->default(0)->comment('Number of times accessed');
            $table->text('notes')->nullable()->comment('User notes about this chapter');
            $table->integer('comprehension_rating')->nullable()->comment('Self-rated comprehension (1-5)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapter_completions', function (Blueprint $table) {
            $table->dropColumn([
                'time_spent',
                'last_accessed_at',
                'access_count',
                'notes',
                'comprehension_rating'
            ]);
        });
    }
};
