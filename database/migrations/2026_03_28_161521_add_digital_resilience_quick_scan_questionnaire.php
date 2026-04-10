<?php

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
        (new SyncDigitalResilienceQuickScanQuestionnaire)->handle();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Questionnaire::query()
            ->whereIn('title', [
                SyncDigitalResilienceQuickScanQuestionnaire::TITLE,
                SyncDigitalResilienceQuickScanQuestionnaire::ENGLISH_TITLE,
            ])
            ->delete();
    }
};
