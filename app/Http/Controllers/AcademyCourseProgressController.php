<?php

namespace App\Http\Controllers;

use App\Actions\Academy\MarkAcademyCourseCompleted;
use App\Models\AcademyCourse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademyCourseProgressController extends Controller
{
    public function complete(
        Request $request,
        AcademyCourse $academyCourse,
        MarkAcademyCourseCompleted $markAcademyCourseCompleted,
    ): JsonResponse {
        $user = $request->user();

        abort_unless($academyCourse->is_active, 404);
        abort_unless($user !== null && $academyCourse->canBeLaunchedBy($user), 403);

        $markAcademyCourseCompleted($user, $academyCourse, [
            'source' => 'ispring',
            'event' => $request->string('event')->toString() ?: null,
        ]);

        return response()->json(['status' => 'completed']);
    }
}
