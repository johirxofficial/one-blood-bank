@extends('layouts.app')

@section('title', 'ড্যাশবোর্ড — One Blood Bank')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">স্বাগতম, {{ $user->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">@ {{ $user->username }} · {{ $user->role->label() }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('profile.edit') }}" class="btn-secondary">প্রোফাইল সেটিংস</a>
            @if ($donor?->isApproved())
                <a href="{{ route('donor.card') }}" class="btn-secondary">ডোনার কার্ড</a>
                <a href="{{ route('donor.apply') }}" class="btn-primary">তথ্য আপডেট</a>
            @else
                <a href="{{ route('donor.apply') }}" class="btn-primary">ডোনার হতে আবেদন করুন</a>
            @endif
        </div>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        {{-- Donor status --}}
        <div class="card p-5 lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-base font-bold text-slate-900">ডোনার স্ট্যাটাস</h2>
                @if ($donor)
                    <span class="badge {{ $donor->status->badgeClasses() }}">{{ $donor->status->label() }}</span>
                @else
                    <span class="badge bg-slate-100 text-slate-600">ডোনার নন</span>
                @endif
            </div>

            @if (! $donor)
                <div class="mt-4 rounded-xl bg-slate-50 p-5 text-center">
                    <p class="text-3xl">🩸</p>
                    <h3 class="mt-2 font-bold text-slate-900">আপনি এখনো ডোনার নন</h3>
                    <p class="mt-1 text-sm text-slate-600">আবেদন করুন — যাচাইয়ের পর আপনার প্রোফাইল সবার জন্য উন্মুক্ত হবে।</p>
                    <a href="{{ route('donor.apply') }}" class="btn-primary mt-4">ডোনার আবেদন করুন</a>
                </div>
            @elseif ($donor->status === \App\Enums\DonorStatus::Pending)
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <p class="font-semibold">আপনার আবেদন যাচাই করা হচ্ছে।</p>
                    <p class="mt-1">আমাদের টিম আপনার নম্বরে কল করে যাচাই করবে। সাধারণত ২৪–৪৮ ঘণ্টা লাগে।</p>
                </div>
            @elseif ($donor->status === \App\Enums\DonorStatus::Rejected)
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">
                    <p class="font-semibold">আবেদন বাতিল হয়েছে</p>
                    <p class="mt-1">কারণ: {{ $donor->rejection_reason ?: 'উল্লেখ নেই' }}</p>
                    <a href="{{ route('donor.apply') }}" class="btn-secondary mt-3">তথ্য ঠিক করে আবার আবেদন করুন</a>
                </div>
            @elseif ($donor->status === \App\Enums\DonorStatus::Approved)
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs text-slate-500">রক্তের গ্রুপ</p>
                        <p class="mt-1 text-2xl font-extrabold text-brand-700">{{ $donor->blood_group }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs text-slate-500">উপলব্ধতা</p>
                        <p class="mt-1 text-lg font-bold {{ $donor->is_available ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $donor->is_available ? 'রক্ত দিতে পারবেন' : 'অনুপলব্ধ' }}
                        </p>
                        @if (! $donor->is_available && $donor->available_from)
                            <p class="text-xs text-slate-500">আবার সম্ভব: {{ $donor->available_from->format('d M Y') }}</p>
                        @endif
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs text-slate-500">মোট রক্তদান</p>
                        <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ $donor->total_donations }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs text-slate-500">রেটিং</p>
                        <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format((float) $donor->rating_avg, 1) }} <span class="text-sm font-medium text-slate-400">/ ৫</span></p>
                        <p class="text-xs text-slate-500">{{ $donor->rating_count }} টি রিভিউ</p>
                    </div>
                </div>
            @else
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    আপনার ডোনার অ্যাকাউন্ট বর্তমানে স্থগিত রয়েছে। বিস্তারিত জানতে সাপোর্টে যোগাযোগ করুন।
                </div>
            @endif
        </div>

        {{-- Reveal limit --}}
        <div class="space-y-4">
            <div class="card p-5">
                <h2 class="text-base font-bold text-slate-900">নম্বর দেখার সীমা</h2>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-extrabold text-slate-900">{{ $revealedToday }}<span class="text-base font-medium text-slate-400"> / {{ $revealLimit === 0 ? 'আনলিমিটেড' : $revealLimit }}</span></p>
                    <span class="text-xs text-slate-500">আজ</span>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-brand-600" style="width: {{ $revealLimit > 0 ? min(100, round($revealedToday / $revealLimit * 100)) : 100 }}%"></div>
                </div>
                <p class="mt-2 text-xs text-slate-500">
                    @if ($revealLimit === 0)
                        আজ আপনি আনলিমিটেড ডোনারের নম্বর দেখতে পারবেন।
                    @else
                        প্রতিদিন সর্বোচ্চ {{ $revealLimit }} জন ডোনারের নম্বর দেখতে পারবেন। সীমা শেষ হলে পরদিন আবার চেষ্টা করুন।
                    @endif
                </p>
                <a href="{{ route('donors.index') }}" class="btn-secondary mt-3 w-full">ডোনার খুঁজুন</a>
            </div>

            <div class="card p-5">
                <h2 class="text-base font-bold text-slate-900">অ্যাকাউন্ট নিরাপত্তা</h2>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li class="flex items-center justify-between">
                        <span>মোবাইল ভেরিফাইড</span>
                        @if ($user->phone_verified_at)
                            <span class="text-emerald-600">✓</span>
                        @elseif (\App\Models\Setting::phoneVerificationRequired())
                            <a href="{{ route('verification.notice') }}" class="text-xs font-semibold text-amber-600 hover:underline">যাচাই করুন</a>
                        @else
                            <span class="text-xs text-slate-400">ঐচ্ছিক</span>
                        @endif
                    </li>
                    <li class="flex items-center justify-between">
                        <span>ইমেইল ভেরিফাইড</span>
                        @if ($user->email_verified_at)
                            <span class="text-emerald-600">✓</span>
                        @elseif (\App\Models\Setting::emailVerificationRequired())
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button class="text-xs font-semibold text-amber-600 hover:underline">লিংক পাঠান</button>
                            </form>
                        @else
                            <span class="text-xs text-slate-400">ঐচ্ছিক</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if ($donor?->isApproved())
        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            {{-- Add donation --}}
            <div class="card p-5">
                <h2 class="text-base font-bold text-slate-900">রক্তদানের তথ্য যোগ করুন</h2>
                <p class="mt-1 text-xs text-slate-500">রক্তদানের পর তারিখ যোগ করলে ৪ মাস আপনি “অনুপলব্ধ” হিসেবে দেখাবেন।</p>

                <form method="POST" action="{{ route('dashboard.donations.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="label" for="donated_at">রক্তদানের তারিখ</label>
                        <input type="date" name="donated_at" id="donated_at" class="input" value="{{ old('donated_at', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="label" for="hospital">হাসপাতাল (ঐচ্ছিক)</label>
                        <input type="text" name="hospital" id="hospital" class="input" value="{{ old('hospital') }}" placeholder="যেমন: কুমিল্লা মেডিকেল কলেজ হাসপাতাল">
                    </div>
                    <button type="submit" class="btn-primary w-full">যোগ করুন</button>
                </form>
            </div>

            {{-- Donation history --}}
            <div class="card p-5">
                <h2 class="text-base font-bold text-slate-900">রক্তদানের ইতিহাস</h2>
                <ol class="mt-4 space-y-3">
                    @forelse ($donor->donations as $donation)
                        <li class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $donation->donated_at->format('d M Y') }}</p>
                                <p class="text-xs text-slate-500">{{ $donation->hospital ?: 'হাসপাতাল উল্লেখ নেই' }}</p>
                            </div>
                            <span class="badge bg-emerald-50 text-emerald-700">সম্পন্ন</span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">এখনো কোনো রক্তদানের তথ্য নেই।</li>
                    @endforelse
                </ol>
            </div>
        </div>
    @endif

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="text-base font-bold text-slate-900">আপনার রিভিউ</h2>
            <div class="mt-3 space-y-3">
                @forelse ($ratings as $rating)
                    <div class="rounded-lg bg-slate-50 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-800">{{ $rating->user?->name ?? 'ইউজার' }}</p>
                            <span class="text-xs text-amber-500">{{ str_repeat('★', $rating->rating) }}</span>
                        </div>
                        @if ($rating->comment)
                            <p class="mt-1 text-sm text-slate-600">{{ $rating->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">এখনো কোনো রিভিউ আসেনি।</p>
                @endforelse
            </div>
        </div>

        <div class="card p-5">
            <h2 class="text-base font-bold text-slate-900">বার্তা</h2>
            <div class="mt-3 space-y-3">
                @forelse ($messages as $message)
                    <div class="rounded-lg border border-slate-100 p-3">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $message->subject }}</p>
                            <span class="text-[11px] text-slate-400">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit($message->body, 160) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">কোনো বার্তা নেই।</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
