<?php

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use App\Actions\Questionnaires\SyncDigitalResilienceQuickScanQuestionnaire;
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
        (new SyncDigitalResilienceQuickScanQuestionnaire)->handle();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Questionnaire::query()
            ->whereIn('title', [
                SyncAdaptabilityAceQuestionnaire::ENGLISH_TITLE,
                SyncDigitalResilienceQuickScanQuestionnaire::ENGLISH_TITLE,
            ])
            ->delete();
    }
};
