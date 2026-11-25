@props(['donor'])

@php
    $isAvailable = $donor->is_available;
    $reviewCount = $donor->rating_count;
@endphp

<article class="card card-hover flex flex-col gap-4 p-5">
    <div class="flex items-start gap-3">
        <img src="{{ $donor->user->avatarUrl() }}" alt="{{ $donor->user->name }}"
             class="h-12 w-12 shrink-0 rounded-lg object-cover">

        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-1.5">
                <h3 class="truncate text-[15px] font-medium text-ink">{{ $donor->user->name }}</h3>
                <svg class="h-4 w-4 shrink-0 text-brand-700" viewBox="0 0 20 20" fill="currentColor" aria-label="ভেরিফাইড">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="truncate text-xs text-ink-muted">@ {{ $donor->user->username }}</p>
            <x-star-rating :rating="$donor->rating_avg" :count="$reviewCount" class="mt-1" />
        </div>

        <x-blood-badge :group="$donor->blood_group" />
    </div>

    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-ink-soft">
        <span class="inline-flex items-center gap-1.5">
            <svg class="h-3.5 w-3.5 text-ink-muted" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9.7 1.7a1 1 0 0 1 1.4.1c1.3 1.6 4.4 5.7 4.4 8.7a5 5 0 1 1-10 0c0-3 3.1-7.1 4.2-8.8ZM10 12a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" clip-rule="evenodd"/></svg>
            {{ $donor->locationLabel() ?: 'ঠিকানা দেওয়া নেই' }}
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full {{ $isAvailable ? 'bg-[#16a34a]' : 'bg-brand-600' }}"></span>
            @if ($isAvailable)
                রক্ত দিতে পারবেন
            @else
                অনুপলব্ধ{{ $donor->availableInLabel() ? ' · '.$donor->availableInLabel() : '' }}
            @endif
        </span>
    </div>

    <div class="mt-auto flex items-center gap-2 pt-1">
        @auth
            <button type="button"
                    data-reveal-phone="{{ route('donors.reveal', $donor->user->username) }}"
                    data-donor-name="{{ $donor->user->name }}"
                    class="btn-primary btn-sm flex-1">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2 3.5A1.5 1.5 0 0 1 3.5 2h2.6a1 1 0 0 1 .97.76l.86 3.45a1 1 0 0 1-.5 1.12l-1.6.9a11.3 11.3 0 0 0 5.94 5.94l.9-1.6a1 1 0 0 1 1.12-.5l3.45.86a1 1 0 0 1 .76.97v2.6A1.5 1.5 0 0 1 16.5 18C8.5 18 2 11.5 2 3.5Z"/></svg>
                নম্বর দেখুন
            </button>
        @else
            <a href="{{ route('login') }}" class="btn-primary btn-sm flex-1">নম্বর দেখতে লগইন</a>
        @endauth
        <a href="{{ route('donors.show', $donor->user->username) }}" class="btn-secondary btn-sm">
            প্রোফাইল
        </a>
    </div>
</article>
