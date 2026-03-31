<?php

use App\Models\User;
use Illuminate\Support\Arr;

beforeEach(function (): void {
    $this->translationBackups = collect(array_keys(config('locales.supported', [])))
        ->mapWithKeys(fn (string $locale): array => [$locale => file_get_contents(lang_path("{$locale}/hermes.php"))]);
});

afterEach(function (): void {
    foreach ($this->translationBackups as $locale => $contents) {
        file_put_contents(lang_path("{$locale}/hermes.php"), $contents);
    }
});

test('manager cannot access translation management', function () {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)->get(route('admin.translations.index'));

    $response->assertForbidden();
});

test('admin can filter translation management overview', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.translations.index', [
        'locale' => 'nl',
        'page' => 'home',
        'element' => 'hero_title',
    ]));

    $response->assertOk()
        ->assertSee('Beheer alle Hermes-teksten')
        ->assertSee('NL')
        ->assertSee('home')
        ->assertSee('hero_title')
        ->assertViewHas('translations', function ($translations): bool {
            return $translations->total() === 1
                && $translations->items()[0]['content'] === 'Maak digitale transformatie begrijpelijk, meetbaar en menselijk';
        });
});

test('admin can search within translation content', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.translations.index', [
        'search' => 'digitale transformatie begrijpelijk',
    ]));

    $response->assertOk()
        ->assertViewHas('translations', function ($translations): bool {
            return $translations->total() >= 1
                && collect($translations->items())->contains(
                    fn (array $translation): bool => $translation['key'] === 'home.hero_title'
                        && $translation['locale'] === 'nl',
                );
        });
});

test('admin can update a translation row from the overview flow', function () {
    $admin = User::factory()->admin()->create();
    $updatedText = 'Nieuwe titel voor de homepage vanuit het admin-portal';

    $response = $this->actingAs($admin)->put(route('admin.translations.update'), [
        'locale' => 'nl',
        'key' => 'home.hero_title',
        'content' => $updatedText,
        'filter_locale' => 'nl',
        'filter_page' => 'home',
        'filter_element' => 'hero_title',
        'filter_search' => 'homepage vanuit het admin-portal',
        'page_number' => 1,
    ]);

    $response->assertRedirect(route('admin.translations.index', [
        'locale' => 'nl',
        'page' => 'home',
        'element' => 'hero_title',
        'search' => 'homepage vanuit het admin-portal',
        'page_number' => 1,
    ]));

    $overview = $this->actingAs($admin)->get(route('admin.translations.index', [
        'locale' => 'nl',
        'page' => 'home',
        'element' => 'hero_title',
    ]));

    $overview->assertOk()
        ->assertViewHas('translations', function ($translations) use ($updatedText): bool {
            return $translations->total() === 1
                && $translations->items()[0]['content'] === $updatedText;
        });

    expect(Arr::get(require lang_path('nl/hermes.php'), 'home.hero_title'))
        ->toBe($updatedText);
});
