@props([
    'paginator',
    'ariaLabel' => 'Paginatie',
    'linkMode' => 'window',
    'previousLabel' => 'Vorige',
    'nextLabel' => 'Volgende',
    'rangeText' => null,
])

@php
    $rangeText ??= sprintf(
        'Resultaten %d t/m %d van %d',
        $paginator->firstItem() ?? 0,
        $paginator->lastItem() ?? 0,
        $paginator->total()
    );
@endphp

<div {{ $attributes->class('meta') }}>
    <div class="muted">
        {{ $rangeText }}
    </div>

    @if ($paginator->hasPages())
        <nav class="pagination" aria-label="{{ $ariaLabel }}">
            @if ($linkMode === 'collection')
                @foreach ($paginator->linkCollection() as $link)
                    @if ($link['url'] === null)
                        <span class="pagination__current">{{ $link['label'] }}</span>
                    @elseif ($link['active'])
                        <span class="pagination__current">{{ $link['label'] }}</span>
                    @else
                        <a href="{{ $link['url'] }}" class="pagination__link">{!! $link['label'] !!}</a>
                    @endif
                @endforeach
            @elseif ($linkMode === 'simple')
                @if ($paginator->onFirstPage())
                    <span class="pagination__link">{{ $previousLabel }}</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination__link">{{ $previousLabel }}</a>
                @endif

                <span class="pagination__current">{{ $paginator->currentPage() }}</span>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination__link">{{ $nextLabel }}</a>
                @else
                    <span class="pagination__link">{{ $nextLabel }}</span>
                @endif
            @else
                @if ($paginator->onFirstPage())
                    <span class="pagination__link muted">{{ $previousLabel }}</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="pagination__link">{{ $previousLabel }}</a>
                @endif

                @foreach (range(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page)
                    @if ($page === $paginator->currentPage())
                        <span class="pagination__current">{{ $page }}</span>
                    @else
                        <a href="{{ $paginator->url($page) }}" class="pagination__link">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="pagination__link">{{ $nextLabel }}</a>
                @else
                    <span class="pagination__link muted">{{ $nextLabel }}</span>
                @endif
            @endif
        </nav>
    @endif
</div>
