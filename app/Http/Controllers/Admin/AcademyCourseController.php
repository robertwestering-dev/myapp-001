<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\HandlesLocalizedPayload;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAcademyCourseRequest;
use App\Http\Requests\Admin\UpdateAcademyCourseRequest;
use App\Models\AcademyCourse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcademyCourseController extends Controller
{
    use HandlesLocalizedPayload;

    public function index(): View
    {
        $academyCourses = AcademyCourse::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(config('app.per_page'));

        return view('admin.academy-courses.index', [
            'academyCourses' => $academyCourses,
        ]);
    }

    public function create(): View
    {
        return view('admin.academy-courses.form', [
            'title' => __('hermes.admin.form_titles.new_academy_course'),
            'intro' => 'Voeg een nieuwe e-learning toe aan de Academy-catalogus voor ingelogde gebruikers.',
            'submitLabel' => 'Academy-cursus opslaan',
            'academyCourse' => new AcademyCourse([
                'theme' => AcademyCourse::THEME_ADAPTABILITY,
                'is_active' => true,
                'estimated_minutes' => 30,
                'sort_order' => 0,
            ]),
            'isEditing' => false,
            'themes' => AcademyCourse::themes(),
            'supportedLocales' => config('locales.supported', []),
        ]);
    }

    public function store(StoreAcademyCourseRequest $request): RedirectResponse
    {
        $academyCourse = AcademyCourse::create($this->academyCoursePayload($request));

        return redirect()
            ->route('admin.academy-courses.edit', $academyCourse)
            ->with('status', __('hermes.admin.academy_courses.created'));
    }

    public function edit(AcademyCourse $academyCourse): View
    {
        return view('admin.academy-courses.form', [
            'title' => __('hermes.admin.form_titles.edit_academy_course'),
            'intro' => 'Werk metadata, vertalingen en publicatie-instellingen van deze e-learning bij.',
            'submitLabel' => 'Wijzigingen opslaan',
            'academyCourse' => $academyCourse,
            'isEditing' => true,
            'themes' => AcademyCourse::themes(),
            'supportedLocales' => config('locales.supported', []),
        ]);
    }

    public function update(UpdateAcademyCourseRequest $request, AcademyCourse $academyCourse): RedirectResponse
    {
        $academyCourse->update($this->academyCoursePayload($request));

        return redirect()
            ->route('admin.academy-courses.edit', $academyCourse)
            ->with('status', __('hermes.admin.academy_courses.updated'));
    }

    public function confirmDestroy(AcademyCourse $academyCourse): View
    {
        return view('admin.academy-courses.confirm-delete', [
            'academyCourse' => $academyCourse,
        ]);
    }

    public function destroy(AcademyCourse $academyCourse): RedirectResponse
    {
        $academyCourse->delete();

        return redirect()
            ->route('admin.academy-courses.index')
            ->with('status', __('hermes.admin.academy_courses.deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function academyCoursePayload(Request $request): array
    {
        $attributes = $request->validated();

        return [
            'slug' => $attributes['slug'],
            'theme' => $attributes['theme'],
            'path' => trim($attributes['path'], '/'),
            'estimated_minutes' => $attributes['estimated_minutes'],
            'sort_order' => $attributes['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'pro_only' => $request->boolean('pro_only'),
            'title' => $this->localizedFieldPayload($attributes['title']),
            'audience' => $this->localizedFieldPayload($attributes['audience']),
            'goal' => $this->localizedFieldPayload($attributes['goal']),
            'summary' => $this->localizedFieldPayload($attributes['summary']),
            'learning_goals' => $this->localizedListPayload($attributes['learning_goals']),
            'contents' => $this->localizedListPayload($attributes['contents']),
        ];
    }
}
