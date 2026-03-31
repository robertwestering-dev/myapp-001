<?php

namespace App\Actions\Translations;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class ManageHermesTranslations
{
    /**
     * @return Collection<int, array{locale: string, locale_label: string, page: string, element: string, key: string, content: string}>
     */
    public function all(): Collection
    {
        return collect(config('locales.supported', []))
            ->map(function (string $localeLabel, string $locale): Collection {
                $translations = $this->loadLocaleFile($locale);

                return collect($this->flattenTranslations($translations))
                    ->map(function (string $content, string $key) use ($locale, $localeLabel): array {
                        $segments = explode('.', $key, 2);
                        $page = $segments[0];
                        $element = $segments[1] ?? $segments[0];

                        return [
                            'locale' => $locale,
                            'locale_label' => $localeLabel,
                            'page' => $page,
                            'element' => $element,
                            'key' => $key,
                            'content' => $content,
                        ];
                    })
                    ->values();
            })
            ->collapse()
            ->sortBy([
                ['locale', 'asc'],
                ['page', 'asc'],
                ['element', 'asc'],
            ])
            ->values();
    }

    /**
     * @return array{locale: string, locale_label: string, page: string, element: string, key: string, content: string}|null
     */
    public function find(string $locale, string $key): ?array
    {
        if (! array_key_exists($locale, config('locales.supported', []))) {
            return null;
        }

        $translations = $this->loadLocaleFile($locale);
        $value = Arr::get($translations, $key);

        if (is_array($value) || $value === null) {
            return null;
        }

        $segments = explode('.', $key, 2);

        return [
            'locale' => $locale,
            'locale_label' => config("locales.supported.{$locale}", Str::upper($locale)),
            'page' => $segments[0],
            'element' => $segments[1] ?? $segments[0],
            'key' => $key,
            'content' => (string) $value,
        ];
    }

    public function update(string $locale, string $key, string $content): void
    {
        $translations = $this->loadLocaleFile($locale);

        if (is_array(Arr::get($translations, $key)) || Arr::get($translations, $key) === null) {
            throw new RuntimeException("Translation [{$locale}.{$key}] could not be updated.");
        }

        Arr::set($translations, $key, trim($content));

        file_put_contents(
            $this->translationPath($locale),
            "<?php\n\nreturn ".$this->exportValue($translations).";\n",
        );
    }

    /**
     * @param  array<string|int, mixed>  $translations
     * @return array<string, string>
     */
    protected function flattenTranslations(array $translations, string $prefix = ''): array
    {
        $records = [];

        foreach ($translations as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $records += $this->flattenTranslations($value, $fullKey);

                continue;
            }

            $records[$fullKey] = (string) $value;
        }

        return $records;
    }

    /**
     * @return array<string, mixed>
     */
    protected function loadLocaleFile(string $locale): array
    {
        $path = $this->translationPath($locale);

        if (! file_exists($path)) {
            throw new RuntimeException("Translation file [{$path}] not found.");
        }

        /** @var array<string, mixed> $translations */
        $translations = require $path;

        return $translations;
    }

    protected function translationPath(string $locale): string
    {
        return lang_path("{$locale}/hermes.php");
    }

    protected function exportValue(mixed $value, int $indent = 0): string
    {
        if (! is_array($value)) {
            return var_export($value, true);
        }

        if ($value === []) {
            return '[]';
        }

        $padding = str_repeat('    ', $indent);
        $childPadding = str_repeat('    ', $indent + 1);
        $lines = [];

        foreach ($value as $key => $item) {
            $exportedKey = is_int($key) ? $key : var_export($key, true);
            $lines[] = "{$childPadding}{$exportedKey} => ".$this->exportValue($item, $indent + 1).',';
        }

        return "[\n".implode("\n", $lines)."\n{$padding}]";
    }
}
