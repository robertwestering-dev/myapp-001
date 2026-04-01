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
        Schema::table('questionnaire_responses', function (Blueprint $table) {
            $table->timestamp('last_saved_at')->nullable()->after('submitted_at');
            $table->string('resume_token', 64)->nullable()->unique()->after('last_saved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questionnaire_responses', function (Blueprint $table) {
            $table->dropUnique(['resume_token']);
            $table->dropColumn(['last_saved_at', 'resume_token']);
        });
    }
};
