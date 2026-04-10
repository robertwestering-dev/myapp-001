<?php

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use App\Models\Questionnaire;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        (new SyncAdaptabilityAceQuestionnaire)->handle();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Questionnaire::query()
            ->whereIn('title', [
                SyncAdaptabilityAceQuestionnaire::TITLE,
                SyncAdaptabilityAceQuestionnaire::ENGLISH_TITLE,
            ])
            ->delete();
    }
};
