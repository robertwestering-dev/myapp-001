<?php

namespace App\Http\Requests\Admin;

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreOrganizationQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdminPortal() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        /** @var User|null $actor */
        $actor = $this->user();

        $organizationRule = $actor?->isAdmin()
            ? Rule::exists(Organization::class, 'org_id')
            : Rule::in([(string) $actor?->org_id]);

        if (! $actor?->isAdmin()) {
            return [
                'org_id' => ['required', $organizationRule],
                'available_from' => ['nullable', 'date'],
                'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
                'is_active' => ['required', 'boolean'],
            ];
        }

        return [
            'org_ids' => ['nullable', 'array'],
            'org_ids.*' => ['distinct', $organizationRule],
            'available_from_by_org' => ['nullable', 'array'],
            'available_from_by_org.*' => ['nullable', 'date'],
            'available_until_by_org' => ['nullable', 'array'],
            'available_until_by_org.*' => ['nullable', 'date'],
            'is_active_by_org' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function ($validator): void {
                /** @var Questionnaire|null $questionnaire */
                $questionnaire = $this->route('questionnaire');
                /** @var User|null $actor */
                $actor = $this->user();

                if ($questionnaire === null) {
                    // continue with remaining validation rules
                } elseif (! $actor?->isAdmin()) {
                    if ($this->boolean('is_active') && ! $questionnaire->is_active) {
                        $validator->errors()->add('is_active', __('hermes.questionnaires.availability_requires_active_questionnaire'));
                    }
                } else {
                    foreach ($this->selectedOrganizationConfigurations() as $configuration) {
                        if ($configuration['is_active'] && ! $questionnaire->is_active) {
                            $validator->errors()->add('org_ids', __('hermes.questionnaires.availability_requires_active_questionnaire'));

                            break;
                        }
                    }
                }

                if (! $actor?->isAdmin()) {
                    if ($actor->org_id === null) {
                        $validator->errors()->add('org_id', 'Kies een organisatie.');
                    }

                    return;
                }

                $organizationIds = collect($this->input('org_ids', []))
                    ->filter(fn (mixed $value): bool => filled($value))
                    ->map(fn (mixed $value): string => (string) $value)
                    ->values();

                if ($organizationIds->isEmpty()) {
                    $validator->errors()->add('org_ids', 'Kies minimaal één organisatie.');

                    return;
                }

                foreach ($this->selectedOrganizationConfigurations() as $configuration) {
                    $availableFrom = $configuration['available_from'];
                    $availableUntil = $configuration['available_until'];

                    if ($availableFrom !== null && $availableUntil !== null) {
                        $availableFromDate = Carbon::parse($availableFrom);
                        $availableUntilDate = Carbon::parse($availableUntil);

                        if ($availableUntilDate->isBefore($availableFromDate)) {
                            $validator->errors()->add(
                                "available_until_by_org.{$configuration['org_id']}",
                                'De einddatum moet gelijk zijn aan of na de begindatum.'
                            );
                        }
                    }

                    $existingAvailability = OrganizationQuestionnaire::query()
                        ->where('questionnaire_id', $questionnaire?->id)
                        ->where('org_id', $configuration['org_id'])
                        ->exists();

                    if ($existingAvailability) {
                        $validator->errors()->add(
                            "org_ids.{$configuration['org_id']}",
                            'Voor deze organisatie bestaat al een beschikbaarheid.'
                        );
                    }
                }
            },
        ];
    }

    /**
     * @return array<int, array{org_id: int, available_from: ?string, available_until: ?string, is_active: bool}>
     */
    public function selectedOrganizationConfigurations(): array
    {
        /** @var User|null $actor */
        $actor = $this->user();

        if (! $actor?->isAdmin()) {
            if (! $this->filled('org_id')) {
                return [];
            }

            return [[
                'org_id' => (int) $this->input('org_id'),
                'available_from' => $this->dateValue('available_from'),
                'available_until' => $this->dateValue('available_until'),
                'is_active' => $this->boolean('is_active'),
            ]];
        }

        $organizationIds = array_values(array_unique(array_map(
            static fn (mixed $value): int => (int) $value,
            array_filter($this->input('org_ids', []), static fn (mixed $value): bool => filled($value)),
        )));

        return array_map(function (int $organizationId): array {
            return [
                'org_id' => $organizationId,
                'available_from' => $this->dateValue("available_from_by_org.{$organizationId}"),
                'available_until' => $this->dateValue("available_until_by_org.{$organizationId}"),
                'is_active' => $this->boolean("is_active_by_org.{$organizationId}"),
            ];
        }, $organizationIds);
    }

    protected function dateValue(string $key): ?string
    {
        $value = $this->input($key);

        return filled($value) ? (string) $value : null;
    }
}
