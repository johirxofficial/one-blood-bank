@props(['rating' => 0, 'count' => 0, 'showCount' => true])

@php
    $rating = (float) $rating;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 text-sm']) }}>
    <span class="text-amber-500" aria-hidden="true">
        @for ($i = 1; $i <= 5; $i++)
            <span class="{{ $i <= round($rating) ? 'opacity-100' : 'opacity-25' }}">★</span>
        @endfor
    </span>
    <span class="font-semibold text-slate-700">{{ number_format($rating, 1) }}</span>
    @if ($showCount)
        <span class="text-xs text-slate-500">({{ $count }})</span>
    @endif
</span>
