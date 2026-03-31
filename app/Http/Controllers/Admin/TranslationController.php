<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Translations\ManageHermesTranslations;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTranslationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TranslationController extends Controller
{
    public function __construct(public ManageHermesTranslations $translations) {}

    public function index(Request $request): View
    {
        $locale = $request->string('locale')->toString();
        $page = $request->string('page')->toString();
        $element = $request->string('element')->toString();
        $search = trim($request->string('search')->toString());

        $records = $this->translations->all();

        $filteredTranslations = $records
            ->when($locale !== '', fn (Collection $collection): Collection => $collection->where('locale', $locale))
            ->when($page !== '', fn (Collection $collection): Collection => $collection->where('page', $page))
            ->when($element !== '', fn (Collection $collection): Collection => $collection->where('element', $element))
            ->when($search !== '', function (Collection $collection) use ($search): Collection {
                $needle = Str::lower($search);

                return $collection->filter(
                    fn (array $translation): bool => Str::contains(Str::lower($translation['content']), $needle),
                );
            })
            ->values();

        return view('admin.translations.index', [
            'translations' => $this->paginate($filteredTranslations, $request),
            'supportedLocales' => config('locales.supported', []),
            'pages' => $records->pluck('page')->unique()->sort()->values(),
            'elements' => $records->pluck('element')->unique()->sort()->values(),
            'filters' => [
                'locale' => $locale,
                'page' => $page,
                'element' => $element,
                'search' => $search,
            ],
        ]);
    }

    public function edit(Request $request): View
    {
        $locale = $request->string('locale')->toString();
        $key = $request->string('key')->toString();
        $translation = $this->translations->find($locale, $key);

        abort_if($translation === null, 404);

        return view('admin.translations.edit', [
            'translation' => $translation,
            'returnFilters' => [
                'locale' => $request->string('filter_locale')->toString(),
                'page' => $request->string('filter_page')->toString(),
                'element' => $request->string('filter_element')->toString(),
                'search' => $request->string('filter_search')->toString(),
                'page_number' => $request->integer('page_number', 1),
            ],
        ]);
    }

    public function update(UpdateTranslationRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $translation = $this->translations->find($attributes['locale'], $attributes['key']);

        abort_if($translation === null, 404);

        $this->translations->update(
            $attributes['locale'],
            $attributes['key'],
            $attributes['content'],
        );

        return redirect()
            ->route('admin.translations.index', [
                'locale' => $attributes['filter_locale'] ?: null,
                'page' => $attributes['filter_page'] ?: null,
                'element' => $attributes['filter_element'] ?: null,
                'search' => $attributes['filter_search'] ?: null,
                'page_number' => $attributes['page_number'] ?: null,
            ])
            ->with('status', 'Vertaling succesvol bijgewerkt.');
    }

    protected function paginate(Collection $translations, Request $request): LengthAwarePaginator
    {
        $perPage = 25;
        $currentPage = max(1, $request->integer('page_number', 1));
        $items = $translations->forPage($currentPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $translations->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page_number',
                'query' => $request->query(),
            ],
        );
    }
}
