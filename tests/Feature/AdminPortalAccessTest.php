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
        ->assertSee($admin->email)
        ->assertSee('Welkom terug, beheerder.')
        ->assertSee(SyncAdaptabilityAceQuestionnaire::TITLE)
        ->assertSee(SyncDigitalResilienceQuickScanQuestionnaire::TITLE)
        ->assertSee('Baseline assessments')
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('/?contact=1#contact', false)
        ->assertSee('Français')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('managers can visit the admin portal', function () {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)->get(route('admin.portal'));

    $response->assertOk()
        ->assertSee('Admin-portal')
        ->assertSee($manager->email)
        ->assertSee(SyncDigitalResilienceQuickScanQuestionnaire::TITLE);
});

test('non admins cannot visit the admin portal', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.portal'));

    $response->assertForbidden();
});
