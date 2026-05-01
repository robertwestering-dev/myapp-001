<?php

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Support\Carbon;

test('guests are redirected when opening the weekly intention widget', function () {
    $this->get(route('academy.widgets.weekly-intention'))
        ->assertRedirect(route('login'));
});

test('pro users can open the compact weekly intention widget', function () {
    Carbon::setTestNow('2026-05-02 09:30:00');

    $user = User::factory()->pro()->create([
        'selected_strengths' => ['teamwerk', 'leiderschap', 'nieuwsgierigheid'],
    ]);

    $this->actingAs($user)
        ->get(route('academy.widgets.weekly-intention'))
        ->assertOk()
        ->assertSee('academy-weekly-intention-widget', false)
        ->assertSee(__('hermes.journal.types.weekly_intention.fields.strength_key'))
        ->assertSee(__('hermes.journal.types.weekly_intention.fields.planned_strength_use'))
        ->assertSee(__('hermes.academy.strengths_widget.options.teamwerk'))
        ->assertSee('value="2026-05-02"', false)
        ->assertSee(__('hermes.academy.weekly_intention_widget.placeholders.planned_strength_use'))
        ->assertDontSee(__('hermes.academy.weekly_intention_widget.date_hint'))
        ->assertDontSee(__('hermes.journal.types.weekly_intention.selected_strengths_hint'))
        ->assertDontSee(__('hermes.journal.types.weekly_intention.fields.general_intention'))
        ->assertDontSee(__('hermes.academy.weekly_intention_widget.placeholders.general_intention'))
        ->assertDontSee(__('hermes.dashboard.logout'));
});

test('regular users are redirected from the weekly intention widget to the pro upgrade page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('academy.widgets.weekly-intention'))
        ->assertRedirect(route('pro-upgrade.show'));
});

test('pro users can save their weekly intention entry from the widget', function () {
    Carbon::setTestNow('2026-05-02 09:30:00');

    $user = User::factory()->pro()->create([
        'selected_strengths' => ['teamwerk', 'leiderschap', 'nieuwsgierigheid'],
    ]);

    $this->actingAs($user)
        ->post(route('academy.widgets.weekly-intention.store'), [
            'entry_date' => '2026-05-02',
            'entry_type' => JournalEntry::TYPE_WEEKLY_INTENTION,
            'content' => [
                'strength_key' => 'teamwerk',
                'planned_strength_use' => 'Ik plan elke dag een korte afstemming met mijn team.',
            ],
        ])
        ->assertRedirect(route('academy.widgets.weekly-intention'))
        ->assertSessionHas('status', __('hermes.academy.weekly_intention_widget.saved'));

    $entry = $user->journalEntries()->firstOrFail();

    expect($entry)
        ->entry_type->toBe(JournalEntry::TYPE_WEEKLY_INTENTION)
        ->entry_date->toDateString()->toBe('2026-05-02')
        ->contentValue('strength_key')->toBe('teamwerk')
        ->contentValue('planned_strength_use')->toBe('Ik plan elke dag een korte afstemming met mijn team.');
});

test('the widget prefills todays existing weekly intention entry', function () {
    Carbon::setTestNow('2026-05-02 09:30:00');

    $user = User::factory()->pro()->create([
        'selected_strengths' => ['teamwerk', 'leiderschap', 'nieuwsgierigheid'],
    ]);

    JournalEntry::factory()->weeklyIntention()->create([
        'user_id' => $user->getKey(),
        'entry_date' => '2026-05-02',
        'content' => [
            'strength_key' => 'nieuwsgierigheid',
            'planned_strength_use' => 'Ik reserveer elke ochtend tijd om iets nieuws te onderzoeken.',
            'general_intention' => 'Ik wil mijn week opener en leergerichter starten.',
        ],
    ]);

    $this->actingAs($user)
        ->get(route('academy.widgets.weekly-intention'))
        ->assertOk()
        ->assertSee('Ik reserveer elke ochtend tijd om iets nieuws te onderzoeken.')
        ->assertDontSee('<label for="general_intention">', false)
        ->assertDontSee('<textarea id="general_intention"', false)
        ->assertSee('value="nieuwsgierigheid"', false);
});
