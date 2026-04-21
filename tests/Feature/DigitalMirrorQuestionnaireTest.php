<?php

use App\Actions\Questionnaires\SyncDigitalMirrorQuestionnaire;
use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\User;
use Database\Seeders\DigitalMirrorQuestionnaireSeeder;
use Illuminate\Support\Carbon;

test('the digital mirror questionnaire is available in the questionnaire library baseline', function () {
    $this->seed(DigitalMirrorQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->with(['categories.questions'])
        ->where('title', SyncDigitalMirrorQuestionnaire::TITLE)
        ->first();

    expect($questionnaire)->not->toBeNull();
    expect($questionnaire?->locale)->toBe('nl');
    expect($questionnaire?->is_active)->toBeTrue();
    expect($questionnaire?->categories)->toHaveCount(7);
    expect($questionnaire?->categories->pluck('title')->all())->toBe([
        'Positief fundament',
        'Groeimindset en grit',
        'Weerbaarheid',
        'Stress en het brein',
        'Zelfleiderschap',
        'Afleren en aanpassen',
        'Digitale weerbaarheid in de praktijk',
    ]);
    expect($questionnaire?->categories->sum(fn ($category) => $category->questions->count()))->toBe(84);
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->where('locale', 'nl')->count() === 4))->toBeTrue();
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->where('locale', 'en')->count() === 4))->toBeTrue();
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->where('locale', 'de')->count() === 4))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->type === QuestionnaireQuestion::TYPE_LIKERT_SCALE))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->is_required))->toBeTrue();
    expect($questionnaire?->questions->where('locale', 'nl')->every(fn (QuestionnaireQuestion $question) => $question->options === [
        'Zeer mee oneens',
        'Mee oneens',
        'Neutraal',
        'Mee eens',
        'Zeer mee eens',
    ]))->toBeTrue();
    expect($questionnaire?->questions->where('locale', 'en')->every(fn (QuestionnaireQuestion $question) => $question->options === [
        'Strongly disagree',
        'Disagree',
        'Neutral',
        'Agree',
        'Strongly agree',
    ]))->toBeTrue();
    expect($questionnaire?->questions->where('locale', 'de')->every(fn (QuestionnaireQuestion $question) => $question->options === [
        'Stimme überhaupt nicht zu',
        'Stimme nicht zu',
        'Neutral',
        'Stimme zu',
        'Stimme voll und ganz zu',
    ]))->toBeTrue();
});

test('seeding the digital mirror questionnaire twice does not create duplicates', function () {
    $this->seed(DigitalMirrorQuestionnaireSeeder::class);
    $this->seed(DigitalMirrorQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->withCount(['categories', 'questions'])
        ->where('title', SyncDigitalMirrorQuestionnaire::TITLE)
        ->firstOrFail();

    expect(Questionnaire::query()->where('title', SyncDigitalMirrorQuestionnaire::TITLE)->count())->toBe(1);
    expect($questionnaire->categories_count)->toBe(7);
    expect($questionnaire->questions_count)->toBe(84);
});

test('digital mirror renders english content for english users while keeping one questionnaire record', function () {
    $this->seed(DigitalMirrorQuestionnaireSeeder::class);

    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'en',
    ]);
    $questionnaire = Questionnaire::query()
        ->where('title', SyncDigitalMirrorQuestionnaire::TITLE)
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
        ->assertSee(SyncDigitalMirrorQuestionnaire::ENGLISH_TITLE)
        ->assertSee('This questionnaire maps how you view your own digital growth')
        ->assertDontSee('Deze vragenlijst brengt in kaart');

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee(SyncDigitalMirrorQuestionnaire::ENGLISH_TITLE)
        ->assertSee('Positive foundation')
        ->assertSee('I know which activities give me energy and consciously make room for them.')
        ->assertSee('Strongly agree')
        ->assertDontSee('Positief fundament')
        ->assertDontSee('Ik weet welke activiteiten mij energie geven');

    expect(Questionnaire::query()->where('title', SyncDigitalMirrorQuestionnaire::TITLE)->count())->toBe(1);
});

test('digital mirror renders german content for german users while keeping one questionnaire record', function () {
    $this->seed(DigitalMirrorQuestionnaireSeeder::class);

    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'de',
    ]);
    $questionnaire = Questionnaire::query()
        ->where('title', SyncDigitalMirrorQuestionnaire::TITLE)
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
        ->assertSee(SyncDigitalMirrorQuestionnaire::GERMAN_TITLE)
        ->assertSee('Dieser Fragebogen zeigt, wie Sie Ihr eigenes digitales Wachstum')
        ->assertDontSee('This questionnaire maps how you view your own digital growth')
        ->assertDontSee('Deze vragenlijst brengt in kaart');

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee(SyncDigitalMirrorQuestionnaire::GERMAN_TITLE)
        ->assertSee('Positive Grundlage')
        ->assertSee('Ich weiß, welche Aktivitäten mir Energie geben, und schaffe bewusst Raum dafür.')
        ->assertSee('Stimme voll und ganz zu')
        ->assertDontSee('Positive foundation')
        ->assertDontSee('Positief fundament')
        ->assertDontSee('I know which activities give me energy')
        ->assertDontSee('Ik weet welke activiteiten mij energie geven');

    expect(Questionnaire::query()->where('title', SyncDigitalMirrorQuestionnaire::TITLE)->count())->toBe(1);
});
