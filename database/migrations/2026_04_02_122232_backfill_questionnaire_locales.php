<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('questionnaires')
            ->whereNull('locale')
            ->update([
                'locale' => 'nl',
            ]);

        DB::table('questionnaire_questions')
            ->select('questionnaire_questions.id', 'questionnaires.locale as questionnaire_locale')
            ->join('questionnaire_categories', 'questionnaire_categories.id', '=', 'questionnaire_questions.questionnaire_category_id')
            ->join('questionnaires', 'questionnaires.id', '=', 'questionnaire_categories.questionnaire_id')
            ->whereNull('questionnaire_questions.locale')
            ->orderBy('questionnaire_questions.id')
            ->get()
            ->each(function (object $question): void {
                DB::table('questionnaire_questions')
                    ->where('id', $question->id)
                    ->update([
                        'locale' => $question->questionnaire_locale ?? 'nl',
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('questionnaire_questions')->update([
            'locale' => null,
        ]);

        DB::table('questionnaires')->update([
            'locale' => null,
        ]);
    }
};
