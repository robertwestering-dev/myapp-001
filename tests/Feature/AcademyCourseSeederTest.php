<?php

use App\Models\AcademyCourse;
use Database\Seeders\AcademyCourseSeeder;

test('academy course seeder removes legacy seeded academy courses', function () {
    AcademyCourse::factory()->create([
        'slug' => 'adaptability-foundations',
        'path' => 'academy-courses/adaptability-foundations',
    ]);

    AcademyCourse::factory()->create([
        'slug' => 'digital-resilience-basics',
        'path' => 'academy-courses/digital-resilience-basics',
    ]);

    $customCourse = AcademyCourse::factory()->create([
        'slug' => 'custom-course',
        'path' => 'academy-courses/custom-course',
    ]);

    $this->seed(AcademyCourseSeeder::class);

    expect(AcademyCourse::query()->pluck('slug')->all())
        ->toBe(['custom-course']);

    $this->assertModelExists($customCourse);
});
