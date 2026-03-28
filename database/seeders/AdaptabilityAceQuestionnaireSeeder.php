<?php

namespace Database\Seeders;

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use Illuminate\Database\Seeder;

class AdaptabilityAceQuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        (new SyncAdaptabilityAceQuestionnaire)->handle();
    }
}
