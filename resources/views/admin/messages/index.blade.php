@extends('layouts.admin')

@section('title', 'বার্তা')
@section('heading', 'বার্তা')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">বার্তা ও নোটিফিকেশন</h2>
            <p class="mt-1 text-sm text-slate-600">সিস্টেম থেকে পাঠানো সব বার্তার লগ। মোট {{ number_format($messages->total()) }} টি।</p>
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
                        <h3 class="text-base font-bold text-slate-900">{{ $message->subject }}</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            প্রেরক: <span class="font-medium text-slate-700">{{ $message->sender?->name ?? 'সিস্টেম' }}</span>
                            <span class="mx-1 text-slate-300">→</span>
                            প্রাপক: <span class="font-medium text-slate-700">{{ $message->recipient?->name ?? 'মুছে ফেলা ইউজার' }}</span>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="badge bg-violet-50 text-violet-700">{{ $channelLabels[$message->channel] ?? $message->channel }}</span>
                        @if ($message->read_at)
                            <span class="badge bg-emerald-50 text-emerald-700">পঠিত</span>
                        @else
                            <span class="badge bg-amber-50 text-amber-700">অপঠিত</span>
                        @endif
                    </div>
                </div>

                <p class="mt-3 rounded-xl bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-700">{{ $message->body }}</p>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                    <span>@ {{ $message->recipient?->username ?? 'unknown' }} · {{ $message->recipient?->email ?? '—' }}</span>
                    <span>{{ $message->created_at->format('d M Y, h:i A') }} · {{ $message->created_at->diffForHumans() }}</span>
                </div>
            </article>
        @empty
            <div class="card p-10 text-center">
                <p class="text-4xl">✉️</p>
                <p class="mt-3 text-sm font-semibold text-slate-700">এখনো কোনো বার্তা পাঠানো হয়নি</p>
                <a href="{{ route('admin.messages.create') }}" class="btn-primary btn-sm mt-3">প্রথম বার্তা পাঠান</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $messages->links() }}</div>
@endsection
