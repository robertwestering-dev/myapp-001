<?php

use App\Models\AcademyCourse;
use App\Models\AcademyCourseProgress;
use App\Models\User;

test('guests see an empty response when opening the course completion widget', function () {
    $course = AcademyCourse::factory()->create([
        'slug' => 'adaptability-foundations',
    ]);

    $this->get(route('academy.widgets.course-completion', $course->slug))
        ->assertOk()
        ->assertDontSee('academy-course-completion-widget', false);
});

test('authenticated users can open the course completion widget', function () {
    $user = User::factory()->create();
    $course = AcademyCourse::factory()->create([
        'slug' => 'adaptability-foundations',
    ]);

    $this->actingAs($user)
        ->get(route('academy.widgets.course-completion', $course->slug))
        ->assertOk()
        ->assertSee('academy-course-completion-widget', false)
        ->assertSee(__('hermes.academy.course_completion_widget.submit'));
});

test('course completion widget marks the academy course as completed', function () {
    $user = User::factory()->create();
    $course = AcademyCourse::factory()->create([
        'slug' => 'digital-resilience-basics',
    ]);

    $this->actingAs($user)
        ->post(route('academy.widgets.course-completion.store', $course->slug))
        ->assertRedirect(route('academy.widgets.course-completion', $course->slug))
        ->assertSessionHas('status', __('hermes.academy.course_completion_widget.saved'));

    $progress = AcademyCourseProgress::query()
        ->where('user_id', $user->id)
        ->where('academy_course_id', $course->id)
        ->firstOrFail();

    expect($progress->status)->toBe(AcademyCourseProgress::STATUS_COMPLETED)
        ->and($progress->started_at)->not->toBeNull()
        ->and($progress->last_seen_at)->not->toBeNull()
        ->and($progress->completed_at)->not->toBeNull()
        ->and($progress->metadata)->toMatchArray([
            'source' => 'academy-completion-widget',
            'event' => 'academy-course-completed',
        ]);
});

test('course completion widget shows completed state after registration', function () {
    $user = User::factory()->create();
    $course = AcademyCourse::factory()->create();

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
        ->get(route('academy.widgets.course-completion', $course->slug))
        ->assertOk()
        ->assertSee(__('hermes.academy.course_completion_widget.completed_badge'))
        ->assertDontSee(__('hermes.academy.course_completion_widget.submit'));
});
