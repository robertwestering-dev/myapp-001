<?php

namespace Database\Seeders;

use App\Actions\Questionnaires\SyncDigitalResilienceQuickScanQuestionnaire;
use Illuminate\Database\Seeder;

class DigitalResilienceQuickScanQuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        (new SyncDigitalResilienceQuickScanQuestionnaire)->handle();
    }
}
