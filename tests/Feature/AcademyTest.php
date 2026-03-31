<?php

use App\Models\AcademyCourse;
use App\Models\User;

test('guests are redirected to the login page when visiting the academy', function () {
    $response = $this->get(route('academy.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the academy catalog', function () {
    $user = User::factory()->create();
    $course = AcademyCourse::factory()->create([
        'path' => 'academy-courses/adaptability-foundations',
        'title' => [
            'nl' => 'Adaptability Fundamentals',
            'en' => 'Adaptability Fundamentals',
            'de' => 'Adaptability Fundamentals',
            'fr' => 'Adaptability Fundamentals',
        ],
    ]);

    $response = $this->actingAs($user)->get(route('academy.index'));

    $response->assertOk()
        ->assertSee(__('hermes.academy.heading'))
        ->assertSee($course->titleForLocale())
        ->assertSee('/academy-courses/adaptability-foundations/index.html', false);
});

test('dashboard links authenticated users to the academy catalog', function () {
    $user = User::factory()->create();
    AcademyCourse::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee(route('academy.index', absolute: false), false)
        ->assertSee(__('hermes.dashboard.academy_title'));
});
