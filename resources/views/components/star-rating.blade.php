@props(['rating' => 0, 'count' => 0, 'showCount' => true])

@php
    $rating = (float) $rating;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 text-xs']) }}>
    <span class="text-brand-700" aria-hidden="true">
        @for ($i = 1; $i <= 5; $i++)
            <span class="{{ $i <= round($rating) ? 'opacity-100' : 'opacity-20' }}">★</span>
        @endfor
    </span>
    <span class="tnum font-medium text-ink-soft">{{ number_format($rating, 1) }}</span>
    @if ($showCount)
        <span class="tnum text-ink-muted">({{ $count }})</span>
    @endif
</span>
