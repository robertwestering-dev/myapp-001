<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademyStrengthsRequest;
use App\Models\User;
use App\Support\Academy\PositiveFoundationStrengthCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AcademyStrengthsWidgetController extends Controller
{
    public function __construct(
        private readonly PositiveFoundationStrengthCatalog $strengthCatalog,
    ) {}

    public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('academy.strengths-widget', [
            'strengthOptions' => $this->strengthCatalog->options(),
            'selectedStrengths' => collect($user->selected_strengths ?? [])
                ->map(fn (mixed $strength): string => (string) $strength)
                ->values()
                ->all(),
        ]);
    }

    public function store(StoreAcademyStrengthsRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->forceFill([
            'selected_strengths' => $request->selectedStrengths(),
        ])->save();

        return redirect()
            ->route('academy.widgets.strengths')
            ->with('status', __('hermes.academy.strengths_widget.saved'));
    }
}
