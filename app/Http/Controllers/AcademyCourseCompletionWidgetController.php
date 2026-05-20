<?php

namespace App\Http\Controllers;

use App\Actions\Academy\MarkAcademyCourseCompleted;
use App\Models\AcademyCourse;
use App\Models\AcademyCourseProgress;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcademyCourseCompletionWidgetController extends Controller
{
    public function show(Request $request, AcademyCourse $academyCourse): View
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null || ! $user->hasVerifiedEmail()) {
            return view('academy.empty-widget');
        }

        abort_unless($academyCourse->is_active, 404);
        abort_unless($academyCourse->canBeLaunchedBy($user), 403);

        $progress = AcademyCourseProgress::query()
            ->whereBelongsTo($user)
            ->whereBelongsTo($academyCourse)
            ->first();

        return view('academy.course-completion-widget', [
            'course' => $academyCourse,
            'isCompleted' => $progress?->isCompleted() ?? false,
        ]);
    }

    public function store(
        Request $request,
        AcademyCourse $academyCourse,
        MarkAcademyCourseCompleted $markAcademyCourseCompleted,
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        abort_unless($academyCourse->is_active, 404);
        abort_unless($academyCourse->canBeLaunchedBy($user), 403);

        $markAcademyCourseCompleted($user, $academyCourse, [
            'source' => 'academy-completion-widget',
            'event' => 'academy-course-completed',
        ]);

        return redirect()
            ->route('academy.widgets.course-completion', $academyCourse->slug)
            ->with('status', __('hermes.academy.course_completion_widget.saved'));
    }
}
