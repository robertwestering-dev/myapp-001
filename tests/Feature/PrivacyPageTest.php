<?php

test('privacy page can be rendered with footer link', function () {
    $response = $this->get(route('privacy.show'));

    $response->assertSuccessful()
        ->assertSee(__('hermes.privacy.hero_title'))
        ->assertSee(__('hermes.privacy.sections.8.title'))
        ->assertSee(route('contact.show', absolute: false), false);
});

test('organization contact form links to the privacy page', function () {
    $response = $this->get(route('organizations.landing'));

    $response->assertSuccessful()
        ->assertSee(route('privacy.show', absolute: false), false)
        ->assertSee(__('hermes.home.contact_privacy_notice', ['url' => route('privacy.show')]), false);
});
