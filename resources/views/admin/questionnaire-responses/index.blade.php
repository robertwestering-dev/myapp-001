<x-layouts.hermes-admin
    :title="__('hermes.reports.index_title')"
    :eyebrow="__('hermes.reports.eyebrow')"
    :heading="__('hermes.reports.index_heading')"
    :lead="__('hermes.reports.index_lead')"
    menu-active="questionnaire-responses"
    :show-secondary-menu-items="false"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="$responses->total()"
            :description="__('hermes.reports.responses_in_selection')"
        />
        <x-hermes-fact
            :title="count($questionnaires)"
            :description="__('hermes.reports.available_questionnaires_filter')"
        />
        <x-hermes-fact
            :title="__('hermes.reports.scope')"
            :description="__('hermes.reports.scope_text')"
        />
    </x-slot:heroFacts>

    <style>
        .spotlight-grid,
        .meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .admin-toolbar {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin: 28px 0 24px;
        }

        .admin-toolbar--end {
            align-items: end;
        }

        .admin-toolbar__group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end;
        }

        .admin-filter-field {
            display: grid;
            gap: 8px;
            min-width: min(100%, 220px);
        }

        .admin-filter-field__label,
        .admin-filter-field__description {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        .admin-filter-control {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .admin-filter-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .spotlight-grid {
            margin: 0 0 24px;
        }

        .spotlight-card {
            flex: 1 1 320px;
            padding: 22px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.56);
            display: grid;
            gap: 14px;
        }

        .spotlight-card__actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
        }

        table {
            width: 100%;
            min-width: 920px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
            vertical-align: top;
        }

        th {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .muted {
            color: var(--muted);
        }

        .meta {
            align-items: center;
            justify-content: space-between;
            margin-top: 22px;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pagination__link,
        .pagination__current {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 44px;
            padding: 0 14px;
            border-radius: 14px;
            font-family: Arial, Helvetica, sans-serif;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.68);
        }

        .pagination__current {
            color: #fff;
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-soft) 100%);
            border-color: transparent;
        }
    </style>

    <section class="content-panel">
        <x-admin-feedback :messages="session('status')" />
        <x-admin-feedback variant="errors" :messages="$errors->all()" />

        <x-admin-toolbar>
            <form method="GET" action="{{ route('admin.questionnaire-responses.index') }}" class="filters">
                <x-admin-filter-field
                    label="Questionnaire"
                    :description="__('hermes.reports.questionnaire')"
                >
                    <select name="questionnaire_id" class="admin-filter-control">
                        <option value="">{{ __('hermes.reports.all_questionnaires') }}</option>
                        @foreach ($questionnaires as $id => $title)
                            <option value="{{ $id }}" @selected($questionnaireId === (int) $id)>{{ $title }}</option>
                        @endforeach
                    </select>
                </x-admin-filter-field>

                <x-admin-filter-field :label="__('hermes.reports.organization')">
                    <select name="org_id" class="admin-filter-control">
                        <option value="">{{ __('hermes.reports.all_organizations') }}</option>
                        @foreach ($organizations as $id => $name)
                            <option value="{{ $id }}" @selected($orgId === (int) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </x-admin-filter-field>

                <x-admin-filter-field :label="__('hermes.reports.user')">
                    <select name="user_id" class="admin-filter-control">
                        <option value="">{{ __('hermes.reports.all_users') }}</option>
                        @foreach ($users as $id => $label)
                            <option value="{{ $id }}" @selected($selectedUserId === (int) $id)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-admin-filter-field>

                <x-admin-filter-field :label="__('hermes.reports.response_state')">
                    <select name="response_state" class="admin-filter-control">
                        <option value="completed" @selected($responseState === 'completed')>{{ __('hermes.reports.state_completed_only') }}</option>
                        <option value="draft" @selected($responseState === 'draft')>{{ __('hermes.reports.state_draft_only') }}</option>
                        <option value="all" @selected($responseState === 'all')>{{ __('hermes.reports.state_all') }}</option>
                    </select>
                </x-admin-filter-field>

                <x-admin-filter-actions>
                    <button type="submit" class="pill">{{ __('hermes.reports.filter') }}</button>
                    <a href="{{ route('admin.questionnaire-responses.index') }}" class="ghost-pill">{{ __('hermes.reports.reset') }}</a>
                </x-admin-filter-actions>
            </form>

            <x-admin-toolbar-group>
                <a
                    href="{{ route('admin.questionnaire-responses.stats', $activeFilters) }}"
                    class="ghost-pill"
                >
                    {{ __('hermes.reports.view_stats') }}
                </a>
                <a
                    href="{{ route('admin.questionnaire-responses.export', $activeFilters) }}"
                    class="ghost-pill"
                >
                    {{ __('hermes.reports.export_detail') }}
                </a>
                <a
                    href="{{ route('admin.questionnaire-responses.export-summary', $activeFilters) }}"
                    class="ghost-pill"
                >
                    {{ __('hermes.reports.export_summary') }}
                </a>
                <a
                    href="{{ route('admin.questionnaire-responses.export-stats', $activeFilters) }}"
                    class="ghost-pill"
                >
                    {{ __('hermes.reports.export_stats') }}
                </a>
            </x-admin-toolbar-group>
        </x-admin-toolbar>

        <div class="spotlight-grid">
            @foreach ($spotlightQuestionnaires as $spotlightQuestionnaire)
                <article class="spotlight-card">
                    <div>
                        <span class="eyebrow">{{ __('hermes.reports.featured') }}</span>
                    </div>
                    <div>
                        <strong>{{ $spotlightQuestionnaire->title }}</strong>
                        <div class="muted">{{ $spotlightQuestionnaire->description }}</div>
                    </div>
                    <div class="spotlight-card__actions">
                        <a href="{{ route('admin.questionnaire-responses.index', ['questionnaire_id' => $spotlightQuestionnaire->id]) }}" class="ghost-pill">
                            {{ __('hermes.reports.filter_responses') }}
                        </a>
                        <a href="{{ route('admin.questionnaire-responses.stats', ['questionnaire_id' => $spotlightQuestionnaire->id]) }}" class="ghost-pill">
                            {{ __('hermes.reports.open_stats') }}
                        </a>
                        <a href="{{ route('admin.questionnaire-responses.export-summary', ['questionnaire_id' => $spotlightQuestionnaire->id]) }}" class="ghost-pill">
                            {{ __('hermes.reports.summary_export') }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('hermes.reports.questionnaire') }}</th>
                        <th>{{ __('hermes.reports.organization') }}</th>
                        <th>{{ __('hermes.reports.user') }}</th>
                        <th>{{ __('hermes.reports.response_state') }}</th>
                        <th>{{ __('hermes.reports.submitted_at') }}</th>
                        <th>{{ __('hermes.reports.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($responses as $response)
                        <tr>
                            <td>{{ $response->organizationQuestionnaire->questionnaire->title }}</td>
                            <td>{{ $response->organizationQuestionnaire->organization->naam }}</td>
                            <td>
                                <div>{{ $response->user->name }}</div>
                                <div class="muted">{{ $response->user->email }}</div>
                            </td>
                            <td>
                                <x-admin-status-badge
                                    :label="$response->isDraft() ? __('hermes.reports.state_draft') : __('hermes.reports.state_completed')"
                                    :tone="$response->isDraft() ? 'warning' : 'default'"
                                    uppercase
                                />
                            </td>
                            <td>{{ $response->submitted_at?->format('d-m-Y H:i') ?? $response->last_saved_at?->format('d-m-Y H:i') ?? __('hermes.reports.submitted_at_unknown') }}</td>
                            <td>
                                <a href="{{ route('admin.questionnaire-responses.show', $response) }}" class="ghost-pill">{{ __('hermes.reports.view_response') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-admin-empty-state
                                    class="spotlight-card"
                                    actions-class="spotlight-card__actions"
                                    :title="__('hermes.reports.no_responses_title')"
                                    :description="__('hermes.reports.no_responses_text')"
                                >
                                    <x-slot:actions>
                                        <a href="{{ route('admin.questionnaire-responses.index') }}" class="ghost-pill">{{ __('hermes.reports.reset') }}</a>
                                        <a href="{{ route('admin.questionnaire-responses.stats', $activeFilters) }}" class="ghost-pill">{{ __('hermes.reports.view_stats') }}</a>
                                    </x-slot:actions>
                                </x-admin-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-admin-results-meta
            :paginator="$responses"
            :aria-label="__('hermes.reports.action')"
            link-mode="collection"
            :range-text="__('hermes.reports.results_range', ['from' => $responses->firstItem() ?? 0, 'to' => $responses->lastItem() ?? 0, 'total' => $responses->total()])"
        />
    </section>
</x-layouts.hermes-admin>
