<?php

use App\Mail\ContactFormSubmitted;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('home page can be rendered', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertDontSee('U bent niet ingelogd')
        ->assertSee(__('hermes.home_people.hero_title'))
        ->assertSee(__('hermes.home_people.hero_primary'))
        ->assertSee(__('hermes.home_people.challenge_1_title'))
        ->assertSeeInOrder([
            __('hermes.home_people.tool_2_title'),
            __('hermes.home_people.tool_1_title'),
            __('hermes.home_people.tool_3_title'),
        ])
        ->assertSeeInOrder([
            __('hermes.home_people.confidence_2_title'),
            __('hermes.home_people.confidence_3_title'),
            __('hermes.home_people.confidence_1_title'),
        ])
        ->assertSee('home-organization-card home-organization-card--accent', false)
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('Nederlands')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('guests can see the login and register links on the home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee(route('login'))
        ->assertSee(route('register'))
        ->assertSee('Inloggen');
});

test('home page includes a mobile navigation menu with submenu links and locale switch', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Open navigatiemenu')
        ->assertSee('<details class="home-menu-dropdown">', false)
        ->assertSee('<details class="mobile-menu__submenu">', false)
        ->assertSee('Inspiratiebronnen')
        ->assertSee('Over ons')
        ->assertSee('Prijzen')
        ->assertSee(__('hermes.footer.privacy'))
        ->assertSee(route('contact.show', absolute: false), false)
        ->assertSee('Taal')
        ->assertSee(route('locale.update'), false)
        ->assertSee('Nederlands')
        ->assertSee('English')
        ->assertSee('Deutsch');
});

test('contact page can be rendered with the contact form only', function () {
    $response = $this->get(route('contact.show'));

    $response->assertOk()
        ->assertSee(__('hermes.contact_page.heading'))
        ->assertSee(__('hermes.header.booking'))
        ->assertSee(route('contact.store'), false)
        ->assertSee(__('hermes.home.contact_submit'))
        ->assertSee('contact-page', false)
        ->assertSee('closing__panel', false)
        ->assertDontSee(__('hermes.home.closing_title'))
        ->assertDontSee(__('hermes.home.closing_text'))
        ->assertDontSee(__('hermes.home.hero_primary'));
});

test('the booking header button is only shown on the contact page for visitors', function (string $routeName, bool $shouldShowBooking) {
    $response = $this->get(route($routeName));

    $response->assertOk();

    if ($shouldShowBooking) {
        $response->assertSee(__('hermes.header.booking'));

        return;
    }

    $response->assertDontSee(__('hermes.header.booking'));
})->with([
    'home' => ['home', false],
    'contact' => ['contact.show', true],
    'inspiration sources' => ['inspiration-sources.show', false],
    'about' => ['about.show', false],
    'pricing' => ['pricing.show', false],
    'privacy' => ['privacy.show', false],
    'organizations' => ['organizations.landing', false],
    'blog' => ['blog.index', false],
]);

test('public guest pages show the contact navigation link', function (string $routeName) {
    $this->get(route($routeName))
        ->assertOk()
        ->assertSee(route('contact.show', absolute: false), false)
        ->assertSee(__('hermes.nav.contact'));
})->with([
    'home',
    'contact.show',
    'inspiration-sources.show',
    'about.show',
    'pricing.show',
    'privacy.show',
    'organizations.landing',
    'blog.index',
]);

test('public contact links open the contact page at the top', function () {
    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('href="'.route('contact.show').'"', false)
        ->assertDontSee('href="'.route('contact.show').'#contact"', false);

    $this->get(route('privacy.show'))
        ->assertOk()
        ->assertSee(route('contact.show'), false)
        ->assertDontSee(route('contact.show').'#contact', false);
});

test('authenticated users are redirected to dashboard from the home page', function () {
    $response = $this->actingAs(User::factory()->create())->get(route('home'));

    $response->assertRedirect(route('dashboard'));
});

test('authenticated users can still open the homepage when the contact parameter is passed', function () {
    $response = $this->actingAs(User::factory()->create())->get(route('home', ['contact' => 1]));

    $response->assertOk()
        ->assertSee(__('hermes.home_people.hero_title'));
});

test('guests can submit the contact form', function () {
    Mail::fake();

    $this->from(route('contact.show').'#contact')->post(route('contact.store'), [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'message' => 'Ik wil meer weten over de quick scan adaptability.',
        'privacy_consent' => '1',
    ])
        ->assertRedirect(route('contact.show').'#contact')
        ->assertSessionHas('status', __('hermes.home.contact_success'));

    Mail::assertQueued(ContactFormSubmitted::class, 'robert.van.westering@outlook.com');

    /** @var ContactFormSubmitted $mail */
    $mail = Mail::queued(ContactFormSubmitted::class)->first();
    $replyTo = $mail->replyTo[0] ?? null;

    expect($replyTo['address'] ?? null)->toBe('ada@example.com');
    expect($replyTo['name'] ?? null)->toBe('Ada Lovelace');
    expect($mail->name)->toBe('Ada Lovelace');
    expect($mail->email)->toBe('ada@example.com');
    expect($mail->messageBody)->toBe('Ik wil meer weten over de quick scan adaptability.');
    expect($mail->consentGiven)->toBeTrue();
});

test('contact form validates required fields', function () {
    $this->from(route('home').'#contact')
        ->post(route('contact.store'), [])
        ->assertRedirect(route('home').'#contact')
        ->assertSessionHasErrors([
            'name',
            'email',
            'message',
            'privacy_consent',
        ]);
});
