<?php

use App\Mail\ContactFormSubmitted;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('home page can be rendered', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertDontSee('U bent niet ingelogd')
        ->assertSee('Maak digitale transformatie begrijpelijk, meetbaar en menselijk.')
        ->assertSee('Diensten')
        ->assertSee('Contact')
        ->assertSee('Quick scan adaptability van medewerkers')
        ->assertSee('Quick scan digitale weerbaarheid')
        ->assertSee('Verandermanagement bij digitale transformatie')
        ->assertSee('Naam')
        ->assertSee('Emailadres')
        ->assertSee('Bericht')
        ->assertSee('Ik ga akkoord met verwerking van mijn gegevens om contact op te nemen.')
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('https://calendly.com/robertwestering/30min')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('guests can see the login link on the home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee(route('login'))
        ->assertDontSee(route('register'))
        ->assertSee('Inloggen');
});

test('authenticated users still see the guest homepage text on the home page', function () {
    $response = $this->actingAs(User::factory()->create())->get(route('home'));

    $response->assertRedirect(route('dashboard'));
});

test('guests can submit the contact form', function () {
    Mail::fake();

    $this->post(route('contact.store'), [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'message' => 'Ik wil meer weten over de quick scan adaptability.',
        'privacy_consent' => '1',
    ])
        ->assertRedirect(route('home').'#contact')
        ->assertSessionHas('status', 'Bedankt voor uw bericht. We nemen zo snel mogelijk contact met u op.');

    Mail::assertSent(ContactFormSubmitted::class, 'robert.van.westering@outlook.com');

    /** @var ContactFormSubmitted $mail */
    $mail = Mail::sent(ContactFormSubmitted::class)->first();
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
