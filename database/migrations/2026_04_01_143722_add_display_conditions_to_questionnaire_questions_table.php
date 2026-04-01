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
        Schema::table('questionnaire_questions', function (Blueprint $table) {
            $table->foreignId('display_condition_question_id')
                ->nullable()
                ->after('options')
                ->constrained('questionnaire_questions')
                ->nullOnDelete();
            $table->string('display_condition_operator', 40)->nullable()->after('display_condition_question_id');
            $table->json('display_condition_answer')->nullable()->after('display_condition_operator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questionnaire_questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('display_condition_question_id');
            $table->dropColumn(['display_condition_operator', 'display_condition_answer']);
        });
    }
};
