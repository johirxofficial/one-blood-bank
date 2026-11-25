@extends('layouts.admin')

@section('title', 'বার্তা')
@section('heading', 'বার্তা')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">বার্তা ও নোটিফিকেশন</h2>
            <p class="mt-1 text-sm text-ink-soft">সিস্টেম থেকে পাঠানো সব বার্তার লগ। মোট {{ number_format($messages->total()) }} টি।</p>
        </div>
        <a href="{{ route('admin.messages.create') }}" class="btn-primary">+ নতুন বার্তা</a>
    </div>

    <div class="mt-5 space-y-4">
        @forelse ($messages as $message)
            @php
                $channelLabels = ['email' => 'ইমেইল', 'internal' => 'সাইট বার্তা', 'both' => 'ইমেইল + সাইট'];
            @endphp
            <article class="card p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-ink">{{ $message->subject }}</h3>
                        <p class="mt-1 text-xs text-ink-muted">
                            প্রেরক: <span class="font-medium text-ink-soft">{{ $message->sender?->name ?? 'সিস্টেম' }}</span>
                            <span class="mx-1 text-ink-muted">→</span>
                            প্রাপক: <span class="font-medium text-ink-soft">{{ $message->recipient?->name ?? 'মুছে ফেলা ইউজার' }}</span>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge bg-violet-50 text-violet-700">{{ $channelLabels[$message->channel] ?? $message->channel }}</span>
                        @if ($message->read_at)
                            <span class="badge bg-[#edf5ee] text-[#15803d]">পঠিত</span>
                        @else
                            <span class="badge bg-[#f8efd9] text-[#8a5a12]">অপঠিত</span>
                        @endif
                    </div>
                </div>

                <p class="mt-3 rounded-xl bg-raised/60 px-4 py-3 text-sm leading-relaxed text-ink-soft">{{ $message->body }}</p>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-ink-muted">
                    <span>@ {{ $message->recipient?->username ?? 'unknown' }} · {{ $message->recipient?->email ?? '—' }}</span>
                    <span>{{ $message->created_at->format('d M Y, h:i A') }} · {{ $message->created_at->diffForHumans() }}</span>
                </div>
            </article>
        @empty
            <div class="card p-10 text-center">
                <svg class="mx-auto h-6 w-6 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4 7 8 5.5L20 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p class="mt-3 text-sm font-medium text-ink-soft">এখনো কোনো বার্তা পাঠানো হয়নি</p>
                <a href="{{ route('admin.messages.create') }}" class="btn-primary btn-sm mt-3">প্রথম বার্তা পাঠান</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $messages->links() }}</div>
@endsection
