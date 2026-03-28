<?php

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\QuestionnaireResponseAnswer;
use App\Models\User;

test('admins can view questionnaire response reports across organizations', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Atlas BV',
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkbeleving',
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

    $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.index'))
        ->assertOk()
        ->assertSee('Ingevulde questionnaires')
        ->assertSee('Werkbeleving')
        ->assertSee('Atlas BV')
        ->assertSee('Anna Gebruiker')
        ->assertSee('Bekijk response');

    $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.show', $response))
        ->assertOk()
        ->assertSee('Response van Anna Gebruiker');
});

test('admins can filter questionnaire response reports by questionnaire organization and user', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Atlas BV',
    ]);
    $otherOrganization = Organization::factory()->create([
        'naam' => 'Nova BV',
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkbeleving',
    ]);
    $otherQuestionnaire = Questionnaire::factory()->create([
        'title' => 'Vitaliteit',
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
    ]);
    $otherAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $otherQuestionnaire->id,
        'org_id' => $otherOrganization->org_id,
    ]);
    $user = User::factory()->create([
        'name' => 'Anna Gebruiker',
        'org_id' => $organization->org_id,
    ]);
    $otherUser = User::factory()->create([
        'name' => 'Bram Gebruiker',
        'org_id' => $otherOrganization->org_id,
    ]);

    QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
    ]);
    QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $otherAvailability->id,
        'user_id' => $otherUser->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.index', [
            'questionnaire_id' => $questionnaire->id,
            'org_id' => $organization->org_id,
            'user_id' => $user->id,
        ]))
        ->assertOk()
        ->assertSee('Werkbeleving')
        ->assertSee('Anna Gebruiker')
        ->assertSee('Resultaten 1 t/m 1 van 1');
});

test('managers only see responses from their own organization', function () {
    $organization = Organization::factory()->create([
        'naam' => 'Eigen Org',
    ]);
    $otherOrganization = Organization::factory()->create([
        'naam' => 'Andere Org',
    ]);
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkbeleving',
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
    ]);
    $otherAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $otherOrganization->org_id,
    ]);
    $ownUser = User::factory()->create([
        'name' => 'Eigen Gebruiker',
        'org_id' => $organization->org_id,
    ]);
    $otherUser = User::factory()->create([
        'name' => 'Andere Gebruiker',
        'org_id' => $otherOrganization->org_id,
    ]);
    $ownResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $ownUser->id,
    ]);
    $otherResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $otherAvailability->id,
        'user_id' => $otherUser->id,
    ]);

    $this->actingAs($manager)
        ->get(route('admin.questionnaire-responses.index'))
        ->assertOk()
        ->assertSee('Eigen Gebruiker')
        ->assertDontSee('Andere Gebruiker')
        ->assertDontSee('Andere Org');

    $this->actingAs($manager)
        ->get(route('admin.questionnaire-responses.show', $ownResponse))
        ->assertOk();

    $this->actingAs($manager)
        ->get(route('admin.questionnaire-responses.show', $otherResponse))
        ->assertForbidden();
});

test('response detail page shows stored answers', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create();
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

    $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.show', $response))
        ->assertOk()
        ->assertSee('Algemeen')
        ->assertSee('Hoe gaat het?')
        ->assertSee('Goed.');
});

test('regular users cannot access questionnaire response reports', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.questionnaire-responses.index'))
        ->assertForbidden();
});

test('admins can export questionnaire response data as csv', function () {
    $admin = User::factory()->admin()->create();
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

    $export = $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.export', [
            'questionnaire_id' => $questionnaire->id,
        ]));

    $export->assertDownload("questionnaire-responses-{$questionnaire->id}.csv");

    $content = $export->streamedContent();

    expect($content)
        ->toContain('Questionnaire,Organisatie,Gebruiker,Emailadres,"Ingezonden op",Categorie,Vraag,Antwoord')
        ->toContain('Werkbeleving,"Atlas BV","Anna Gebruiker",anna@example.com')
        ->toContain('Algemeen,"Hoe gaat het?",Goed.');
});

