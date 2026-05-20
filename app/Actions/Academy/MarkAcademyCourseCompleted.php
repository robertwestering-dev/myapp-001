<?php

namespace App\Actions\Academy;

use App\Models\AcademyCourse;
use App\Models\AcademyCourseProgress;
use App\Models\User;

class MarkAcademyCourseCompleted
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __invoke(User $user, AcademyCourse $academyCourse, array $metadata = []): AcademyCourseProgress
    {
        $now = now();

        $progress = AcademyCourseProgress::query()->firstOrNew([
            'user_id' => $user->id,
            'academy_course_id' => $academyCourse->id,
        ]);

        if (! $progress->exists || $progress->started_at === null) {
            $progress->started_at = $now;
        }

        $progress->forceFill([
            'status' => AcademyCourseProgress::STATUS_COMPLETED,
            'locale' => app()->getLocale(),
            'last_seen_at' => $now,
            'completed_at' => $progress->completed_at ?? $now,
            'metadata' => array_filter($metadata),
        ])->save();

        return $progress;
    }
}
