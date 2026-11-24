@extends('layouts.admin')

@section('title', 'ড্যাশবোর্ড')
@section('heading', 'ড্যাশবোর্ড')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">স্বাগতম, {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-sm text-slate-600">সাইটের সারসংক্ষেপ ও সাম্প্রতিক কার্যক্রম এক নজরে দেখুন।</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.donors.index', ['status' => 'pending']) }}" class="btn-primary">অপেক্ষমাণ ডোনার</a>
            <a href="{{ route('admin.messages.create') }}" class="btn-secondary">বার্তা পাঠান</a>
        </div>
    </div>

    @php
        $cards = [
            ['label' => 'মোট ইউজার', 'value' => $stats['users'], 'icon' => '👥', 'route' => route('admin.users.index'), 'tone' => 'bg-sky-50 text-sky-700'],
            ['label' => 'ভেরিফাইড ডোনার', 'value' => $stats['donors'], 'icon' => '🩸', 'route' => route('admin.donors.index', ['status' => 'approved']), 'tone' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'অপেক্ষমাণ আবেদন', 'value' => $stats['pending'], 'icon' => '⏳', 'route' => route('admin.donors.index', ['status' => 'pending']), 'tone' => 'bg-amber-50 text-amber-700'],
            ['label' => 'চলমান অনুরোধ', 'value' => $stats['requests'], 'icon' => '🚑', 'route' => route('admin.requests.index'), 'tone' => 'bg-rose-50 text-rose-700'],
            ['label' => 'মোট রিভিউ', 'value' => $stats['ratings'], 'icon' => '⭐', 'route' => route('admin.ratings.index'), 'tone' => 'bg-violet-50 text-violet-700'],
            ['label' => 'আজকের মেইল', 'value' => $stats['mails'], 'icon' => '📬', 'route' => route('admin.logs.mail'), 'tone' => 'bg-slate-100 text-slate-700'],
            ['label' => 'ব্লগ পোস্ট', 'value' => $stats['posts'], 'icon' => '📝', 'route' => route('admin.blog.index'), 'tone' => 'bg-indigo-50 text-indigo-700'],
            ['label' => 'অপেক্ষমাণ ডোনেশন', 'value' => $stats['pending_donations'], 'icon' => '💝', 'route' => route('admin.site-donations.index', ['status' => 'pending']), 'tone' => 'bg-pink-50 text-pink-700'],
            ['label' => 'যাচাইকৃত সহযোগিতা', 'value' => $stats['donation_amount'], 'icon' => '💰', 'route' => route('admin.site-donations.index'), 'tone' => 'bg-emerald-50 text-emerald-700'],
        ];
    @endphp

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($cards as $card)
            <a href="{{ $card['route'] }}" class="stat-card card-hover">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl text-xl {{ $card['tone'] }}">{{ $card['icon'] }}</span>
                <span class="min-w-0">
                    <span class="block text-2xl font-extrabold leading-tight text-slate-900">{{ number_format($card['value']) }}</span>
                    <span class="block text-xs font-medium text-slate-500">{{ $card['label'] }}</span>
                </span>
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        {{-- Pending donor applications --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">অপেক্ষমাণ ডোনার আবেদন</h3>
                    <p class="text-xs text-slate-500">যাচাই করে অনুমোদন বা কারণসহ বাতিল করুন।</p>
                </div>
                <a href="{{ route('admin.donors.index', ['status' => 'pending']) }}" class="btn-secondary btn-sm">সব দেখুন</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($pendingDonors as $donor)
                    <div class="flex items-center gap-3 px-5 py-3.5">
                        <img src="{{ $donor->user->avatarUrl() }}" alt="{{ $donor->user->name }}" class="h-10 w-10 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $donor->user->name }}</p>
                            <p class="truncate text-xs text-slate-500">
                                @ {{ $donor->user->username }} · {{ $donor->locationLabel() ?: 'এলাকা নেই' }}
                            </p>
                        </div>
                        <span class="badge bg-brand-50 text-brand-700">{{ $donor->blood_group }}</span>
                        <a href="{{ route('admin.donors.show', $donor) }}" class="btn-secondary btn-sm">পর্যালোচনা</a>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <p class="text-3xl">✅</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">কোনো অপেক্ষমাণ আবেদন নেই</p>
                        <p class="mt-1 text-xs text-slate-500">সব আবেদন প্রসেস করা হয়েছে।</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Latest ratings --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">সাম্প্রতিক রিভিউ</h3>
                    <p class="text-xs text-slate-500">ডোনারদের পাওয়া সর্বশেষ রেটিং।</p>
                </div>
                <a href="{{ route('admin.ratings.index') }}" class="btn-secondary btn-sm">মডারেশন</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($latestRatings as $rating)
                    <div class="px-5 py-3.5">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $rating->donor?->user?->name ?? 'ডোনার' }}
                            </p>
                            <span class="text-xs text-amber-500">{{ str_repeat('★', (int) $rating->rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - (int) $rating->rating) }}</span></span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $rating->user?->name ?? 'ইউজার' }} · {{ $rating->created_at->diffForHumans() }}
                        </p>
                        @if ($rating->comment)
                            <p class="mt-1.5 line-clamp-2 text-sm text-slate-600">{{ $rating->comment }}</p>
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-500">এখনো কোনো রিভিউ আসেনি।</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        {{-- Latest donors --}}
        <div class="card p-5">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-base font-bold text-slate-900">নতুন ভেরিফাইড ডোনার</h3>
                <a href="{{ route('admin.donors.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">সব ডোনার →</a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($latestDonors as $donor)
                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
                        <img src="{{ $donor->user->avatarUrl() }}" alt="{{ $donor->user->name }}" class="h-9 w-9 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $donor->user->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $donor->locationLabel() ?: 'এলাকা নেই' }}</p>
                        </div>
                        <span class="text-xs text-slate-400">{{ $donor->verified_at?->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">এখনো কোনো ভেরিফাইড ডোনার নেই।</p>
                @endforelse
            </div>
        </div>

        {{-- Recent activity --}}
        <div class="card p-5">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-base font-bold text-slate-900">সাম্প্রতিক কার্যক্রম</h3>
                <a href="{{ route('admin.logs.activity') }}" class="text-xs font-semibold text-brand-700 hover:underline">সব লগ →</a>
            </div>

            <ol class="mt-4 space-y-3">
                @forelse ($recentActivity as $log)
                    <li class="flex gap-3">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>
                        <div class="min-w-0">
                            <p class="text-sm text-slate-700">
                                <span class="font-semibold text-slate-900">{{ $log->user?->name ?? 'সিস্টেম' }}</span>
                                — {{ $log->description ?: $log->action }}
                            </p>
                            <p class="mt-0.5 text-[11px] text-slate-400">{{ $log->created_at?->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">কোনো কার্যক্রম লগ নেই।</li>
                @endforelse
            </ol>
        </div>
    </div>
@endsection
