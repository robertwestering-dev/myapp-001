<x-layouts.hermes-admin
    title="Admin assets"
    eyebrow="Assets"
    heading="Assetbibliotheek"
    lead="Upload hier afbeeldingen, video’s en bestanden naar de server. Gebruik daarna de directe URL of embed-snippet in blogposts en andere content."
    menu-active="media-assets"
    :show-secondary-menu-items="false"
    :show-hero="false"
>
    <style>
        .asset-toolbar,
        .asset-meta,
        .asset-card,
        .asset-grid,
        .asset-form {
            display: grid;
            gap: 16px;
        }

        .asset-toolbar {
            margin-bottom: 24px;
        }

        .asset-form {
            padding: 24px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.56);
        }

        .asset-form__fields {
            display: grid;
            gap: 16px;
            grid-template-columns: minmax(0, 1.2fr) minmax(240px, 0.8fr) auto;
            align-items: end;
        }

        .asset-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .asset-card {
            padding: 22px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.56);
        }

        .asset-preview {
            width: 100%;
            aspect-ratio: 16 / 10;
            border-radius: 18px;
            overflow: hidden;
            background: rgba(32, 69, 58, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .asset-preview img,
        .asset-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .asset-preview span {
            padding: 20px;
            text-align: center;
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
        }

        .asset-meta {
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.92rem;
        }

        .asset-code {
            width: 100%;
            min-height: 68px;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
            resize: vertical;
        }

        label {
            display: grid;
            gap: 8px;
            font-family: Arial, Helvetica, sans-serif;
        }

        input[type="file"],
        input[type="text"],
        input[readonly] {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .helper {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 24px;
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

        @media (max-width: 900px) {
            .asset-form__fields {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="content-panel">
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="asset-toolbar">
            <x-hermes-section-header
                tagline="Server assets"
                heading="Assetbibliotheek"
                description="Upload en embed media vanuit één centrale bibliotheek. Gebruik de directe URL voor omslagafbeeldingen of plak de embed-snippet direct in Markdown-content van een blogpost. Video’s gebruiken een shortcode, dus geen handmatige HTML."
            />

            <form method="POST" action="{{ route('admin.media-assets.store') }}" enctype="multipart/form-data" class="asset-form">
                @csrf

                <div class="asset-form__fields">
                    <label>
                        <span>Bestand</span>
                        <input type="file" name="asset" required>
                    </label>

                    <label>
                        <span>Alt-tekst of omschrijving</span>
                        <input type="text" name="alt_text" value="{{ old('alt_text') }}" placeholder="Bijvoorbeeld Teamworkshop voorjaar 2026">
                    </label>

                    <button type="submit" class="pill">Asset uploaden</button>
                </div>

                <div class="helper">Ondersteund: JPG, PNG, WEBP, GIF, SVG, MP4, MOV, WEBM, OGG en PDF. Maximaal 50 MB per bestand.</div>
            </form>
        </div>

        <div class="asset-grid">
            @forelse ($mediaAssets as $mediaAsset)
                <article class="asset-card">
                    <div class="asset-preview">
                        @if ($mediaAsset->isImage())
                            <img src="{{ $mediaAsset->url() }}" alt="{{ $mediaAsset->alt_text ?: $mediaAsset->original_name }}">
                        @elseif ($mediaAsset->isVideo())
                            <video controls preload="metadata">
                                <source src="{{ $mediaAsset->url() }}" type="{{ $mediaAsset->mime_type }}">
                            </video>
                        @else
                            <span>{{ strtoupper($mediaAsset->extension ?: $mediaAsset->asset_type) }}</span>
                        @endif
                    </div>

                    <div>
                        <h2>{{ $mediaAsset->original_name }}</h2>
                        <div class="asset-meta">
                            <span>{{ $mediaAsset->formattedSize() }}</span>
                            <span>{{ strtoupper($mediaAsset->asset_type) }}</span>
                            <span>{{ $mediaAsset->uploader?->name ?? 'Onbekend' }}</span>
                        </div>
                    </div>

                    <label>
                        <span>Directe URL</span>
                        <input type="text" readonly value="{{ $mediaAsset->absoluteUrl() }}">
                    </label>

                    <label>
                        <span>Embed-snippet</span>
                        <textarea readonly class="asset-code">{{ $mediaAsset->embedSnippet() }}</textarea>
                    </label>
                </article>
            @empty
                <article class="asset-card">
                    <h2>Nog geen assets geüpload</h2>
                    <p class="helper">Upload hierboven je eerste afbeelding, video of bestand. Daarna kun je de URL of embed-snippet direct in een blogpost gebruiken.</p>
                </article>
            @endforelse
        </div>

        @if ($mediaAssets->hasPages())
            <div class="pagination">
                @if ($mediaAssets->onFirstPage())
                    <span class="pagination__current">1</span>
                @else
                    <a href="{{ $mediaAssets->previousPageUrl() }}" class="pagination__link">Vorige</a>
                @endif

                <span class="pagination__current">{{ $mediaAssets->currentPage() }}</span>

                @if ($mediaAssets->hasMorePages())
                    <a href="{{ $mediaAssets->nextPageUrl() }}" class="pagination__link">Volgende</a>
                @endif
            </div>
        @endif
    </section>
</x-layouts.hermes-admin>
