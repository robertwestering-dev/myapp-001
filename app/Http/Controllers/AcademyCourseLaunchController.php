<?php

namespace App\Http\Controllers;

use App\Models\AcademyCourse;
use App\Models\AcademyCourseProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademyCourseLaunchController extends Controller
{
    public function __invoke(Request $request, AcademyCourse $academyCourse): View
    {
        $user = $request->user();

        abort_unless($academyCourse->is_active, 404);
        abort_unless($user !== null && $academyCourse->canBeLaunchedBy($user), 403);
        abort_unless($academyCourse->isAvailable(), 404);

        $progress = AcademyCourseProgress::query()->firstOrNew([
            'user_id' => $user->id,
            'academy_course_id' => $academyCourse->id,
        ]);

        $now = now();

        if (! $progress->exists) {
            $progress->started_at = $now;
            $progress->status = AcademyCourseProgress::STATUS_IN_PROGRESS;
        }

        $progress->locale = app()->getLocale();
        $progress->last_seen_at = $now;
        $progress->save();

        return view('academy.launch', [
            'course' => $academyCourse,
            'courseContentUrl' => $academyCourse->launchUrl(),
        ]);
    }
}
