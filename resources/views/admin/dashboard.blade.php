@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড')
@section('heading', 'ড্যাশবোর্ড')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">স্বাগতম, {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm text-ink-soft">সাইটের সারসংক্ষেপ ও সাম্প্রতিক কার্যক্রম এক নজরে দেখুন।</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.donors.index', ['status' => 'pending']) }}" class="btn-primary">অপেক্ষমাণ ডোনার</a>
            <a href="{{ route('admin.messages.create') }}" class="btn-secondary">বার্তা পাঠান</a>
        </div>
    </div>

    @php
        $cards = [
            ['label' => 'মোট ইউজার', 'value' => $stats['users'], 'route' => route('admin.users.index')],
            ['label' => 'ভেরিফাইড ডোনার', 'value' => $stats['donors'], 'route' => route('admin.donors.index', ['status' => 'approved'])],
            ['label' => 'অপেক্ষমাণ আবেদন', 'value' => $stats['pending'], 'route' => route('admin.donors.index', ['status' => 'pending'])],
            ['label' => 'চলমান অনুরোধ', 'value' => $stats['requests'], 'route' => route('admin.requests.index')],
            ['label' => 'মোট রিভিউ', 'value' => $stats['ratings'], 'route' => route('admin.ratings.index')],
            ['label' => 'আজকের মেইল', 'value' => $stats['mails'], 'route' => route('admin.logs.mail')],
            ['label' => 'ব্লগ পোস্ট', 'value' => $stats['posts'], 'route' => route('admin.blog.index')],
            ['label' => 'অপেক্ষমাণ ডোনেশন', 'value' => $stats['pending_donations'], 'route' => route('admin.site-donations.index', ['status' => 'pending'])],
            ['label' => 'যাচাইকৃত সহযোগিতা', 'value' => $stats['donation_amount'], 'route' => route('admin.site-donations.index')],
        ];
    @endphp

    <div class="mt-6 grid gap-px overflow-hidden rounded-[14px] border border-line bg-line sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($cards as $card)
            <a href="{{ $card['route'] }}" class="bg-surface p-5 transition-colors hover:bg-raised/50">
                <span class="tnum block text-2xl font-medium leading-tight text-ink">{{ number_format($card['value']) }}</span>
                <span class="mt-1 block text-xs text-ink-muted">{{ $card['label'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        {{-- Pending donor applications --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
                <div>
                    <h3 class="text-base font-semibold text-ink">অপেক্ষমাণ ডোনার আবেদন</h3>
                    <p class="text-xs text-ink-muted">যাচাই করে অনুমোদন বা কারণসহ বাতিল করুন।</p>
                </div>
                <a href="{{ route('admin.donors.index', ['status' => 'pending']) }}" class="btn-secondary btn-sm">সব দেখুন</a>
            </div>

            <div class="divide-y divide-line">
                @forelse ($pendingDonors as $donor)
                    <div class="flex items-center gap-3 px-5 py-3.5">
                        <img src="{{ $donor->user->avatarUrl() }}" alt="{{ $donor->user->name }}" class="h-10 w-10 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">{{ $donor->user->name }}</p>
                            <p class="truncate text-xs text-ink-muted">
                                @ {{ $donor->user->username }} · {{ $donor->locationLabel() ?: 'এলাকা নেই' }}
                            </p>
                        </div>
                        <span class="badge bg-brand-50 text-brand-700">{{ $donor->blood_group }}</span>
                        <a href="{{ route('admin.donors.show', $donor) }}" class="btn-secondary btn-sm">পর্যালোচনা</a>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <svg class="mx-auto h-6 w-6 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <circle cx="12" cy="12" r="8.5"/><path d="m8 12 2.5 2.5L16 9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="mt-2 text-sm font-medium text-ink-soft">কোনো অপেক্ষমাণ আবেদন নেই</p>
                        <p class="mt-1 text-xs text-ink-muted">সব আবেদন প্রসেস করা হয়েছে।</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Latest ratings --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
                <div>
                    <h3 class="text-base font-semibold text-ink">সাম্প্রতিক রিভিউ</h3>
                    <p class="text-xs text-ink-muted">ডোনারদের পাওয়া সর্বশেষ রেটিং।</p>
                </div>
                <a href="{{ route('admin.ratings.index') }}" class="btn-secondary btn-sm">মডারেশন</a>
            </div>

            <div class="divide-y divide-line">
                @forelse ($latestRatings as $rating)
                    <div class="px-5 py-3.5">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-ink">
                                {{ $rating->donor?->user?->name ?? 'ডোনার' }}
                            </p>
                            <span class="text-xs text-brand-700">{{ str_repeat('★', (int) $rating->rating) }}<span class="text-ink-muted/30">{{ str_repeat('★', 5 - (int) $rating->rating) }}</span></span>
                        </div>
                        <p class="mt-1 text-xs text-ink-muted">
                            {{ $rating->user?->name ?? 'ইউজার' }} · {{ $rating->created_at->diffForHumans() }}
                        </p>
                        @if ($rating->comment)
                            <p class="mt-1.5 line-clamp-2 text-sm text-ink-soft">{{ $rating->comment }}</p>
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-ink-muted">এখনো কোনো রিভিউ আসেনি।</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        {{-- Latest donors --}}
        <div class="card p-5">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-ink">নতুন ভেরিফাইড ডোনার</h3>
                <a href="{{ route('admin.donors.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">সব ডোনার →</a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($latestDonors as $donor)
                    <div class="flex items-center gap-3 rounded-xl bg-raised/60 px-3 py-2.5">
                        <img src="{{ $donor->user->avatarUrl() }}" alt="{{ $donor->user->name }}" class="h-9 w-9 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">{{ $donor->user->name }}</p>
                            <p class="truncate text-xs text-ink-muted">{{ $donor->locationLabel() ?: 'এলাকা নেই' }}</p>
                        </div>
                        <span class="text-xs text-ink-muted">{{ $donor->verified_at?->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-ink-muted">এখনো কোনো ভেরিফাইড ডোনার নেই।</p>
                @endforelse
            </div>
        </div>

        {{-- Recent activity --}}
        <div class="card p-5">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-ink">সাম্প্রতিক কার্যক্রম</h3>
                <a href="{{ route('admin.logs.activity') }}" class="text-xs font-semibold text-brand-700 hover:underline">সব লগ →</a>
            </div>

            <ol class="mt-4 space-y-3">
                @forelse ($recentActivity as $log)
                    <li class="flex gap-3">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>
                        <div class="min-w-0">
                            <p class="text-sm text-ink-soft">
                                <span class="font-semibold text-ink">{{ $log->user?->name ?? 'সিস্টেম' }}</span>
                                — {{ $log->description ?: $log->action }}
                            </p>
                            <p class="mt-0.5 text-[11px] text-ink-muted">{{ $log->created_at?->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-ink-muted">কোনো কার্যক্রম লগ নেই।</li>
                @endforelse
            </ol>
        </div>
    </div>
@endsection
