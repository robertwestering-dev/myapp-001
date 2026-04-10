<?php

use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use App\Models\Questionnaire;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        (new SyncPositiveFoundationQuestionnaire)->handle();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Questionnaire::query()
            ->where('title', SyncPositiveFoundationQuestionnaire::TITLE)
            ->delete();
    }
};
