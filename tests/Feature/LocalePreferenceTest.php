<?php

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\QuestionnaireResponseAnswer;
use App\Models\User;

test('guests can switch the application locale and it is stored in the session', function () {
    $this->from(route('home'))
        ->post(route('locale.update'), [
            'locale' => 'en',
        ])
        ->assertRedirect(route('home'))
        ->assertSessionHas('locale', 'en');

    $this->withSession(['locale' => 'en'])
        ->get(route('home'))
        ->assertOk()
        ->assertSee(trans('hermes.home.hero_title', locale: 'en'));
});

test('guest visitors on the .com domain receive the english default locale', function () {
    $this->get('http://hermesresults.com')
        ->assertOk()
        ->assertSee(trans('hermes.home.hero_title', locale: 'en'));
});

test('guest visitors on the .nl domain receive the dutch default locale', function () {
    $this->get('http://hermesresults.nl')
        ->assertOk()
        ->assertSee(trans('hermes.home.hero_title', locale: 'nl'));
});

test('guest visitors on the .eu domain receive the german default locale', function () {
    $this->get('http://hermesresults.eu')
        ->assertOk()
        ->assertSee(trans('hermes.home.hero_title', locale: 'de'));
});

test('session locale overrides the host default locale', function () {
    $this->withSession([
        'locale' => 'fr',
    ])->get('http://hermesresults.com')
        ->assertOk()
        ->assertSee(trans('hermes.home.hero_title', locale: 'fr'));
});

test('authenticated users persist their locale preference and see the translated dashboard', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'de',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Sie sind angemeldet.');

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('locale.update'), [
            'locale' => 'fr',
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('locale', 'fr');

    expect($user->fresh()->locale)->toBe('fr');
});

test('report exports use the active locale for csv headers', function () {
    $admin = User::factory()->admin()->create([
        'locale' => 'en',
    ]);
    $organization = Organization::factory()->create([
        'naam' => 'Atlas BV',
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkbeleving',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Algemeen',
    ]);
    $question = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoe gaat het?',
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
    ]);
    $user = User::factory()->create([
        'name' => 'Anna Gebruiker',
        'email' => 'anna@example.com',
        'org_id' => $organization->org_id,
    ]);
    $response = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $response->id,
        'questionnaire_question_id' => $question->id,
        'answer' => 'Goed.',
    ]);

    $export = $this->withSession(['locale' => 'en'])
        ->actingAs($admin)
        ->get(route('admin.questionnaire-responses.export', [
            'questionnaire_id' => $questionnaire->id,
        ]));

    $export->assertDownload("questionnaire-responses-{$questionnaire->id}.csv");

    expect($export->streamedContent())
        ->toContain('Questionnaire,Organization,User,"Email address","Submitted at",Category,Question,Answer');
});
