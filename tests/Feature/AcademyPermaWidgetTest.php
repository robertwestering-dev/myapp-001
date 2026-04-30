<?php

use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Support\Carbon;

test('guests are redirected when opening the academy perma widget', function () {
    $this->get(route('academy.widgets.perma-scores'))
        ->assertRedirect(route('login'));
});

test('academy perma widget shows the latest positive foundation result for the authenticated user', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->pro()->create([
        'org_id' => $organization->org_id,
    ]);

    $questionnaire = Questionnaire::query()->firstOrCreate(
        ['title' => SyncPositiveFoundationQuestionnaire::TITLE],
        Questionnaire::factory()->make([
            'title' => SyncPositiveFoundationQuestionnaire::TITLE,
        ])->toArray(),
    );

    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
        'submitted_at' => now()->subDays(3),
        'analysis_snapshot' => positiveFoundationSnapshot(
            score: 42,
            profileLabel: 'Fragiel fundament',
            recommendedDimensionLabel: 'Relaties',
        ),
    ]);

    QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
        'submitted_at' => now()->subDay(),
        'analysis_snapshot' => positiveFoundationSnapshot(
            score: 76,
            profileLabel: 'Sterk fundament',
            recommendedDimensionLabel: 'Betekenis',
        ),
    ]);

    $otherUser = User::factory()->pro()->create([
        'org_id' => $organization->org_id,
    ]);

    QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $otherUser->id,
        'submitted_at' => now(),
        'analysis_snapshot' => positiveFoundationSnapshot(
            score: 10,
            profileLabel: 'Fragiel fundament',
            recommendedDimensionLabel: 'Positieve emotie',
        ),
    ]);

    $this->actingAs($user)
        ->get(route('academy.widgets.perma-scores'))
        ->assertOk()
        ->assertSee('compact-perma-widget', false)
        ->assertSee(__('hermes.academy.perma_widget.title'))
        ->assertSee('76 / 100')
        ->assertSee('Sterk fundament')
        ->assertSee('Betekenis')
        ->assertSee('widget-dimension__status--strong', false)
        ->assertSee('widget-dimension__status--partial', false)
        ->assertDontSee('widget-dimension__recommended', false)
        ->assertDontSee('Start here')
        ->assertDontSee('42 / 100')
        ->assertDontSee(__('hermes.dashboard.logout'))
        ->assertDontSee(__('hermes.nav.questionnaires'));
});

test('academy perma widget shows a compact empty state when no positive foundation result exists', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('academy.widgets.perma-scores'))
        ->assertOk()
        ->assertSee(__('hermes.academy.perma_widget.empty_title'))
        ->assertSee(__('hermes.academy.perma_widget.empty_text'))
        ->assertDontSee(__('hermes.dashboard.logout'));
});

function positiveFoundationSnapshot(int $score, string $profileLabel, string $recommendedDimensionLabel): array
{
    return [
        'analyzer_key' => 'positive_foundation',
        'analyzer_version' => 1,
        'title' => 'Compact PERMA-overzicht',
        'summary' => 'Snapshot van de meest recente uitslag.',
        'profile_key' => str_contains(strtolower($profileLabel), 'sterk') ? 'strong' : 'fragile',
        'profile_label' => $profileLabel,
        'score' => $score,
        'max_score' => 100,
        'recommended_dimension_key' => strtolower($recommendedDimensionLabel),
        'recommended_dimension_label' => $recommendedDimensionLabel,
        'recommended_action_label' => null,
        'recommended_action_href' => null,
        'dimensions' => [
            [
                'key' => 'p',
                'label' => 'Positieve emotie',
                'score' => 15,
                'max_score' => 20,
                'status_key' => 'strong',
                'status_label' => 'Sterk fundament',
                'summary' => 'Goed',
                'action_label' => null,
                'action_href' => null,
                'is_recommended' => false,
            ],
            [
                'key' => 'e',
                'label' => 'Betrokkenheid',
                'score' => 13,
                'max_score' => 20,
                'status_key' => 'partial',
                'status_label' => 'Gedeeltelijk fundament',
                'summary' => 'Redelijk',
                'action_label' => null,
                'action_href' => null,
                'is_recommended' => $recommendedDimensionLabel === 'Betrokkenheid',
            ],
            [
                'key' => 'r',
                'label' => 'Relaties',
                'score' => 11,
                'max_score' => 20,
                'status_key' => 'partial',
                'status_label' => 'Gedeeltelijk fundament',
                'summary' => 'Redelijk',
                'action_label' => null,
                'action_href' => null,
                'is_recommended' => $recommendedDimensionLabel === 'Relaties',
            ],
            [
                'key' => 'm',
                'label' => 'Betekenis',
                'score' => 18,
                'max_score' => 20,
                'status_key' => 'strong',
                'status_label' => 'Sterk fundament',
                'summary' => 'Sterk',
                'action_label' => null,
                'action_href' => null,
                'is_recommended' => $recommendedDimensionLabel === 'Betekenis',
            ],
            [
                'key' => 'a',
                'label' => 'Accomplishment',
                'score' => 19,
                'max_score' => 20,
                'status_key' => 'strong',
                'status_label' => 'Sterk fundament',
                'summary' => 'Sterk',
                'action_label' => null,
                'action_href' => null,
                'is_recommended' => false,
            ],
        ],
    ];
}
