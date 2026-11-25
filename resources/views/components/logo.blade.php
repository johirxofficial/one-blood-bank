@props(['compact' => false, 'inverse' => false])

@php
    $logoPath = \App\Models\Setting::get('appearance.logo_path');
    $hasLogo = $logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath);
    $siteName = \App\Models\Setting::get('site.name', 'One Blood Bank');
    $tagline = \App\Models\Setting::get('site.tagline', 'রক্ত দিন, জীবন বাঁচান');
@endphp

<a href="{{ route('home') }}" class="inline-flex min-w-0 items-center gap-2.5">
    @if ($hasLogo)
        <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $siteName }}"
             class="h-9 w-9 shrink-0 rounded-md bg-surface object-contain">
    @else
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
             class="h-7 w-7 shrink-0 {{ $inverse ? 'text-white' : 'text-brand-700' }}" aria-hidden="true">
            <path d="M12 2.25c-.32 0-.62.14-.82.39C9.4 5.1 5.25 10.4 5.25 14.25a6.75 6.75 0 0 0 13.5 0c0-3.85-4.15-9.15-5.93-11.61a1.04 1.04 0 0 0-.82-.39Z"/>
        </svg>
    @endif

    <span class="min-w-0 leading-tight">
        <span class="block truncate text-[1.02rem] font-medium tracking-[-0.01em] {{ $inverse ? 'text-white' : 'text-ink' }}">{{ $siteName }}</span>
        @unless ($compact)
            <span class="block truncate text-[11px] font-normal {{ $inverse ? 'text-white/70' : 'text-ink-muted' }}">{{ $tagline }}</span>
        @endunless
    </span>
</a>
