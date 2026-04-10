<?php

use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use Database\Seeders\PositiveFoundationQuestionnaireSeeder;
use Illuminate\Support\Carbon;

test('pro users see a scored perma analysis with the weakest pillar highlighted after submission', function () {
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);

    $organization = Organization::factory()->create();
    $user = User::factory()->pro()->create([
        'org_id' => $organization->org_id,
        'locale' => 'nl',
    ]);

    $questionnaire = Questionnaire::query()
        ->with(['categories.questions'])
        ->where('title', SyncPositiveFoundationQuestionnaire::TITLE)
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
            $answers[$question->id] = match ($category->sort_order) {
                1 => 'Soms',
                2 => 'Nooit / niet',
                3 => 'Vaak',
                4 => $question->sort_order === 4 ? 'Vaak' : 'Altijd / Volledig',
                5 => $question->sort_order === 4 ? 'Soms' : 'Zelden',
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
    expect($response->analysis_snapshot['profile_label'])->toBe('Gedeeltelijk fundament');
    expect($response->analysis_snapshot['score'])->toBe(60);
    expect($response->analysis_snapshot['recommended_dimension_label'])->toBe('Betrokkenheid');
    expect($response->analysis_snapshot['dimensions'])->toHaveCount(5);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee('Je fundament is aanwezig, maar nog niet overal even sterk.')
        ->assertSee('60 / 100')
        ->assertSee('Gedeeltelijk fundament')
        ->assertSee('Betrokkenheid')
        ->assertSee('Start met module 3: Zingeving en betrokkenheid verdiepen →')
        ->assertSee('4 / 20')
        ->assertSee('Je sterke kanten blijven onbenut. Dat is een gemiste kans - en het is oplosbaar. Start met module 2: sterke kanten ontdekken.');
});

test('free users see only the overall positive foundation result after submission', function () {
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);

    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'nl',
    ]);

    $questionnaire = Questionnaire::query()
        ->with(['categories.questions'])
        ->where('title', SyncPositiveFoundationQuestionnaire::TITLE)
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
            $answers[$question->id] = match ($category->sort_order) {
                1 => 'Vaak',
                2 => 'Soms',
                3 => 'Zelden',
                4 => 'Soms',
                5 => 'Vaak',
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
    expect($response->analysis_snapshot['profile_label'])->toBe('Gedeeltelijk fundament');
    expect($response->analysis_snapshot['score'])->toBe(64);
    expect($response->analysis_snapshot['recommended_dimension_label'])->toBeNull();
    expect($response->analysis_snapshot['dimensions'])->toBe([]);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee('Je fundament is aanwezig, maar nog niet overal even sterk.')
        ->assertSee('64 / 100')
        ->assertSee('Gedeeltelijk fundament')
        ->assertSee('Start met module 1: Introductie PERMA en welbevinden →')
        ->assertDontSee('Aanbevolen startlaag')
        ->assertDontSee('4 / 20')
        ->assertDontSee('Je sterke kanten blijven onbenut.');
});
