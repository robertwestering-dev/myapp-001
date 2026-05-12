<?php

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Support\Carbon;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create([
        'name' => 'Volledige Naam',
        'first_name' => 'Robert',
    ]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk()
        ->assertSee('Persoonlijk dashboard van Robert')
        ->assertSee('Logout')
        ->assertSee(__('hermes.dashboard.questionnaires_title'))
        ->assertSee(__('hermes.dashboard.academy_title'))
        ->assertSeeInOrder([
            __('hermes.dashboard.questionnaires_title'),
            __('hermes.dashboard.academy_title'),
        ])
        ->assertSee(__('hermes.dashboard.questionnaires_text'))
        ->assertSee(__('hermes.dashboard.questionnaires_available_count'))
        ->assertSee(__('hermes.dashboard.questionnaires_in_progress_count'))
        ->assertSee(__('hermes.dashboard.questionnaires_completed_count'))
        ->assertSee(__('hermes.dashboard.academy_available_count'))
        ->assertSee(__('hermes.dashboard.academy_in_progress_count'))
        ->assertSee(__('hermes.dashboard.academy_completed_count'))
        ->assertSee('user-panel', false)
        ->assertSee('user-page-heading', false)
        ->assertSee('user-section-heading', false)
        ->assertSee('user-action-row', false)
        ->assertSee('user-inline-meta', false)
        ->assertSee('user-surface-card', false)
        ->assertSee('user-stat-tile', false)
        ->assertSee('user-guidance-card', false)
        ->assertSee('.user-surface-card--accent .user-section-heading h2', false)
        ->assertSee('.user-guidance-card--accent .user-guidance-card__body strong', false)
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('dashboard welcome falls back to the full name when first name is missing', function () {
    $user = User::factory()->create([
        'name' => 'Volledige Naam',
        'first_name' => null,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Persoonlijk dashboard van Volledige Naam');
});

test('home page uses the hermes favicon instead of the laravel default icons', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('rel="icon"', false)
        ->assertSee('/favicon.png?v=', false)
        ->assertSee('type="image/png"', false)
        ->assertSee('rel="alternate icon"', false)
        ->assertSee('/favicon.ico?v=', false)
        ->assertSee('rel="shortcut icon"', false)
        ->assertSee('/apple-touch-icon.png?v=', false);
});

test('dashboard uses the hermes favicon instead of the laravel default icons', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('rel="icon"', false)
        ->assertSee('/favicon.png?v=', false)
        ->assertSee('type="image/png"', false)
        ->assertSee('rel="alternate icon"', false)
        ->assertSee('/favicon.ico?v=', false)
        ->assertSee('rel="shortcut icon"', false)
        ->assertSee('/apple-touch-icon.png?v=', false);
});

test('dashboard header keeps blog and profile out of the primary menu without the booking button', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('questionnaires.index', absolute: false), false)
        ->assertSee(__('hermes.nav.questionnaires'))
        ->assertSee(__('hermes.nav.academy'))
        ->assertSee(__('hermes.nav.forum'))
        ->assertDontSee('<a href="'.route('blog.index').'"', false)
        ->assertDontSee('<a href="'.route('profile.edit').'"', false)
        ->assertDontSee(route('admin.portal', absolute: false), false)
        ->assertDontSee(__('hermes.header.booking'));
});

test('authenticated dashboard header shows the account hover menu links', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('class="user-menu"', false)
        ->assertSee('aria-label="Open gebruikersmenu"', false)
        ->assertSee('class="user-menu__icon"', false)
        ->assertDontSee('class="header-utility-link"', false)
        ->assertSee(route('dashboard', absolute: false), false)
        ->assertSee(route('journal.timeline', absolute: false), false)
        ->assertSee(route('profile.edit', absolute: false), false)
        ->assertSee(route('blog.index', absolute: false), false)
        ->assertSee(route('contact.show', absolute: false), false)
        ->assertSee('<a class="user-menu__item" href="'.route('profile.edit').'"', false)
        ->assertSee('<a class="user-menu__item" href="'.route('blog.index').'"', false)
        ->assertSee(__('hermes.dashboard.title'))
        ->assertSee(__('hermes.journal.timeline_page_title'))
        ->assertSee(__('hermes.nav.profile'))
        ->assertSee(__('hermes.nav.blog'))
        ->assertSee(__('hermes.nav.contact'));
});

test('authenticated mobile header stays compact and gives the menu its own scroll area', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('.topbar__inner {', false)
        ->assertSee('flex-direction: row;', false)
        ->assertSee('.mobile-menu[open] .mobile-menu__panel', false)
        ->assertSee('overflow-y: auto;', false)
        ->assertSee('overscroll-behavior: contain;', false);
});

test('authenticated mobile header includes the account submenu links inside the hamburger panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('class="mobile-menu__submenu mobile-menu__account"', false)
        ->assertSee('class="mobile-menu__nav mobile-menu__account-list"', false)
        ->assertSee('aria-label="Gebruikersmenu mobiel"', false)
        ->assertSee('Persoonlijk')
        ->assertSee('<a class="mobile-menu__account-link" href="'.route('dashboard').'"', false)
        ->assertSee('<a class="mobile-menu__account-link" href="'.route('journal.timeline').'"', false)
        ->assertSee('<a class="mobile-menu__account-link" href="'.route('profile.edit').'"', false)
        ->assertSee('<a class="mobile-menu__account-link" href="'.route('blog.index').'"', false)
        ->assertSee('<a class="mobile-menu__account-link" href="'.route('contact.show', absolute: false).'"', false)
        ->assertDontSee('class="mobile-menu__link"', false)
        ->assertSee(__('hermes.journal.timeline_page_title'))
        ->assertSee(__('hermes.nav.profile'))
        ->assertSee(__('hermes.nav.blog'))
        ->assertSee(__('hermes.nav.contact'));
});

test('admins are redirected from dashboard to the admin portal', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertRedirect(route('admin.portal'));
});

test('admin users see a final admin portal link in the shared user menu', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('forum.index'))
        ->assertOk()
        ->assertSee(route('admin.portal', absolute: false), false)
        ->assertSee(__('hermes.admin_menu.portal'));
});

test('dashboard shows questionnaire summary counts instead of the questionnaire list', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme Scan',
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    QuestionnaireResponse::factory()->draft()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
        'last_saved_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('hermes.dashboard.questionnaires_in_progress_count'))
        ->assertSee('1')
        ->assertSee(__('hermes.dashboard.questionnaires_action'))
        ->assertDontSee('Werkritme Scan');
});

test('dashboard questionnaire counts include all available questionnaires regardless of active user language', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'en',
    ]);
    $englishQuestionnaire = Questionnaire::factory()->create([
        'title' => 'Work rhythm scan',
        'locale' => 'en',
    ]);
    $dutchQuestionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme scan',
        'locale' => 'nl',
    ]);

    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $englishQuestionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $dutchQuestionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSee(__('hermes.dashboard.questionnaires_available_count'))
        ->assertSee('2')
        ->assertViewHas('availableQuestionnaireCount', 2);
});

test('dashboard uses the session locale for questionnaire context when the profile locale is empty', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => null,
    ]);
    $englishQuestionnaire = Questionnaire::factory()->create([
        'title' => 'Work rhythm scan',
        'locale' => 'en',
    ]);

    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $englishQuestionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->withSession(['locale' => 'en'])
        ->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('availableQuestionnaireCount', 1);
});
