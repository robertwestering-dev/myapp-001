<?php

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Support\Carbon;

test('pro users can view only their own journal entries', function () {
    $user = User::factory()->pro()->create();
    $otherUser = User::factory()->pro()->create();

    $visibleEntry = JournalEntry::factory()->create([
        'user_id' => $user->getKey(),
        'entry_date' => '2026-04-28',
        'content' => [
            'what_went_well' => 'Ik bleef rustig in een lastig gesprek.',
            'my_contribution' => 'Ik bereidde het gesprek goed voor.',
        ],
    ]);

    JournalEntry::factory()->strengthsReflection()->create([
        'user_id' => $otherUser->getKey(),
        'content' => [
            'strength_key' => 'teamwerk',
            'situation' => 'Deze tekst mag niet zichtbaar zijn.',
            'how_used' => 'Verborgen bijdrage.',
            'reflection' => 'Verborgen reflectie.',
        ],
    ]);

    $this->actingAs($user)
        ->get(route('journal.index'))
        ->assertOk()
        ->assertSee('Hier kun je op een centrale plaats je dagboek en reflecties bijhouden. Dat geeft je een mooi beeld van de positieve dingen die in je leven gebeuren en de manieren waarop jij jouw sterke punten gebruikt. Dat is belangrijk voor gevoel van welbevinden, ook in moeilijke tijden.')
        ->assertSee('3 goede dingen vandaag')
        ->assertSee('Mijn sterke punten gebruikt')
        ->assertSee('Mijn voornemens')
        ->assertSee('Beschrijf jouw 3 goede dingen van vandaag in dit veld, of neem de tijd en ruimte om voor elk van jouw goede dingen een eigen invoer te maken. De keuze is aan jouw.')
        ->assertSee('Weet u zeker dat u dit item wilt verwijderen?')
        ->assertSee($visibleEntry->contentValue('what_went_well'))
        ->assertSee($visibleEntry->contentValue('my_contribution'))
        ->assertDontSee('Deze tekst mag niet zichtbaar zijn')
        ->assertDontSee('Verborgen reflectie.');
});

test('regular users are redirected to the pro upgrade page for the journal', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('journal.index'))
        ->assertRedirect(route('pro-upgrade.show'));
});

test('pro users can save one journal entry per day and type and update it later', function () {
    $user = User::factory()->pro()->create([
        'selected_strengths' => ['teamwerk', 'leiderschap', 'nieuwsgierigheid'],
    ]);

    $this->actingAs($user)
        ->post(route('journal.store'), [
            'entry_date' => '2026-04-30',
            'entry_type' => JournalEntry::TYPE_THREE_GOOD_THINGS,
            'content' => [
                'what_went_well' => 'Ik hield focus op mijn planning.',
                'my_contribution' => 'Ik blokte tijd in mijn agenda.',
            ],
        ])
        ->assertRedirect(route('journal.index'));

    expect($user->journalEntries()->count())->toBe(1);

    $entry = $user->journalEntries()->firstOrFail();

    $this->actingAs($user)
        ->post(route('journal.store'), [
            'entry_date' => '2026-04-30',
            'entry_type' => JournalEntry::TYPE_THREE_GOOD_THINGS,
            'content' => [
                'what_went_well' => 'Ik hield focus en rondde mijn planning af.',
                'my_contribution' => 'Ik blokte tijd in mijn agenda en stelde prioriteiten.',
            ],
        ])
        ->assertRedirect(route('journal.index'));

    expect($user->journalEntries()->count())->toBe(1);

    $this->actingAs($user)
        ->post(route('journal.store'), [
            'entry_date' => '2026-04-30',
            'entry_type' => JournalEntry::TYPE_STRENGTHS_REFLECTION,
            'content' => [
                'strength_key' => 'teamwerk',
                'situation' => 'We moesten samen snel schakelen.',
                'how_used' => 'Ik bracht overzicht en betrok iedereen.',
                'reflection' => 'Dat gaf rust en maakte ons sneller.',
            ],
        ])
        ->assertRedirect(route('journal.index'));

    $this->actingAs($user)
        ->post(route('journal.store'), [
            'entry_date' => '2026-04-30',
            'entry_type' => JournalEntry::TYPE_WEEKLY_INTENTION,
            'content' => [
                'strength_key' => 'nieuwsgierigheid',
                'planned_strength_use' => 'Ik plan elke dag een verkenmoment voor nieuwe inzichten.',
                'general_intention' => 'Ik wil bewuster ruimte maken om te leren en rust te houden.',
            ],
        ])
        ->assertRedirect(route('journal.index'));

    $this->actingAs($user)
        ->put(route('journal.update', $entry), [
            'entry_date' => '2026-04-30',
            'entry_type' => JournalEntry::TYPE_THREE_GOOD_THINGS,
            'content' => [
                'what_went_well' => 'Ik hield focus en rondde mijn werk af.',
                'my_contribution' => 'Ik blokte tijd in mijn agenda en zette meldingen uit.',
            ],
        ])
        ->assertRedirect(route('journal.index'));

    expect($user->journalEntries()->count())->toBe(3);

    expect($entry->fresh())
        ->not->toBeNull()
        ->entry_date->toDateString()->toBe('2026-04-30')
        ->entry_type->toBe(JournalEntry::TYPE_THREE_GOOD_THINGS)
        ->contentValue('what_went_well')->toBe('Ik hield focus en rondde mijn werk af.')
        ->contentValue('my_contribution')->toBe('Ik blokte tijd in mijn agenda en zette meldingen uit.');
});

