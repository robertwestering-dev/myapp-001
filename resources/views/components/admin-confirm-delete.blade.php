@props([
    'action',
    'cancelHref',
    'confirmLabel' => 'JA, verwijderen',
    'cancelLabel' => 'NEE, annuleren',
    'confirmClass' => 'pill',
    'method' => 'DELETE',
])

<style>
    .confirm-card {
        display: grid;
        gap: 18px;
    }

    .confirm-card p {
        color: var(--muted);
        line-height: 1.7;
    }

    .confirm-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 8px;
    }
</style>

<section class="content-panel confirm-card">
    {{ $slot }}

    <div class="confirm-actions">
        <form method="POST" action="{{ $action }}">
            @csrf
            @method($method)

            <button type="submit" class="{{ $confirmClass }}">{{ $confirmLabel }}</button>
        </form>

        <a href="{{ $cancelHref }}" class="ghost-pill">{{ $cancelLabel }}</a>
    </div>
</section>
