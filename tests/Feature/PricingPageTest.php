<?php

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
