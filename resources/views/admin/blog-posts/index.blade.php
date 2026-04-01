<x-layouts.hermes-admin
    title="Admin blog"
    eyebrow="Blog"
    heading="Blogbeheer"
    lead="Beheer hier alle publieke blogposts die zichtbaar zijn op de nieuwe openbare blogpagina."
    menu-active="blog-posts"
    :show-secondary-menu-items="false"
>
    <x-slot:heroFacts>
        <x-hermes-fact :title="$blogPosts->total()" description="Blogposts in de database" />
        <x-hermes-fact :title="$blogPosts->where('is_published', true)->count()" description="Gepubliceerd op deze pagina" />
        <x-hermes-fact :title="$blogPosts->where('is_featured', true)->count()" description="Uitgelichte posts op deze pagina" />
    </x-slot:heroFacts>

    <style>
        .toolbar,
        .actions,
        .meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar,
        .meta {
            align-items: center;
            justify-content: space-between;
        }

        .toolbar {
            margin: 28px 0 24px;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
        }

        table {
            width: 100%;
            min-width: 980px;
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

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
        }

        .badge--inactive {
            background: rgba(168, 74, 25, 0.12);
            color: var(--accent-deep);
        }

        .muted {
            color: var(--muted);
        }

        .tag-list {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .tag-list span {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-size: 0.82rem;
        }

        .meta {
            margin-top: 22px;
        }
    </style>

    <section class="content-panel">
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <div class="toolbar">
            <div class="muted">Combineer publiceren, uitlichten, tags en meertalige content vanuit een centrale blogmodule.</div>

            <div class="actions">
                <a href="{{ route('admin.blog-posts.create') }}" class="pill">Nieuwe blogpost</a>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Blogpost</th>
                        <th>Status</th>
                        <th>Auteur</th>
                        <th>Publicatie</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($blogPosts as $blogPost)
                        <tr>
                            <td>
                                <strong>{{ $blogPost->titleForLocale('nl') }}</strong>
                                <div class="muted">{{ $blogPost->excerptForLocale('nl') }}</div>
                                <div class="tag-list">
                                    @foreach ($blogPost->tagsList() as $tag)
                                        <span>{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span @class(['badge', 'badge--inactive' => ! $blogPost->is_published])>
                                    {{ $blogPost->is_published ? 'Gepubliceerd' : 'Concept' }}
                                </span>

                                @if ($blogPost->is_featured)
                                    <div class="muted">Uitgelicht</div>
                                @endif
                            </td>
                            <td>{{ $blogPost->author?->name ?? 'Onbekend' }}</td>
                            <td>
                                <div>{{ $blogPost->published_at?->format('Y-m-d H:i') ?? 'Nog niet gepland' }}</div>
                                <div class="muted">{{ $blogPost->slug }}</div>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.blog-posts.edit', $blogPost) }}" class="ghost-pill">Wijzigen</a>
                                    @if ($blogPost->isPublished())
                                        <a href="{{ route('blog.show', $blogPost) }}" class="ghost-pill" target="_blank" rel="noopener noreferrer">Bekijk</a>
                                    @endif
                                    <a href="{{ route('admin.blog-posts.confirm-delete', $blogPost) }}" class="danger-pill">Verwijderen</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">Er zijn nog geen blogposts opgeslagen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="meta">
            <span class="muted">Resultaten {{ $blogPosts->firstItem() ?? 0 }} t/m {{ $blogPosts->lastItem() ?? 0 }} van {{ $blogPosts->total() }}</span>
        </div>
    </section>
</x-layouts.hermes-admin>
