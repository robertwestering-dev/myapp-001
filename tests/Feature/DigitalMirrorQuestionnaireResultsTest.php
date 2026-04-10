<?php

use App\Actions\Questionnaires\SyncDigitalMirrorQuestionnaire;
use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use Database\Seeders\DigitalMirrorQuestionnaireSeeder;
use Illuminate\Support\Carbon;

test('digital mirror shows a scored analysis with recommended start layer after submission', function () {
    $this->seed(DigitalMirrorQuestionnaireSeeder::class);

    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'nl',
    ]);

    $questionnaire = Questionnaire::query()
        ->with(['categories.questions'])
        ->where('title', SyncDigitalMirrorQuestionnaire::TITLE)
        ->firstOrFail();

    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $answers = [];

    foreach ($questionnaire->categories as $category) {
        foreach ($category->questions as $question) {
            $answers[$question->id] = match ($category->sort_order.':'.$question->sort_order) {
                '4:1' => 'Zeer mee eens',
                '4:2', '4:3', '4:4' => 'Mee oneens',
                default => 'Mee eens',
            };
        }
    }

    $this->actingAs($user)
        ->post(route('questionnaire-responses.store', $availability), [
            'intent' => 'submit',
            'answers' => $answers,
        ])
        ->assertRedirect(route('questionnaire-responses.show', $availability));

    $response = $availability->responses()->firstOrFail();

    expect($response->analysis_snapshot)->toBeArray();
    expect($response->analysis_snapshot['profile_label'])->toBe('In ontwikkeling');
    expect($response->analysis_snapshot['score'])->toBe(95);
    expect($response->analysis_snapshot['recommended_dimension_label'])->toBe('Stress en het brein');

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee('Je groeit — en er is duidelijk richting.')
        ->assertSee('95 / 140')
        ->assertSee('In ontwikkeling')
        ->assertSee('Aanbevolen startlaag')
        ->assertSee('Stress en het brein')
        ->assertSee('Begin bij Stress en het brein →')
        ->assertSee('Je brein reageert sterk op digitale dreiging.');
});
