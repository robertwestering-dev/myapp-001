<x-layouts.hermes-admin
    :title="$title"
    eyebrow="Blog"
    :heading="$title"
    :lead="$intro"
    menu-active="blog-posts"
>
    @php
        $publicationStatus = $blogPost->publicationStatus();
        $statusLabel = match ($publicationStatus) {
            'draft' => 'Concept',
            'scheduled' => 'Geplande publicatie',
            default => 'Publiek zichtbaar',
        };
        $statusText = match ($publicationStatus) {
            'draft' => 'Deze blogpost blijft intern totdat u publiceren inschakelt.',
            'scheduled' => 'Deze blogpost staat ingepland en wordt automatisch zichtbaar zodra de publicatiedatum bereikt is.',
            default => 'Deze blogpost is nu zichtbaar op de publieke blog en wordt meegenomen in de sitemap.',
        };
    @endphp

    <style>
        form,
        .locale-grid,
        .preview-stack,
        .helper-actions,
        .status-grid,
        .preview-toolbar {
            display: grid;
            gap: 18px;
        }

        .locale-grid {
            margin-top: 28px;
        }

        .locale-panel,
        .status-card,
        .preview-card {
            padding: 22px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
            display: grid;
            gap: 16px;
        }

        .field-grid,
        .status-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .status-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-bottom: 12px;
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
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.94rem;
            line-height: 1.7;
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

        .helper,
        .preview-note {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .helper-actions {
            grid-template-columns: repeat(3, max-content);
            align-items: start;
        }

        .preview-card {
            background: rgba(255, 255, 255, 0.72);
        }

        .preview-surface {
            padding: 22px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(22, 33, 29, 0.08);
            line-height: 1.8;
            overflow: hidden;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .preview-surface h1,
        .preview-surface h2,
        .preview-surface h3 {
            margin-top: 0;
        }

        .preview-surface img,
        .preview-surface video {
            max-width: 100%;
            width: 100%;
            height: auto;
            display: block;
            border-radius: 16px;
        }

        .preview-surface a {
            overflow-wrap: anywhere;
        }

        .preview-surface::after {
            content: "";
            display: block;
            clear: both;
        }

        .preview-surface .article-media {
            width: 100%;
            margin: 0 0 18px;
        }

        .preview-surface .article-media--left {
            float: left;
            margin: 6px 24px 18px 0;
        }

        .preview-surface .article-media--right {
            float: right;
            margin: 6px 0 18px 24px;
        }

        .preview-surface .article-media--center {
            margin-left: auto;
            margin-right: auto;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(30, 71, 61, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .status-badge--draft,
        .status-badge--scheduled {
            background: rgba(188, 91, 44, 0.12);
            color: var(--accent-deep);
        }

        .preview-toolbar {
            grid-auto-flow: column;
            justify-content: start;
        }

        @media (max-width: 960px) {
            .field-grid,
            .status-grid {
                grid-template-columns: 1fr;
            }

            .helper-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <x-hermes-section-header
        tagline="Publieke blog"
        heading="Beheer publicatie, samenvatting en meertalige inhoud in een centrale blogmodule"
        description="Gebruik per taal een eigen inhoudsblok. De content ondersteunt Markdown, live preview en korte media-shortcodes, zodat de publieke blog professioneler en consistenter blijft."
    />

    <section class="content-panel">
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="status-grid">
            <article class="status-card">
                <span class="status-badge @if ($publicationStatus !== 'published') status-badge--{{ $publicationStatus }} @endif">{{ $statusLabel }}</span>
                <strong>Publicatiestatus</strong>
                <div class="helper">{{ $statusText }}</div>
            </article>

            <article class="status-card">
                <strong>Publicatieplanning</strong>
                <div>{{ $blogPost->published_at?->format('Y-m-d H:i') ?? 'Nog niet ingepland' }}</div>
                <div class="helper">Gebruik een toekomstige datum voor geplande publicatie of laat het concept staan tot u klaar bent.</div>
            </article>

            <article class="status-card">
                <strong>SEO & discoverability</strong>
                <div class="helper">Meta description komt uit de samenvatting. Publieke posts krijgen Open Graph, structured data en opname in de sitemap.</div>
            </article>
        </div>

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
                    <span class="helper">Optioneel. Gebruik een volledige `https://` URL uit <a href="{{ route('admin.media-assets.index') }}">Assetbeheer</a>.</span>
                </label>

                <label>
                    <span>Publicatiedatum</span>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', $blogPost->published_at?->format('Y-m-d\TH:i')) }}">
                    <span class="helper">Toekomstige datum + publiceren aangevinkt = geplande publicatie.</span>
                </label>

                <label>
                    <span>Tags</span>
                    <textarea name="tags">{{ old('tags', implode(PHP_EOL, $blogPost->tagsList())) }}</textarea>
                    <span class="helper">Gebruik een tag per regel of scheid tags met komma's.</span>
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $blogPost->is_published))>
                    <span>Publiceren inschakelen</span>
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $blogPost->is_featured))>
                    <span>Uitlichten in de hero van de blogpagina</span>
                </label>
            </div>

            <div class="locale-grid">
                @foreach ($supportedLocales as $localeCode => $localeLabel)
                    <section class="locale-panel" data-locale-panel>
                        @php($isPrimaryLocale = $localeCode === 'nl')
                        <strong>Taal {{ $localeLabel }}</strong>

                        <label>
                            <span>Titel</span>
                            <input type="text" name="title[{{ $localeCode }}]" value="{{ old("title.$localeCode", $blogPost->translation('title', $localeCode)) }}" @required($isPrimaryLocale) data-preview-title="{{ $localeCode }}">
                        </label>

                        <label>
                            <span>Korte samenvatting</span>
                            <textarea name="excerpt[{{ $localeCode }}]" @required($isPrimaryLocale) data-preview-excerpt="{{ $localeCode }}">{{ old("excerpt.$localeCode", $blogPost->translation('excerpt', $localeCode)) }}</textarea>
                            <span class="helper">
                                Deze samenvatting wordt ook gebruikt als meta description voor zoekmachines en social previews.
                                @unless ($isPrimaryLocale)
                                    Laat dit veld gerust leeg als de vertaling nog niet klaar is.
                                @endunless
                            </span>
                        </label>

                        <label class="content-field">
                            <span>Artikelinhoud</span>
                            <textarea name="content[{{ $localeCode }}]" @required($isPrimaryLocale) data-preview-content="{{ $localeCode }}">{{ old("content.$localeCode", $blogPost->translation('content', $localeCode)) }}</textarea>
                            <span class="helper">
                                Markdown wordt ondersteund: gebruik bijvoorbeeld `#` voor koppen, `-` voor lijsten en de shortcode `[video url="https://..."]` voor video.
                                @unless ($isPrimaryLocale)
                                    Als dit leeg blijft, valt de publieke blog automatisch terug op de Nederlandse inhoud.
                                @endunless
                            </span>
                        </label>

                        <div class="helper-actions">
                            <button type="button" class="ghost-pill" data-insert-snippet="{{ $localeCode }}" data-snippet-type="image">Afbeelding snippet</button>
                            <button type="button" class="ghost-pill" data-insert-snippet="{{ $localeCode }}" data-snippet-type="video">Video shortcode</button>
                            <button type="button" class="ghost-pill" data-insert-snippet="{{ $localeCode }}" data-snippet-type="cta">CTA blok</button>
                        </div>

                        <section class="preview-card">
                            <div class="preview-stack">
                                <div class="preview-toolbar">
                                    <span class="status-badge">Live preview {{ strtoupper($localeCode) }}</span>
                                </div>
                                <div class="preview-note">De preview volgt uw Markdown direct in het formulier en helpt bij koppen, lijsten, afbeeldingen en video shortcodes.</div>
                                <article class="preview-surface">
                                    <h2 data-rendered-title="{{ $localeCode }}">{{ old("title.$localeCode", $blogPost->translation('title', $localeCode)) }}</h2>
                                    <p data-rendered-excerpt="{{ $localeCode }}">{{ old("excerpt.$localeCode", $blogPost->translation('excerpt', $localeCode)) }}</p>
                                    <div data-rendered-content="{{ $localeCode }}"></div>
                                </article>
                            </div>
                        </section>
                    </section>
                @endforeach
            </div>

            <div class="form-actions">
                <button type="submit" class="pill">{{ $submitLabel }}</button>
                @if ($isEditing)
                    <a href="{{ route('admin.blog-posts.preview', $blogPost) }}" class="ghost-pill" target="_blank" rel="noopener noreferrer">Open volledige preview</a>
                @endif
                <a href="{{ route('admin.blog-posts.index') }}" class="ghost-pill">Terug naar overzicht</a>
            </div>
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const escapeHtml = (value) => value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const renderMarkdown = (markdown) => {
                const lines = String(markdown || '').split(/\r?\n/);
                const blocks = [];
                let listItems = [];

                const renderInlineMarkdown = (value) => {
                    let rendered = escapeHtml(value);

                    rendered = rendered.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                    rendered = rendered.replace(/(^|[\s(])\*(?!\*)(.+?)\*(?!\*)/g, '$1<em>$2</em>');
                    rendered = rendered.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');

                    return rendered;
                };

                const parseShortcodeAttributes = (value) => {
                    const attributes = {};

                    String(value || '').replace(/(\w+)="([^"]*)"/g, (_, key, attributeValue) => {
                        attributes[key] = attributeValue;
                        return '';
                    });

                    return attributes;
                };

                const normalizeCssDimension = (value) => {
                    const trimmed = String(value || '').trim();

                    if (trimmed === '') {
                        return '';
                    }

                    return /^\d+$/.test(trimmed) ? `${trimmed}px` : trimmed;
                };

                const mediaAlignmentClass = (align) => {
                    if (align === 'left') {
                        return 'article-media article-media--left';
                    }

                    if (align === 'right') {
                        return 'article-media article-media--right';
                    }

                    return 'article-media article-media--center';
                };

                const mediaWrapperStyle = (attributes) => {
                    const styles = [];

                    if (attributes.width) {
                        styles.push(`max-width: ${normalizeCssDimension(attributes.width)}`);
                    }

                    return styles.length > 0 ? ` style="${styles.join('; ')}"` : '';
                };

                const mediaElementStyle = (attributes) => {
                    const styles = ['width: 100%', 'height: auto'];

                    if (attributes.height) {
                        styles[1] = `height: ${normalizeCssDimension(attributes.height)}`;
                        styles.push('object-fit: cover');
                    }

                    return ` style="${styles.join('; ')}"`;
                };

                const flushList = () => {
                    if (listItems.length === 0) {
                        return;
                    }

                    blocks.push(`<ul>${listItems.map((item) => `<li>${item}</li>`).join('')}</ul>`);
                    listItems = [];
                };

                for (const line of lines) {
                    const trimmed = line.trim();

                    if (trimmed === '') {
                        flushList();
                        continue;
                    }

                    const videoMatch = trimmed.match(/^\[video url="([^"]+)"\]$/i);

                    if (videoMatch) {
                        flushList();
                        blocks.push(`<figure class="article-media article-media--center"><video controls src="${escapeHtml(videoMatch[1])}" style="width: 100%; height: auto"></video></figure>`);
                        continue;
                    }

                    const imageShortcodeMatch = trimmed.match(/^\[image\s+([^\]]+)\]$/i);

                    if (imageShortcodeMatch) {
                        flushList();

                        const attributes = parseShortcodeAttributes(imageShortcodeMatch[1]);

                        if (attributes.url) {
                            blocks.push(`<figure class="${mediaAlignmentClass(attributes.align)}"${mediaWrapperStyle(attributes)}><img src="${escapeHtml(attributes.url)}" alt="${escapeHtml(attributes.alt || '')}"${mediaElementStyle(attributes)}></figure>`);
                        }

                        continue;
                    }

                    const imageMatch = trimmed.match(/^!\[([^\]]*)\]\(([^)]+)\)$/);

                    if (imageMatch) {
                        flushList();
                        blocks.push(`<img src="${escapeHtml(imageMatch[2])}" alt="${escapeHtml(imageMatch[1])}">`);
                        continue;
                    }

                    const headingMatch = trimmed.match(/^(#{1,3})\s*(.+)$/);

                    if (headingMatch) {
                        flushList();

                        const headingLevel = headingMatch[1].length;
                        const headingText = renderInlineMarkdown(headingMatch[2].trim());

                        blocks.push(`<h${headingLevel}>${headingText}</h${headingLevel}>`);
                        continue;
                    }

                    if (trimmed.startsWith('- ')) {
                        listItems.push(renderInlineMarkdown(trimmed.slice(2)));
                        continue;
                    }

                    flushList();

                    blocks.push(`<p>${renderInlineMarkdown(trimmed)}</p>`);
                }

                flushList();

                return blocks.join('');
            };

            document.querySelectorAll('[data-preview-content]').forEach((contentField) => {
                const locale = contentField.dataset.previewContent;
                const titleField = document.querySelector(`[data-preview-title="${locale}"]`);
                const excerptField = document.querySelector(`[data-preview-excerpt="${locale}"]`);
                const renderedTitle = document.querySelector(`[data-rendered-title="${locale}"]`);
                const renderedExcerpt = document.querySelector(`[data-rendered-excerpt="${locale}"]`);
                const renderedContent = document.querySelector(`[data-rendered-content="${locale}"]`);

                const syncPreview = () => {
                    if (renderedTitle) {
                        renderedTitle.textContent = titleField?.value || 'Titel preview';
                    }

                    if (renderedExcerpt) {
                        renderedExcerpt.textContent = excerptField?.value || 'Korte samenvatting voor SEO en social preview.';
                    }

                    if (renderedContent) {
                        renderedContent.innerHTML = renderMarkdown(contentField.value);
                    }
                };

                titleField?.addEventListener('input', syncPreview);
                excerptField?.addEventListener('input', syncPreview);
                contentField.addEventListener('input', syncPreview);

                syncPreview();
            });

            document.querySelectorAll('[data-insert-snippet]').forEach((button) => {
                button.addEventListener('click', () => {
                    const locale = button.dataset.insertSnippet;
                    const contentField = document.querySelector(`[data-preview-content="${locale}"]`);

                    if (! contentField) {
                        return;
                    }

                    const snippets = {
                        image: '\n\n![Korte alt-tekst](https://example.com/afbeelding.jpg)\n',
                        video: '\n\n[video url="https://example.com/video.mp4"]\n',
                        cta: '\n\n## Volgende stap\n\n- Benoem het concrete risico\n- Deel de belangrijkste observatie\n- Sluit af met een duidelijke vervolgstap\n',
                    };

                    const snippet = snippets[button.dataset.snippetType] || '';
                    const start = contentField.selectionStart ?? contentField.value.length;
                    const end = contentField.selectionEnd ?? contentField.value.length;

                    contentField.value = `${contentField.value.slice(0, start)}${snippet}${contentField.value.slice(end)}`;
                    contentField.dispatchEvent(new Event('input', { bubbles: true }));
                    contentField.focus();
                });
            });
        });
    </script>
</x-layouts.hermes-admin>
