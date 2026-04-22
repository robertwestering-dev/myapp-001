<?php

use App\Models\User;

test('pricing page can be rendered with individual and organization packages', function () {
    $response = $this->get(route('pricing.show'));

    $response->assertOk()
        ->assertSee(__('hermes.pricing_page.hero_title'))
        ->assertSee('helpen je op jouw persoonlijke weg')
        ->assertDontSee('jeop')
        ->assertSee(__('hermes.pricing_page.personal_free.name'))
        ->assertSee(__('hermes.pricing_page.personal_pro.name'))
        ->assertSee('<s>'.__('hermes.pricing_page.personal_pro.price').'</s> '.__('hermes.pricing_page.personal_pro.temporary_free_label'), false)
        ->assertDontSee(__('hermes.pricing_page.organizations_eyebrow'))
        ->assertDontSee(__('hermes.pricing_page.organization_tiers.starter.name'))
        ->assertDontSee(__('hermes.pricing_page.organization_tiers.business.name'))
        ->assertDontSee(__('hermes.pricing_page.organization_tiers.enterprise.name'))
        ->assertSee(route('register', absolute: false), false)
        ->assertSee('"@type": "WebPage"', false);
});

test('guest cannot view the pro upgrade page', function () {
    $this->get(route('pro-upgrade.show'))
        ->assertRedirectToRoute('login');
});

test('logged in user can view the pro upgrade page with the temporary pro offer', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_USER,
    ]);

    $response = $this->actingAs($user)->get(route('pro-upgrade.show'));

    $response->assertOk()
        ->assertSee(__('hermes.pro_upgrade_page.hero_title'))
        ->assertSee(__('hermes.pro_upgrade_page.hero_intro'))
        ->assertSee(__('hermes.pricing_page.personal_pro.name'))
        ->assertSee('<s>'.__('hermes.pricing_page.personal_pro.price').'</s> <b>'.__('hermes.pro_upgrade_page.free_label').'</b>', false)
        ->assertSee(__('hermes.pro_upgrade_page.temporary_tagline'))
        ->assertDontSee('Minder dan €1 per week, voor wie echt wil groeien')
        ->assertSee(__('hermes.pricing_page.personal_pro.features')[0])
        ->assertSee(route('pro-upgrade.store', absolute: false), false)
        ->assertDontSee(route('register', absolute: false), false);
});

test('logged in user can upgrade their account to pro from the pro upgrade page', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_USER,
    ]);

    $this->actingAs($user)
        ->get(route('pro-upgrade.show'))
        ->assertOk()
        ->assertSee(route('pro-upgrade.store', absolute: false), false)
        ->assertDontSee(route('register', absolute: false), false);

    $this->actingAs($user)
        ->post(route('pro-upgrade.store'))
        ->assertRedirect(route('pro-upgrade.show'))
        ->assertSessionHas('status', __('hermes.pro_upgrade_page.upgraded_status'));

    expect($user->fresh()->role)->toBe(User::ROLE_USER_PRO);
});

test('pro upgrade action does not change admin or manager roles', function (string $role) {
    $user = User::factory()->create([
        'role' => $role,
    ]);

    $this->actingAs($user)
        ->post(route('pro-upgrade.store'))
        ->assertRedirect(route('pro-upgrade.show'));

    expect($user->fresh()->role)->toBe($role);
})->with([
    User::ROLE_ADMIN,
    User::ROLE_MANAGER,
    User::ROLE_USER_PRO,
]);

test('visitor submenu links to the dedicated pricing page', function () {
    $response = $this->get(route('about.show'));

    $response->assertOk()
        ->assertSee(route('pricing.show', absolute: false), false)
        ->assertSeeInOrder([
            'Inspiratiebronnen',
            'Over ons',
            'Prijzen',
            __('hermes.footer.privacy'),
        ]);
});
