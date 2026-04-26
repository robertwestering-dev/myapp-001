<?php

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use App\Actions\Questionnaires\SyncDigitalResilienceQuickScanQuestionnaire;
use App\Models\User;

test('guests are redirected to the login page for the admin portal', function () {
    $response = $this->get(route('admin.portal'));

    $response->assertRedirect(route('login'));
});

test('admins can visit the admin portal', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.portal'));

    $response->assertOk()
        ->assertSee('Admin-portal')
        ->assertSee('Welkom terug, beheerder.')
        ->assertSee('Beheer')
        ->assertSee('Forum')
        ->assertSee(route('forum.index', absolute: false), false)
        ->assertSee(route('admin.users.index', absolute: false), false)
        ->assertSee(route('admin.organizations.index', absolute: false), false)
        ->assertSee(route('admin.questionnaires.index', absolute: false), false)
        ->assertSee(route('admin.questionnaire-responses.index', absolute: false), false)
        ->assertSee(route('admin.strategy-pages.index', absolute: false), false)
        ->assertSee(route('admin.media-assets.index', absolute: false), false)
        ->assertSee(route('admin.academy-courses.index', absolute: false), false)
        ->assertSee(route('admin.blog-posts.index', absolute: false), false)
        ->assertSee(route('admin.translations.index', absolute: false), false)
        ->assertSee(SyncAdaptabilityAceQuestionnaire::TITLE)
        ->assertSee(SyncDigitalResilienceQuickScanQuestionnaire::TITLE)
        ->assertSee('Baseline assessments')
        ->assertDontSee('hero__side', false)
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('managers can visit the admin portal', function () {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)->get(route('admin.portal'));

    $response->assertOk()
        ->assertSee('Admin-portal')
        ->assertSee('Beheer')
        ->assertSee('Forum')
        ->assertSee(route('forum.index', absolute: false), false)
        ->assertSee(route('admin.users.index', absolute: false), false)
        ->assertSee(route('admin.organizations.index', absolute: false), false)
        ->assertSee(route('admin.questionnaires.index', absolute: false), false)
        ->assertSee(route('admin.questionnaire-responses.index', absolute: false), false)
        ->assertDontSee(route('admin.strategy-pages.index', absolute: false), false)
        ->assertDontSee(route('admin.media-assets.index', absolute: false), false)
        ->assertDontSee(route('admin.academy-courses.index', absolute: false), false)
        ->assertDontSee(route('admin.blog-posts.index', absolute: false), false)
        ->assertDontSee(route('admin.translations.index', absolute: false), false)
        ->assertSee(SyncDigitalResilienceQuickScanQuestionnaire::TITLE);
});

test('non admins cannot visit the admin portal', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.portal'));

    $response->assertForbidden();
});