test('export requires a selected questionnaire', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.export'))
        ->assertRedirect(route('admin.questionnaire-responses.index'))
        ->assertSessionHasErrors('questionnaire_id');
});

test('managers can only export csv data for their own organization scope', function () {
    $organization = Organization::factory()->create([
        'naam' => 'Eigen Org',
    ]);
    $otherOrganization = Organization::factory()->create([
        'naam' => 'Andere Org',
    ]);
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
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
    $ownAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
    ]);
    $otherAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $otherOrganization->org_id,
    ]);
    $ownUser = User::factory()->create([
        'name' => 'Eigen Gebruiker',
        'email' => 'eigen@example.com',
        'org_id' => $organization->org_id,
    ]);
    $otherUser = User::factory()->create([
        'name' => 'Andere Gebruiker',
        'email' => 'ander@example.com',
        'org_id' => $otherOrganization->org_id,
    ]);
    $ownResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $ownAvailability->id,
        'user_id' => $ownUser->id,
    ]);
    $otherResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $otherAvailability->id,
        'user_id' => $otherUser->id,
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $ownResponse->id,
        'questionnaire_question_id' => $question->id,
        'answer' => 'Eigen antwoord',
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $otherResponse->id,
        'questionnaire_question_id' => $question->id,
        'answer' => 'Ander antwoord',
    ]);

    $export = $this->actingAs($manager)
        ->get(route('admin.questionnaire-responses.export', [
            'questionnaire_id' => $questionnaire->id,
        ]));

    $content = $export->streamedContent();

    expect($content)
        ->toContain('Eigen Gebruiker')
        ->toContain('Eigen antwoord')
        ->not->toContain('Andere Gebruiker')
        ->not->toContain('Ander antwoord');
});

test('admins can export questionnaire response summary data as one row per response', function () {
    $admin = User::factory()->admin()->create();
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
    $firstQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoe gaat het?',
        'sort_order' => 1,
    ]);
    $secondQuestion = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoeveel energie heeft u?',
        'sort_order' => 2,
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
        'questionnaire_question_id' => $firstQuestion->id,
        'answer' => 'Goed.',
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $response->id,
        'questionnaire_question_id' => $secondQuestion->id,
        'answer' => 'Soms',
    ]);

    $export = $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.export-summary', [
            'questionnaire_id' => $questionnaire->id,
        ]));

    $export->assertDownload("questionnaire-responses-summary-{$questionnaire->id}.csv");

    $content = $export->streamedContent();

    expect($content)
        ->toContain('Questionnaire,Organisatie,Gebruiker,Emailadres,"Ingezonden op","Hoe gaat het?","Hoeveel energie heeft u?"')
        ->toContain('Werkbeleving,"Atlas BV","Anna Gebruiker",anna@example.com')
        ->toContain('Goed.,Soms');
});

test('admins can export questionnaire response statistics per question and option', function () {
    $admin = User::factory()->admin()->create();
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
    $textQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoe gaat het?',
        'type' => QuestionnaireQuestion::TYPE_SHORT_TEXT,
        'sort_order' => 1,
    ]);
    $choiceQuestion = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoeveel energie heeft u?',
        'sort_order' => 2,
        'options' => ['Altijd', 'Soms', 'Nooit'],
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
    ]);
    $firstUser = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $secondUser = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $firstResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $firstUser->id,
    ]);
    $secondResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $secondUser->id,
    ]);

    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $firstResponse->id,
        'questionnaire_question_id' => $textQuestion->id,
        'answer' => 'Goed',
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $secondResponse->id,
        'questionnaire_question_id' => $textQuestion->id,
        'answer' => 'Prima',
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $firstResponse->id,
        'questionnaire_question_id' => $choiceQuestion->id,
        'answer' => 'Soms',
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $secondResponse->id,
        'questionnaire_question_id' => $choiceQuestion->id,
        'answer' => 'Altijd',
    ]);

    $export = $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.export-stats', [
            'questionnaire_id' => $questionnaire->id,
        ]));

    $export->assertDownload("questionnaire-responses-stats-{$questionnaire->id}.csv");

    $content = $export->streamedContent();

    expect($content)
        ->toContain('Questionnaire,Categorie,Vraag,Vraagtype,"Totaal ingevuld",Optie,Aantal')
        ->toContain('Werkbeleving,Algemeen,"Hoe gaat het?","Korte tekst",2,,')
        ->toContain('Werkbeleving,Algemeen,"Hoeveel energie heeft u?","Enkele keuze",2,Altijd,1')
        ->toContain('Werkbeleving,Algemeen,"Hoeveel energie heeft u?","Enkele keuze",2,Soms,1')
        ->toContain('Werkbeleving,Algemeen,"Hoeveel energie heeft u?","Enkele keuze",2,Nooit,0');
});

