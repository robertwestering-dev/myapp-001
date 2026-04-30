<?php

namespace App\Support\Academy;

class PositiveFoundationStrengthCatalog
{
    /**
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_keys($this->labels());
    }

    /**
     * @return array<int, array{key: string, label: string}>
     */
    public function options(): array
    {
        return collect($this->labels())
            ->map(fn (string $label, string $key): array => [
                'key' => $key,
                'label' => $label,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function labels(): array
    {
        $labels = trans('hermes.academy.strengths_widget.options');

        return is_array($labels) ? $labels : [];
    }
}