test('users cannot update entries that belong to another user', function () {
    $user = User::factory()->pro()->create();
    $otherUser = User::factory()->pro()->create();
    $entry = JournalEntry::factory()->create([
        'user_id' => $otherUser->getKey(),
        'entry_date' => Carbon::parse('2026-04-29')->toDateString(),
    ]);

    $this->actingAs($user)
        ->put(route('journal.update', $entry), [
            'entry_date' => '2026-04-29',
            'entry_type' => JournalEntry::TYPE_THREE_GOOD_THINGS,
            'content' => [
                'what_went_well' => 'Onzichtbare wijziging',
                'my_contribution' => 'Mag niet lukken',
            ],
        ])
        ->assertNotFound();
});

test('pro users can save a strengths reflection entry', function () {
    $user = User::factory()->pro()->create([
        'selected_strengths' => ['leiderschap', 'teamwerk', 'nieuwsgierigheid'],
    ]);

    $this->actingAs($user)
        ->post(route('journal.store'), [
            'entry_date' => '2026-04-27',
            'entry_type' => JournalEntry::TYPE_STRENGTHS_REFLECTION,
            'content' => [
                'strength_key' => 'leiderschap',
                'situation' => 'Er was onduidelijkheid in het team.',
                'how_used' => 'Ik nam initiatief en maakte keuzes expliciet.',
                'reflection' => 'Het hielp om sneller richting te vinden.',
            ],
        ])
        ->assertRedirect(route('journal.index'));

    $entry = $user->journalEntries()->latest('id')->firstOrFail();

    expect($entry->entry_type)->toBe(JournalEntry::TYPE_STRENGTHS_REFLECTION);
    expect($entry->contentValue('strength_key'))->toBe('leiderschap');
    expect($entry->contentValue('reflection'))->toBe('Het hielp om sneller richting te vinden.');
});

test('pro users can save a weekly intention entry', function () {
    $user = User::factory()->pro()->create([
        'selected_strengths' => ['leiderschap', 'teamwerk', 'nieuwsgierigheid'],
    ]);

    $this->actingAs($user)
        ->post(route('journal.store'), [
            'entry_date' => '2026-04-25',
            'entry_type' => JournalEntry::TYPE_WEEKLY_INTENTION,
            'content' => [
                'strength_key' => 'teamwerk',
                'planned_strength_use' => 'Ik begin elk overleg met een korte afstemming op ieders bijdrage.',
                'general_intention' => 'Ik wil deze week meer rust en samenwerking brengen in mijn agenda.',
            ],
        ])
        ->assertRedirect(route('journal.index'));

    $entry = $user->journalEntries()->latest('id')->firstOrFail();

    expect($entry->entry_type)->toBe(JournalEntry::TYPE_WEEKLY_INTENTION);
    expect($entry->contentValue('strength_key'))->toBe('teamwerk');
    expect($entry->contentValue('planned_strength_use'))->toBe('Ik begin elk overleg met een korte afstemming op ieders bijdrage.');
    expect($entry->contentValue('general_intention'))->toBe('Ik wil deze week meer rust en samenwerking brengen in mijn agenda.');
});

test('strengths reflections only accept one of the users three saved strengths', function () {
    $user = User::factory()->pro()->create([
        'selected_strengths' => ['teamwerk', 'leiderschap', 'nieuwsgierigheid'],
    ]);

    $this->actingAs($user)
        ->post(route('journal.store'), [
            'entry_date' => '2026-04-26',
            'entry_type' => JournalEntry::TYPE_STRENGTHS_REFLECTION,
            'content' => [
                'strength_key' => 'vriendelijkheid',
                'situation' => 'Ik hielp iemand verder.',
                'how_used' => 'Ik luisterde goed.',
                'reflection' => 'Dat gaf verbinding.',
            ],
        ])
        ->assertSessionHasErrors('content.strength_key');

    $this->assertDatabaseMissing('three_good_things_entries', [
        'user_id' => $user->getKey(),
        'entry_date' => '2026-04-26',
        'entry_type' => JournalEntry::TYPE_STRENGTHS_REFLECTION,
    ]);
});

test('weekly intentions only accept one of the users three saved strengths', function () {
    $user = User::factory()->pro()->create([
        'selected_strengths' => ['teamwerk', 'leiderschap', 'nieuwsgierigheid'],
    ]);

    $this->actingAs($user)
        ->post(route('journal.store'), [
            'entry_date' => '2026-04-24',
            'entry_type' => JournalEntry::TYPE_WEEKLY_INTENTION,
            'content' => [
                'strength_key' => 'vriendelijkheid',
                'planned_strength_use' => 'Ik ga vaker actief iemand helpen.',
                'general_intention' => 'Ik wil deze week meer aandacht geven aan anderen.',
            ],
        ])
        ->assertSessionHasErrors('content.strength_key');

    $this->assertDatabaseMissing('three_good_things_entries', [
        'user_id' => $user->getKey(),
        'entry_date' => '2026-04-24',
        'entry_type' => JournalEntry::TYPE_WEEKLY_INTENTION,
    ]);
});

test('pro users can delete their own journal entries', function () {
    $user = User::factory()->pro()->create();
    $entry = JournalEntry::factory()->create([
        'user_id' => $user->getKey(),
    ]);

    $this->actingAs($user)
        ->delete(route('journal.destroy', $entry))
        ->assertRedirect(route('journal.index'));

    $this->assertDatabaseMissing('three_good_things_entries', [
        'id' => $entry->getKey(),
    ]);
});
