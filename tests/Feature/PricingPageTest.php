<?php

use App\Models\User;

test('pricing page can be rendered with individual and organization packages', function () {
    $response = $this->get(route('pricing.show'));

    $response->assertOk()
        ->assertSee(__('hermes.pricing_page.hero_title'))
        ->assertSee(__('hermes.pricing_page.personal_free.name'))
        ->assertSee(__('hermes.pricing_page.personal_pro.name'))
        ->assertDontSee(__('hermes.pricing_page.organizations_eyebrow'))
        ->assertDontSee(__('hermes.pricing_page.organization_tiers.starter.name'))
        ->assertDontSee(__('hermes.pricing_page.organization_tiers.business.name'))
        ->assertDontSee(__('hermes.pricing_page.organization_tiers.enterprise.name'))
        ->assertSee(route('register', absolute: false), false)
        ->assertSee('"@type": "WebPage"', false);
});

test('pro upgrade page renders the temporary pro offer', function () {
    $response = $this->get(route('pro-upgrade.show'));

    $response->assertOk()
        ->assertSee(__('hermes.pro_upgrade_page.hero_title'))
        ->assertSee(__('hermes.pricing_page.personal_pro.name'))
        ->assertSee('<s>'.__('hermes.pricing_page.personal_pro.price').'</s> <b>'.__('hermes.pro_upgrade_page.free_label').'</b>', false)
        ->assertSee(__('hermes.pro_upgrade_page.temporary_tagline'))
        ->assertDontSee(__('hermes.pricing_page.personal_pro.tagline'))
        ->assertSee(__('hermes.pricing_page.personal_pro.features')[0])
        ->assertSee(route('register', absolute: false), false);
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
