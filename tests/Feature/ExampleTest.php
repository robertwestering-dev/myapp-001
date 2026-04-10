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

    $this->post(route('contact.store'), [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'message' => 'Ik wil meer weten over de quick scan adaptability.',
        'privacy_consent' => '1',
    ])
        ->assertRedirect(route('organizations.landing').'#contact')
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
