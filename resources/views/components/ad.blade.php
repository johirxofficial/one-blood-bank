@props(['placement' => 'sidebar', 'class' => ''])

@php
    $ads = \App\Models\Setting::get('ads.enabled', false)
        ? \App\Models\Ad::query()->running()->forPlacement(\App\Enums\AdPlacement::from($placement))->get()
        : collect();
@endphp

@if ($ads->isNotEmpty())
    <div class="space-y-3 {{ $class }}">
        @foreach ($ads as $ad)
            @if ($ad->type === \App\Enums\AdType::Banner && $ad->image_path)
                <a href="{{ $ad->target_url ?: '#' }}" target="_blank" rel="noopener sponsored"
                   class="block overflow-hidden rounded-xl border border-slate-200">
                    <img src="{{ asset('storage/'.$ad->image_path) }}" alt="{{ $ad->title }}" class="w-full" loading="lazy">
                </a>
            @elseif ($ad->code)
                <div class="overflow-hidden rounded-xl">{!! $ad->code !!}</div>
            @endif
        @endforeach
    </div>
@endif
