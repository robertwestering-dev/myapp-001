<?php

test('about page can be rendered with the visitor submenu link', function () {
    $response = $this->get(route('about.show'));

    $response->assertOk()
        ->assertSee(__('hermes.about_page.hero_title'))
        ->assertSee(__('hermes.about_page.story_title'))
        ->assertSee('ruim 35 jaar')
        ->assertDontSee('bijna 40 jaar')
        ->assertSee(__('hermes.about_page.mission_title'))
        ->assertSee(route('about.show', absolute: false), false)
        ->assertSee('Over ons')
        ->assertSee('"@type": "AboutPage"', false);
});

test('public pages link to the dedicated about page from the visitor submenu', function () {
    $response = $this->get(route('blog.index'));

    $response->assertOk()
        ->assertSeeInOrder([
            'Inspiratiebronnen',
            'Over ons',
            'Prijzen',
            __('hermes.footer.privacy'),
        ])
        ->assertSee(route('about.show', absolute: false), false)
        ->assertSee('Over ons');
});
