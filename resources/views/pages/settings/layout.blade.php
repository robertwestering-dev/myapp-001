@props([
    'heading' => '',
    'subheading' => '',
    'eyebrow' => null,
])

<div class="settings-shell">
    <style>
        .settings-shell__panel {
            padding: 32px;
            display: grid;
            gap: 22px;
        }

        .settings-shell__content {
            display: grid;
            gap: 24px;
            width: 100%;
        }

        .settings-shell {
            display: grid;
        }
    </style>

    <section class="settings-shell__panel user-panel user-panel--compact">
        <x-user-page-heading
            :eyebrow="$eyebrow"
            :title="$heading"
            :text="$subheading"
        />

        <div class="settings-shell__content">
            {{ $slot }}
        </div>
    </section>
</div>
