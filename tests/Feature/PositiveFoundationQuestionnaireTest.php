<?php

use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\User;
use Database\Seeders\PositiveFoundationQuestionnaireSeeder;
use Illuminate\Support\Carbon;

test('the positive foundation questionnaire is available in the questionnaire library baseline', function () {
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->with(['categories.questions'])
        ->where('title', SyncPositiveFoundationQuestionnaire::TITLE)
        ->first();

    expect($questionnaire)->not->toBeNull();
    expect($questionnaire?->locale)->toBe('nl');
    expect($questionnaire?->is_active)->toBeTrue();
    expect($questionnaire?->categories)->toHaveCount(5);
    expect($questionnaire?->categories->pluck('title')->all())->toBe([
        'Positieve emotie',
        'Betrokkenheid',
        'Relaties',
        'Zingeving',
        'Voldoening',
    ]);
    expect($questionnaire?->categories->sum(fn ($category) => $category->questions->count()))->toBe(60);
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->where('locale', 'nl')->count() === 4))->toBeTrue();
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->where('locale', 'en')->count() === 4))->toBeTrue();
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->where('locale', 'de')->count() === 4))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->type === QuestionnaireQuestion::TYPE_LIKERT_SCALE))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->is_required))->toBeTrue();
    expect($questionnaire?->questions->where('locale', 'nl')->every(fn (QuestionnaireQuestion $question) => $question->options === [
        'Nooit / niet',
        'Zelden',
        'Soms',
        'Vaak',
        'Altijd / Volledig',
    ]))->toBeTrue();
    expect($questionnaire?->questions->where('locale', 'en')->every(fn (QuestionnaireQuestion $question) => $question->options === [
        'Never / not at all',
        'Rarely',
        'Sometimes',
        'Often',
        'Always / fully',
    ]))->toBeTrue();
    expect($questionnaire?->questions->where('locale', 'de')->every(fn (QuestionnaireQuestion $question) => $question->options === [
        'Nie / gar nicht',
        'Selten',
        'Manchmal',
        'Oft',
        'Immer / vollständig',
    ]))->toBeTrue();
    expect($questionnaire?->questions->pluck('prompt')->all())->toContain(
        'Hoe vaak voel je je tevreden met hoe je dag verloopt?',
        'Hoe vaak heb je het gevoel dat je vooruitgaat - hoe klein ook?',
        'How often do you feel satisfied with how your day is going?',
        'Wie oft bist du zufrieden damit, wie dein Tag verläuft?',
    );
});

test('seeding the positive foundation questionnaire twice does not create duplicates', function () {
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->withCount(['categories', 'questions'])
        ->where('title', SyncPositiveFoundationQuestionnaire::TITLE)
        ->firstOrFail();

    expect(Questionnaire::query()->where('title', SyncPositiveFoundationQuestionnaire::TITLE)->count())->toBe(1);
    expect($questionnaire->categories_count)->toBe(5);
    expect($questionnaire->questions_count)->toBe(60);
});

test('positive foundation renders english content for english users while keeping one questionnaire record', function () {
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);

    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'en',
    ]);
    $questionnaire = Questionnaire::query()
        ->where('title', SyncPositiveFoundationQuestionnaire::TITLE)
        ->firstOrFail();

    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee(SyncPositiveFoundationQuestionnaire::ENGLISH_TITLE)
        ->assertSee('This questionnaire maps the positive foundation')
        ->assertDontSee('Deze vragenlijst brengt het positieve fundament');

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee(SyncPositiveFoundationQuestionnaire::ENGLISH_TITLE)
        ->assertSee('Positive emotion')
        ->assertSee('How often do you feel satisfied with how your day is going?')
        ->assertSee('Always / fully')
        ->assertDontSee('Positieve emotie')
        ->assertDontSee('Hoe vaak voel je je tevreden met hoe je dag verloopt?');

    expect(Questionnaire::query()->where('title', SyncPositiveFoundationQuestionnaire::TITLE)->count())->toBe(1);
});

test('positive foundation renders german content for german users while keeping one questionnaire record', function () {
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);

    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'de',
    ]);
    $questionnaire = Questionnaire::query()
        ->where('title', SyncPositiveFoundationQuestionnaire::TITLE)
        ->firstOrFail();

    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee(SyncPositiveFoundationQuestionnaire::GERMAN_TITLE)
        ->assertSee('Dieser Fragebogen erfasst das positive Fundament')
        ->assertDontSee('This questionnaire maps the positive foundation')
        ->assertDontSee('Deze vragenlijst brengt het positieve fundament');

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee(SyncPositiveFoundationQuestionnaire::GERMAN_TITLE)
        ->assertSee('Positive Emotion')
        ->assertSee('Wie oft bist du zufrieden damit, wie dein Tag verläuft?')
        ->assertSee('Immer / vollständig')
        ->assertDontSee('Positive emotion')
        ->assertDontSee('Positieve emotie')
        ->assertDontSee('How often do you feel satisfied with how your day is going?')
        ->assertDontSee('Hoe vaak voel je je tevreden met hoe je dag verloopt?');

    expect(Questionnaire::query()->where('title', SyncPositiveFoundationQuestionnaire::TITLE)->count())->toBe(1);
});
