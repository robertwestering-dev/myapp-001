<x-layouts.hermes-admin
    :title="$title"
    eyebrow="Blog"
    :heading="$title"
    :lead="$intro"
    menu-active="blog-posts"
>
    <style>
        form,
        .locale-grid {
            display: grid;
            gap: 18px;
        }

        .locale-grid {
            margin-top: 28px;
        }

        .locale-panel {
            padding: 22px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
            display: grid;
            gap: 16px;
        }

        .field-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        label {
            display: grid;
            gap: 8px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        input,
        textarea {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        .content-field textarea {
            min-height: 260px;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .checkbox-row input {
            width: auto;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .helper {
            color: var(--muted);
            font-size: 0.92rem;
        }

        @media (max-width: 860px) {
            .field-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <x-hermes-section-header
        tagline="Publieke blog"
        heading="Beheer publicatie, samenvatting en meertalige inhoud in een centrale blogmodule"
        description="Gebruik per taal een eigen inhoudsblok. De content ondersteunt Markdown, zodat koppen, lijsten en links netjes op de publieke blog detailpagina worden weergegeven."
    />

    <section class="content-panel">
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $isEditing ? route('admin.blog-posts.update', $blogPost) : route('admin.blog-posts.store') }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <div class="field-grid">
                <label>
                    <span>Slug</span>
                    <input type="text" name="slug" value="{{ old('slug', $blogPost->slug) }}" required>
                </label>

                <label>
                    <span>Omslagafbeelding URL</span>
                    <input type="url" name="cover_image_url" value="{{ old('cover_image_url', $blogPost->cover_image_url) }}">
                    <span class="helper">Optioneel. Gebruik een volledige `https://` URL.</span>
                </label>

                <label>
                    <span>Publicatiedatum</span>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', $blogPost->published_at?->format('Y-m-d\TH:i')) }}">
                </label>

                <label>
                    <span>Tags</span>
                    <textarea name="tags">{{ old('tags', implode(PHP_EOL, $blogPost->tagsList())) }}</textarea>
                    <span class="helper">Gebruik een tag per regel of scheid tags met komma's.</span>
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $blogPost->is_published))>
                    <span>Gepubliceerd tonen op de publieke blog</span>
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $blogPost->is_featured))>
                    <span>Uitlichten in de hero van de blogpagina</span>
                </label>
            </div>

            <div class="locale-grid">
                @foreach ($supportedLocales as $localeCode => $localeLabel)
                    <section class="locale-panel">
                        <strong>Taal {{ $localeLabel }}</strong>

                        <label>
                            <span>Titel</span>
                            <input type="text" name="title[{{ $localeCode }}]" value="{{ old("title.$localeCode", $blogPost->translation('title', $localeCode)) }}" required>
                        </label>

                        <label>
                            <span>Korte samenvatting</span>
                            <textarea name="excerpt[{{ $localeCode }}]" required>{{ old("excerpt.$localeCode", $blogPost->translation('excerpt', $localeCode)) }}</textarea>
                        </label>

                        <label class="content-field">
                            <span>Artikelinhoud</span>
                            <textarea name="content[{{ $localeCode }}]" required>{{ old("content.$localeCode", $blogPost->translation('content', $localeCode)) }}</textarea>
                            <span class="helper">Markdown wordt ondersteund: gebruik bijvoorbeeld `#` voor koppen en `-` voor lijsten.</span>
                        </label>
                    </section>
                @endforeach
            </div>

            <div class="form-actions">
                <button type="submit" class="pill">{{ $submitLabel }}</button>
                <a href="{{ route('admin.blog-posts.index') }}" class="ghost-pill">Terug naar overzicht</a>
            </div>
        </form>
    </section>
</x-layouts.hermes-admin>
