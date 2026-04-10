<?php

namespace Database\Seeders;

use App\Actions\Questionnaires\SyncDigitalMirrorQuestionnaire;
use Illuminate\Database\Seeder;

class DigitalMirrorQuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        (new SyncDigitalMirrorQuestionnaire)->handle();
    }
}
