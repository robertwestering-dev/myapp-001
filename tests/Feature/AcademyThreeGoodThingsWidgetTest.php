<?php

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Support\Carbon;

test('guests are redirected when opening the three good things widget', function () {
    $this->get(route('academy.widgets.three-good-things'))
        ->assertRedirect(route('login'));
});

test('pro users can open the compact three good things widget', function () {
    Carbon::setTestNow('2026-05-01 09:30:00');

    $user = User::factory()->pro()->create();

    $this->actingAs($user)
        ->get(route('academy.widgets.three-good-things'))
        ->assertOk()
        ->assertSee('academy-three-good-things-widget', false)
        ->assertSee(__('hermes.journal.types.three_good_things.fields.what_went_well'))
        ->assertSee(__('hermes.journal.types.three_good_things.fields.my_contribution'))
        ->assertSee('value="2026-05-01"', false)
        ->assertSee('placeholder="Beschrijf concreet wat positief uitpakte."', false)
        ->assertSee('placeholder="Beschrijf concreet wat jij daar zelf voor deed of inbracht."', false)
        ->assertDontSee(__('hermes.academy.three_good_things_widget.today'))
        ->assertDontSee('<h1>'.__('hermes.academy.three_good_things_widget.title').'</h1>', false)
        ->assertDontSee(__('hermes.academy.three_good_things_widget.intro'))
        ->assertDontSee(__('hermes.journal.types.three_good_things.title'))
        ->assertDontSee(__('hermes.journal.types.three_good_things.helper.what_went_well'))
        ->assertDontSee(__('hermes.journal.types.three_good_things.helper.my_contribution'))
        ->assertDontSee(__('hermes.dashboard.logout'));
});

test('regular users are redirected from the three good things widget to the pro upgrade page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('academy.widgets.three-good-things'))
        ->assertRedirect(route('pro-upgrade.show'));
});

test('pro users can save their first three good things entry from the widget', function () {
    Carbon::setTestNow('2026-05-01 09:30:00');

    $user = User::factory()->pro()->create();

    $this->actingAs($user)
        ->post(route('academy.widgets.three-good-things.store'), [
            'entry_date' => '2026-05-01',
            'entry_type' => JournalEntry::TYPE_THREE_GOOD_THINGS,
            'content' => [
                'what_went_well' => 'Ik maakte een heldere start met mijn e-learning.',
                'my_contribution' => 'Ik plande tijd in en begon direct.',
            ],
        ])
        ->assertRedirect(route('academy.widgets.three-good-things'))
        ->assertSessionHas('status', __('hermes.academy.three_good_things_widget.saved'));

    $entry = $user->journalEntries()->firstOrFail();

    expect($entry)
        ->entry_type->toBe(JournalEntry::TYPE_THREE_GOOD_THINGS)
        ->entry_date->toDateString()->toBe('2026-05-01')
        ->contentValue('what_went_well')->toBe('Ik maakte een heldere start met mijn e-learning.')
        ->contentValue('my_contribution')->toBe('Ik plande tijd in en begon direct.');
});

test('the widget prefills todays existing three good things entry', function () {
    Carbon::setTestNow('2026-05-01 09:30:00');

    $user = User::factory()->pro()->create();

    JournalEntry::factory()->create([
        'user_id' => $user->getKey(),
        'entry_date' => '2026-05-01',
        'entry_type' => JournalEntry::TYPE_THREE_GOOD_THINGS,
        'content' => [
            'what_went_well' => 'Ik bleef kalm tijdens een lastige keuze.',
            'my_contribution' => 'Ik nam eerst afstand en koos daarna bewust.',
        ],
    ]);

    $this->actingAs($user)
        ->get(route('academy.widgets.three-good-things'))
        ->assertOk()
        ->assertSee('Ik bleef kalm tijdens een lastige keuze.')
        ->assertSee('Ik nam eerst afstand en koos daarna bewust.');
});
