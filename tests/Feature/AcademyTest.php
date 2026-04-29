<?php

use App\Models\AcademyCourse;
use App\Models\User;
use Illuminate\Support\Facades\File;

test('guests are redirected to the login page when visiting the academy', function () {
    $response = $this->get(route('academy.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the academy catalog', function () {
    $user = User::factory()->create([
        'name' => 'Volledige Naam',
        'first_name' => 'Robert',
    ]);
    $course = AcademyCourse::factory()->create([
        'path' => 'academy-courses/adaptability-foundations',
        'title' => [
            'nl' => 'Adaptability Fundamentals',
            'en' => 'Adaptability Fundamentals',
            'de' => 'Adaptability Fundamentals',
            'fr' => 'Adaptability Fundamentals',
        ],
    ]);
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Adaptability Fundamentals</body></html>');

    $response = $this->actingAs($user)->get(route('academy.index'));

    $response->assertOk()
        ->assertSee(route('dashboard', absolute: false), false)
        ->assertSee(__('hermes.dashboard.title'))
        ->assertSee(__('hermes.nav.questionnaires'))
        ->assertSee(__('hermes.nav.blog'))
        ->assertSee(__('hermes.nav.profile'))
        ->assertSee(__('hermes.dashboard.logout'))
        ->assertSee('user-panel', false)
        ->assertSee('user-section-heading', false)
        ->assertSee('user-action-row', false)
        ->assertSee('user-meta-grid', false)
        ->assertSee('user-meta-item', false)
        ->assertSee('academy-content', false)
        ->assertSee(__('hermes.academy.eyebrow'))
        ->assertSee(__('hermes.academy.sidebar_title'))
        ->assertSee(__('hermes.academy.sidebar_text'))
        ->assertSee($course->titleForLocale())
        ->assertSee('user-surface-card', false)
        ->assertSee(__('hermes.academy.more_info'))
        ->assertSee(__('hermes.academy.open_course'))
        ->assertDontSee(__('hermes.academy.heading'))
        ->assertDontSee('Robert')
        ->assertDontSee($user->email)
        ->assertSee('#academy-course-'.$course->slug, false)
        ->assertSee(route('academy-courses.show', ['academyCoursePath' => $course->contentRouteSegment()], absolute: false), false);
});

test('dashboard links authenticated users to the academy catalog', function () {
    $user = User::factory()->create();
    AcademyCourse::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee(route('academy.index', absolute: false), false)
        ->assertSee(__('hermes.dashboard.academy_title'));
});

test('academy shows a clear empty state with a dashboard action when there are no courses', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('academy.index'))
        ->assertOk()
        ->assertSee(__('hermes.academy.empty_title'))
        ->assertSee(__('hermes.academy.empty_text'))
        ->assertSee(__('hermes.academy.empty_action'))
        ->assertSee(route('dashboard', absolute: false), false)
        ->assertSee('user-guidance-card', false);
});

test('guests are redirected to the login page when opening academy course content', function () {
    $course = AcademyCourse::factory()->create();
    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Course</body></html>');

    $this->get(route('academy-courses.show', ['academyCoursePath' => $course->contentRouteSegment()]))
        ->assertRedirect(route('login'));
});
