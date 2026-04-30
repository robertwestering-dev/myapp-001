<?php

use App\Models\User;

test('guests are redirected when opening the academy strengths widget', function () {
    $this->get(route('academy.widgets.strengths'))
        ->assertRedirect(route('login'));
});

test('academy strengths widget shows the saved strengths for the authenticated user', function () {
    $user = User::factory()->create([
        'selected_strengths' => ['aanpassingsvermogen', 'doorzettingsvermogen', 'teamwerk'],
    ]);

    $this->actingAs($user)
        ->get(route('academy.widgets.strengths'))
        ->assertOk()
        ->assertSee('academy-strengths-widget', false)
        ->assertSee(__('hermes.academy.strengths_widget.title'))
        ->assertSee(__('hermes.academy.strengths_widget.options.aanpassingsvermogen'))
        ->assertSee(__('hermes.academy.strengths_widget.options.creativiteit'))
        ->assertSee(__('hermes.academy.strengths_widget.options.teamwerk'))
        ->assertSee('value="aanpassingsvermogen"', false)
        ->assertSee('checked', false)
        ->assertDontSee(__('hermes.dashboard.logout'));
});

test('academy strengths widget stores exactly three selected strengths', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('academy.widgets.strengths.store'), [
            'selected_strengths' => ['nieuwsgierigheid', 'vriendelijkheid', 'zelfbeheersing'],
        ])
        ->assertRedirect(route('academy.widgets.strengths'))
        ->assertSessionHas('status', __('hermes.academy.strengths_widget.saved'));

    expect($user->fresh()->selected_strengths)->toBe([
        'nieuwsgierigheid',
        'vriendelijkheid',
        'zelfbeheersing',
    ]);
});

test('academy strengths widget rejects invalid selections', function () {
    $user = User::factory()->create([
        'selected_strengths' => ['aanpassingsvermogen', 'doorzettingsvermogen', 'teamwerk'],
    ]);

    $this->actingAs($user)
        ->from(route('academy.widgets.strengths'))
        ->post(route('academy.widgets.strengths.store'), [
            'selected_strengths' => ['creativiteit', 'creativiteit'],
        ])
        ->assertRedirect(route('academy.widgets.strengths'))
        ->assertSessionHasErrors('selected_strengths');

    expect($user->fresh()->selected_strengths)->toBe([
        'aanpassingsvermogen',
        'doorzettingsvermogen',
        'teamwerk',
    ]);
});
