<?php

namespace Database\Seeders;

use App\Models\AcademyCourse;
use Illuminate\Database\Seeder;

class AcademyCourseSeeder extends Seeder
{
    public function run(): void
    {
        AcademyCourse::query()
            ->whereIn('slug', $this->deprecatedCourseSlugs())
            ->delete();
    }

    /**
     * @return array<int, string>
     */
    protected function deprecatedCourseSlugs(): array
    {
        return [
            'adaptability-foundations',
            'digital-resilience-basics',
        ];
    }
}
