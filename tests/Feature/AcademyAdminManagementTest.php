<?php

use App\Models\AcademyCourse;
use App\Models\User;

test('manager cannot access academy course management', function () {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)->get(route('admin.academy-courses.index'));

    $response->assertForbidden();
});

test('non-admin user is forbidden by policy even without middleware', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withoutMiddleware()
        ->get(route('admin.academy-courses.index'))
        ->assertForbidden();
});

test('admin can open academy course management overview', function () {
    $admin = User::factory()->admin()->create();
    $course = AcademyCourse::factory()->create([
        'title' => [
            'nl' => 'Academy Demo',
            'en' => 'Academy Demo',
            'de' => 'Academy Demo',
            'fr' => 'Academy Demo',
        ],
    ]);

    $response = $this->actingAs($admin)->get(route('admin.academy-courses.index'));

    $response->assertOk()
        ->assertDontSee('Academy-catalogus')
        ->assertSee('admin-status-badge', false)
        ->assertSee($course->titleForLocale('nl'));
});

test('admin can create an academy course', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.academy-courses.store'), [
        'slug' => 'academy-admin-course',
        'theme' => AcademyCourse::THEME_ADAPTABILITY,
        'path' => 'academy-courses/academy-admin-course',
        'localized_paths' => [
            'en' => 'EN',
            'de' => 'DE',
        ],
        'estimated_minutes' => 50,
        'sort_order' => 30,
        'is_active' => '1',
        'title' => translatedPayload('Academy Admin Course'),
        'audience' => translatedPayload('Doelgroep voor admins'),
        'goal' => translatedPayload('Doel van de cursus'),
        'summary' => translatedPayload('Samenvatting van de cursus'),
        'learning_goals' => translatedPayload("Leerdoel 1\nLeerdoel 2"),
        'contents' => translatedPayload("Inhoud 1\nInhoud 2"),
    ]);

    $response->assertRedirect();

    expect(AcademyCourse::query()->where('slug', 'academy-admin-course')->exists())->toBeTrue();
    expect(AcademyCourse::query()->where('slug', 'academy-admin-course')->first()->localized_paths)
        ->toBe([
            'en' => 'EN',
            'de' => 'DE',
        ]);
});

function translatedPayload(string $value): array
{
    return collect(array_keys(config('locales.supported', [])))
        ->mapWithKeys(fn (string $locale): array => [$locale => $value.' '.$locale])
        ->all();
}
