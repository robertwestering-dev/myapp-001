<?php

namespace App\Http\Controllers;

use App\Models\AcademyCourse;
use App\Models\AcademyCourseProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademyController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $courses = AcademyCourse::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('academy.index', [
            'courses' => $courses,
            'progressByCourseId' => AcademyCourseProgress::query()
                ->where('user_id', $user?->id)
                ->whereIn('academy_course_id', $courses->pluck('id'))
                ->get()
                ->keyBy('academy_course_id'),
            'user' => $user,
        ]);
    }
}