test('admins can view questionnaire response statistics in the admin portal', function () {
    $admin = User::factory()->admin()->create();
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
    $textQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoe gaat het?',
        'type' => QuestionnaireQuestion::TYPE_SHORT_TEXT,
        'sort_order' => 1,
    ]);
    $choiceQuestion = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoeveel energie heeft u?',
        'sort_order' => 2,
        'options' => ['Altijd', 'Soms', 'Nooit'],
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
    ]);
    $firstUser = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $secondUser = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $firstResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $firstUser->id,
    ]);
    $secondResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $secondUser->id,
    ]);

    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $firstResponse->id,
        'questionnaire_question_id' => $textQuestion->id,
        'answer' => 'Goed',
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $secondResponse->id,
        'questionnaire_question_id' => $textQuestion->id,
        'answer' => 'Prima',
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $firstResponse->id,
        'questionnaire_question_id' => $choiceQuestion->id,
        'answer' => 'Soms',
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $secondResponse->id,
        'questionnaire_question_id' => $choiceQuestion->id,
        'answer' => 'Altijd',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.stats', [
            'questionnaire_id' => $questionnaire->id,
        ]))
        ->assertOk()
        ->assertSee('Statistieken voor Werkbeleving')
        ->assertSee('Responses in de huidige selectie')
        ->assertSee('Hoeveel energie heeft u?')
        ->assertSee('Altijd')
        ->assertSee('1 · 50%')
        ->assertSee('Soms')
        ->assertSee('Goed')
        ->assertSee('Prima');
});

test('managers only see questionnaire statistics for their own organization', function () {
    $organization = Organization::factory()->create([
        'naam' => 'Eigen Org',
    ]);
    $otherOrganization = Organization::factory()->create([
        'naam' => 'Andere Org',
    ]);
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkbeleving',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Algemeen',
    ]);
    $question = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoeveel energie heeft u?',
        'options' => ['Altijd', 'Soms', 'Nooit'],
    ]);
    $ownAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
    ]);
    $otherAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $otherOrganization->org_id,
    ]);
    $ownUser = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $otherUser = User::factory()->create([
        'org_id' => $otherOrganization->org_id,
    ]);
    $ownResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $ownAvailability->id,
        'user_id' => $ownUser->id,
    ]);
    $otherResponse = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $otherAvailability->id,
        'user_id' => $otherUser->id,
    ]);

    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $ownResponse->id,
        'questionnaire_question_id' => $question->id,
        'answer' => 'Altijd',
    ]);
    QuestionnaireResponseAnswer::factory()->create([
        'questionnaire_response_id' => $otherResponse->id,
        'questionnaire_question_id' => $question->id,
        'answer' => 'Nooit',
    ]);

    $this->actingAs($manager)
        ->get(route('admin.questionnaire-responses.stats', [
            'questionnaire_id' => $questionnaire->id,
        ]))
        ->assertOk()
        ->assertSee('Responses in de huidige selectie')
        ->assertSee('Altijd')
        ->assertSee('1 · 100%')
        ->assertSee('Nooit')
        ->assertDontSee('Andere Org');
});

test('statistics view requires a selected questionnaire', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.stats'))
        ->assertRedirect(route('admin.questionnaire-responses.index'))
        ->assertSessionHasErrors('questionnaire_id');
});
