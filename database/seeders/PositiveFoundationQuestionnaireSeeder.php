<?php

namespace Database\Seeders;

use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use Illuminate\Database\Seeder;

class PositiveFoundationQuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        (new SyncPositiveFoundationQuestionnaire)->handle();
    }
}
