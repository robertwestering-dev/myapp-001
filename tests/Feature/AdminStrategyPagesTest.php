<?php

use App\Models\User;

test('guests are redirected to login for strategy pages', function () {
    $this->get(route('admin.strategy-pages.index'))
        ->assertRedirect(route('login'));

    $this->get(route('admin.strategy-pages.show', 'homepage-copy'))
        ->assertRedirect(route('login'));
});

test('global admins can visit the strategy overview and all strategy pages', function () {
    $admin = User::factory()->admin()->create();

    $overview = $this->actingAs($admin)->get(route('admin.strategy-pages.index'));

    $overview->assertOk()
        ->assertSee('Interne strategiepagina&#039;s', false)
        ->assertSee('Homepage-copy voor Hermes Results')
        ->assertSee('Zakelijke landingspagina voor bedrijfsaccounts')
        ->assertSee('Concept voor pricing en abonnementen')
        ->assertSee('Privacy- en vertrouwensboodschap')
        ->assertSee(route('admin.strategy-pages.show', 'homepage-copy'), false);

    $pages = [
        'homepage-copy' => 'Maak gratis een account aan',
        'zakelijke-landingspagina' => 'Vraag een bedrijfsaccount aan',
        'pricing-en-abonnementen' => 'Pakket 3: Insight Plus',
        'privacy-en-vertrouwen' => 'Wij geloven dat betere digitale ontwikkeling begint met inzicht en vertrouwen.',
    ];

    foreach ($pages as $slug => $expectedText) {
        $this->actingAs($admin)
            ->get(route('admin.strategy-pages.show', $slug))
            ->assertOk()
            ->assertSee($expectedText)
            ->assertSee('Niet zichtbaar op de publieke homepage');
    }
});

test('global admins can open full public previews for strategy pages', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.strategy-pages.preview', 'homepage-copy'))
        ->assertOk()
        ->assertSee('Admin-preview')
        ->assertSee('Word sterker in digitale verandering')
        ->assertSee(__('hermes.nav.services'))
        ->assertSee(__('hermes.nav.contact'))
        ->assertSee(__('hermes.nav.login'));

    $this->actingAs($admin)
        ->get(route('admin.strategy-pages.preview', 'zakelijke-landingspagina'))
        ->assertOk()
        ->assertSee('Maak digitale uitdagingen in je organisatie zichtbaar')
        ->assertSee('Wat organisaties krijgen');
});

test('managers cannot visit strategy pages', function () {
    $manager = User::factory()->manager()->create();

    $this->actingAs($manager)
        ->get(route('admin.strategy-pages.index'))
        ->assertForbidden();

    $this->actingAs($manager)
        ->get(route('admin.strategy-pages.show', 'homepage-copy'))
        ->assertForbidden();

    $this->actingAs($manager)
        ->get(route('admin.strategy-pages.preview', 'homepage-copy'))
        ->assertForbidden();
});
