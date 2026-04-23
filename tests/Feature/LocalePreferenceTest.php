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
        ->assertSee(trans('hermes.home_people.hero_title', locale: 'en'));
});

test('guest visitors on the .com domain receive the dutch default locale', function () {
    $this->get('http://hermesresults.com')
        ->assertOk()
        ->assertSee(trans('hermes.home_people.hero_title', locale: 'nl'));
});

test('guest visitors on the .nl domain receive the dutch default locale', function () {
    $this->get('http://hermesresults.nl')
        ->assertOk()
        ->assertSee(trans('hermes.home_people.hero_title', locale: 'nl'));
});

test('guest visitors on the .eu domain receive the german default locale', function () {
    $this->get('http://hermesresults.eu')
        ->assertOk()
        ->assertSee(trans('hermes.home_people.hero_title', locale: 'de'));
});

test('english and german homepages use the updated localized homepage copy', function (string $locale) {
    $this->withSession(['locale' => $locale])
        ->get(route('home'))
        ->assertOk()
        ->assertSee(trans('hermes.home_people.hero_intro', locale: $locale))
        ->assertSee(trans('hermes.home_people.hero_intro_extra', locale: $locale))
        ->assertSee(trans('hermes.home_people.challenges_eyebrow', locale: $locale))
        ->assertSee(trans('hermes.about_page.story_section_eyebrow', locale: $locale))
        ->assertSee(trans('hermes.about_page.story_title', locale: $locale))
        ->assertSee(trans('hermes.about_page.mission_title', locale: $locale))
        ->assertSee(trans('hermes.home_people.resilience_model_eyebrow', locale: $locale))
        ->assertSee(trans('hermes.home_people.resilience_model_title', locale: $locale))
        ->assertSee(trans('hermes.home_people.tools_text', locale: $locale))
        ->assertSee(trans('hermes.home_people.inspiration_title', locale: $locale))
        ->assertSee(trans('hermes.home_people.inspiration_action', locale: $locale))
        ->assertSee(trans('hermes.nav.about', locale: $locale))
        ->assertSee(trans('hermes.nav.organizations', locale: $locale))
        ->assertDontSee('Nieuwsgierig naar de denkers?')
        ->assertDontSee('Bekijk de inspiratiebronnen');
})->with([
    'english' => 'en',
    'german' => 'de',
]);

test('homepage translation sections stay aligned across localized files', function (string $locale) {
    $dutchTranslations = require lang_path('nl/hermes.php');
    $localizedTranslations = require lang_path("{$locale}/hermes.php");

    expect(array_keys($localizedTranslations['home_people']))->toBe(array_keys($dutchTranslations['home_people']));
    expect(array_keys($localizedTranslations['about_page']))->toBe(array_keys($dutchTranslations['about_page']));
})->with([
    'english' => 'en',
    'german' => 'de',
    'french' => 'fr',
]);

test('session locale overrides the host default locale', function () {
    $this->withSession([
        'locale' => 'de',
    ])->get('http://hermesresults.com')
        ->assertOk()
        ->assertSee(trans('hermes.home_people.hero_title', locale: 'de'));
});

test('lang query parameter overrides the .com host default and is stored in the session', function () {
    $response = $this->get('http://hermesresults.com?lang=en');

    $response->assertOk()
        ->assertSee(trans('hermes.home_people.hero_title', locale: 'en'))
        ->assertSessionHas('locale', 'en');

    $this->withSession(['locale' => 'en'])
        ->get('http://hermesresults.com')
        ->assertOk()
        ->assertSee(trans('hermes.home_people.hero_title', locale: 'en'));
});

test('lang query parameter supports german redirects on the .com domain', function () {
    $this->get('http://hermesresults.com?lang=de')
        ->assertOk()
        ->assertSee(trans('hermes.home_people.hero_title', locale: 'de'))
        ->assertSessionHas('locale', 'de');
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
        ->assertSee(trans('hermes.dashboard.overview_eyebrow', locale: 'de'));

    $this->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('locale.update'), [
            'locale' => 'en',
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('locale', 'en');

    expect($user->fresh()->locale)->toBe('en');
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
