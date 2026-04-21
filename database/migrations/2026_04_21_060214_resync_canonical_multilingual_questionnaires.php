<?php

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use App\Actions\Questionnaires\SyncDigitalMirrorQuestionnaire;
use App\Actions\Questionnaires\SyncDigitalResilienceQuickScanQuestionnaire;
use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app(SyncAdaptabilityAceQuestionnaire::class)->handle();
        app(SyncDigitalResilienceQuickScanQuestionnaire::class)->handle();
        app(SyncDigitalMirrorQuestionnaire::class)->handle();
        app(SyncPositiveFoundationQuestionnaire::class)->handle();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Forward-only data repair: do not recreate deprecated per-locale questionnaires.
    }
};
