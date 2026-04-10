<?php

test('inspiration sources page can be rendered with public navigation and cta', function () {
    $response = $this->get(route('inspiration-sources.show'));

    $response->assertOk()
        ->assertSee('Op wiens schouders wij staan')
        ->assertSeeText('Martin Seligman')
        ->assertSeeText('Barry O\'Reilly')
        ->assertSeeText('Nick van Dam')
        ->assertSeeText('Viktor Frankl')
        ->assertSeeText('Ontdek waar jij staat. Doe de gratis weerbaarheidsscan.')
        ->assertSeeTextInOrder([
            'Positieve psychologie',
            'Barry O\'Reilly',
            'Leren en leiderschap',
            'Nick van Dam',
            'Filosofie',
            'Viktor Frankl',
        ])
        ->assertSee(route('inspiration-sources.show', absolute: false), false)
        ->assertSee(route('register', absolute: false), false)
        ->assertSee('"@type": "WebPage"', false);
});

test('homepage and submenu link to the inspiration sources page', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee(route('inspiration-sources.show', absolute: false), false)
        ->assertSee('Nieuwsgierig naar de denkers achter Hermes Results?')
        ->assertSee('Bekijk de inspiratiebronnen')
        ->assertSeeInOrder([
            'Inspiratiebronnen',
            'Over ons',
            'Prijzen',
            __('hermes.footer.privacy'),
        ]);
});
