<?php

use App\Models\User;

test('questionnaire pages keep unsafe eval enabled for livewire expressions', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertHeader('content-security-policy');

    expect($response->headers->get('content-security-policy'))
        ->toContain("script-src 'nonce-")
        ->toContain("'strict-dynamic'")
        ->toContain("'unsafe-eval'");
});

test('academy course content keeps its relaxed academy-specific csp', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('academy.widgets.perma-scores'));

    $response->assertOk()
        ->assertHeader('content-security-policy');

    expect($response->headers->get('content-security-policy'))
        ->toContain("script-src 'nonce-")
        ->toContain("'unsafe-eval'");
});
