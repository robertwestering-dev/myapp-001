<?php

use App\Models\AcademyCourse;
use App\Models\AcademyCourseProgress;
use App\Models\User;
use Illuminate\Support\Facades\File;

test('opening an academy course records in progress state', function () {
    $user = User::factory()->create();
    $course = AcademyCourse::factory()->create([
        'slug' => 'adaptability-foundations',
        'path' => 'academy-courses/adaptability-foundations',
    ]);

    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Course</body></html>');

    $this->actingAs($user)
        ->get(route('academy.courses.launch', $course->slug))
        ->assertOk()
        ->assertSee(route('academy-courses.show', [
            'academyCoursePath' => $course->contentRouteSegment(),
            'asset' => 'index.html',
        ], absolute: false), false)
        ->assertSee('data-completion-url', false)
        ->assertSee('academy-course-completed', false);

    $this->assertDatabaseHas('academy_course_progress', [
        'user_id' => $user->id,
        'academy_course_id' => $course->id,
        'status' => AcademyCourseProgress::STATUS_IN_PROGRESS,
        'locale' => app()->getLocale(),
    ]);
});

test('completion endpoint marks the academy course as completed', function () {
    $user = User::factory()->create();
    $course = AcademyCourse::factory()->create([
        'slug' => 'digital-resilience-basics',
        'path' => 'academy-courses/digital-resilience-basics',
    ]);

    $this->actingAs($user)
        ->postJson(route('academy.courses.complete', $course->slug), [
            'event' => 'academy-course-completed',
        ])
        ->assertOk()
        ->assertJson(['status' => 'completed']);

    $progress = AcademyCourseProgress::query()
        ->where('user_id', $user->id)
        ->where('academy_course_id', $course->id)
        ->firstOrFail();

    expect($progress->status)->toBe(AcademyCourseProgress::STATUS_COMPLETED)
        ->and($progress->started_at)->not->toBeNull()
        ->and($progress->last_seen_at)->not->toBeNull()
        ->and($progress->completed_at)->not->toBeNull()
        ->and($progress->metadata)->toMatchArray([
            'source' => 'ispring',
            'event' => 'academy-course-completed',
        ]);
});

test('dashboard academy counts use course progress', function () {
    $user = User::factory()->create();
    $inProgressCourse = AcademyCourse::factory()->create();
    $completedCourse = AcademyCourse::factory()->create();

    AcademyCourseProgress::query()->create([
        'user_id' => $user->id,
        'academy_course_id' => $inProgressCourse->id,
        'status' => AcademyCourseProgress::STATUS_IN_PROGRESS,
        'locale' => 'nl',
        'started_at' => now(),
        'last_seen_at' => now(),
    ]);

    AcademyCourseProgress::query()->create([
        'user_id' => $user->id,
        'academy_course_id' => $completedCourse->id,
        'status' => AcademyCourseProgress::STATUS_COMPLETED,
        'locale' => 'nl',
        'started_at' => now(),
        'last_seen_at' => now(),
        'completed_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('hermes.dashboard.academy_in_progress_count'))
        ->assertSee(__('hermes.dashboard.academy_completed_count'))
        ->assertSee('>1</', false);
});

test('academy catalog links to the launch wrapper and shows progress badges', function () {
    $user = User::factory()->create();
    $course = AcademyCourse::factory()->create([
        'slug' => 'tracked-course',
        'path' => 'academy-courses/tracked-course',
    ]);

    File::ensureDirectoryExists(dirname($course->contentPath()));
    File::put($course->contentPath(), '<html><body>Course</body></html>');

    AcademyCourseProgress::query()->create([
        'user_id' => $user->id,
        'academy_course_id' => $course->id,
        'status' => AcademyCourseProgress::STATUS_COMPLETED,
        'locale' => 'nl',
        'started_at' => now(),
        'last_seen_at' => now(),
        'completed_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('academy.index'))
        ->assertOk()
        ->assertSee(route('academy.courses.launch', $course->slug, absolute: false), false)
        ->assertSee(__('hermes.academy.status_completed'));
});
