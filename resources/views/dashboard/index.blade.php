@extends('layouts.app')

@section('title', 'ড্যাশবোর্ড — One Blood Bank')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="font-display text-3xl font-normal tracking-[-0.01em] text-ink">স্বাগতম, {{ $user->name }}</h1>
            <p class="mt-1 text-sm text-ink-soft">@ {{ $user->username }} · {{ $user->role->label() }}</p>
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
        <div class="card self-start p-5 lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-base font-semibold text-ink">ডোনার স্ট্যাটাস</h2>
                @if ($donor)
                    <span class="badge {{ $donor->status->badgeClasses() }}">{{ $donor->status->label() }}</span>
                @else
                    <span class="badge bg-raised text-ink-soft">ডোনার নন</span>
                @endif
            </div>

            @if (! $donor)
                <div class="mt-4 rounded-xl bg-raised/60 p-5 text-center">
                    <svg class="mx-auto h-6 w-6 text-brand-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path d="M12 3.5c-2.4 3-6 7.1-6 10.25a6 6 0 0 0 12 0c0-3.15-3.6-7.25-6-10.25Z" stroke-linejoin="round"/>
                    </svg>
                    <h3 class="mt-2 font-medium text-ink">আপনি এখনো ডোনার নন</h3>
                    <p class="mt-1 text-sm text-ink-soft">আবেদন করুন — যাচাইয়ের পর আপনার প্রোফাইল সবার জন্য উন্মুক্ত হবে।</p>
                    <a href="{{ route('donor.apply') }}" class="btn-primary mt-4">ডোনার আবেদন করুন</a>
                </div>
            @elseif ($donor->status === \App\Enums\DonorStatus::Pending)
                <div class="mt-4 rounded-xl border border-[#e5d3a8] bg-[#f8efd9] p-4 text-sm text-[#6b4e16]">
                    <p class="font-semibold">আপনার আবেদন যাচাই করা হচ্ছে।</p>
                    <p class="mt-1">আমাদের টিম আপনার নম্বরে কল করে যাচাই করবে। সাধারণত ২৪–৪৮ ঘণ্টা লাগে।</p>
                </div>
            @elseif ($donor->status === \App\Enums\DonorStatus::Rejected)
                <div class="mt-4 rounded-xl border border-brand-200 bg-brand-50 p-4 text-sm text-brand-800">
                    <p class="font-semibold">আবেদন বাতিল হয়েছে</p>
                    <p class="mt-1">কারণ: {{ $donor->rejection_reason ?: 'উল্লেখ নেই' }}</p>
                    <a href="{{ route('donor.apply') }}" class="btn-secondary mt-3">তথ্য ঠিক করে আবার আবেদন করুন</a>
                </div>
            @elseif ($donor->status === \App\Enums\DonorStatus::Approved)
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-raised/60 p-4">
                        <p class="text-xs text-ink-muted">রক্তের গ্রুপ</p>
                        <p class="mt-1 text-2xl font-semibold text-brand-700">{{ $donor->blood_group }}</p>
                    </div>
                    <div class="rounded-xl bg-raised/60 p-4">
                        <p class="text-xs text-ink-muted">উপলব্ধতা</p>
                        <p class="mt-1 text-lg font-semibold {{ $donor->is_available ? 'text-[#15803d]' : 'text-brand-700' }}">
                            {{ $donor->is_available ? 'রক্ত দিতে পারবেন' : 'অনুপলব্ধ' }}
                        </p>
                        @if (! $donor->is_available && $donor->available_from)
                            <p class="text-xs text-ink-muted">আবার সম্ভব: {{ $donor->available_from->format('d M Y') }}</p>
                        @endif
                    </div>
                    <div class="rounded-xl bg-raised/60 p-4">
                        <p class="text-xs text-ink-muted">মোট রক্তদান</p>
                        <p class="mt-1 text-2xl font-semibold text-ink">{{ $donor->total_donations }}</p>
                    </div>
                    <div class="rounded-xl bg-raised/60 p-4">
                        <p class="text-xs text-ink-muted">রেটিং</p>
                        <p class="mt-1 text-2xl font-semibold text-ink">{{ number_format((float) $donor->rating_avg, 1) }} <span class="text-sm font-medium text-ink-muted">/ ৫</span></p>
                        <p class="text-xs text-ink-muted">{{ $donor->rating_count }} টি রিভিউ</p>
                    </div>
                </div>
            @else
                <div class="mt-4 rounded-xl border border-line bg-raised/60 p-4 text-sm text-ink-soft">
                    আপনার ডোনার অ্যাকাউন্ট বর্তমানে স্থগিত রয়েছে। বিস্তারিত জানতে সাপোর্টে যোগাযোগ করুন।
                </div>
            @endif
        </div>

        {{-- Reveal limit --}}
        <div class="space-y-4">
            <div class="card p-5">
                <h2 class="text-base font-semibold text-ink">নম্বর দেখার সীমা</h2>
                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-semibold text-ink">{{ $revealedToday }}<span class="text-base font-medium text-ink-muted"> / {{ $revealLimit === 0 ? 'আনলিমিটেড' : $revealLimit }}</span></p>
                    <span class="text-xs text-ink-muted">আজ</span>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-raised">
                    <div class="h-full rounded-full bg-brand-600" style="width: {{ $revealLimit > 0 ? min(100, round($revealedToday / $revealLimit * 100)) : 100 }}%"></div>
                </div>
                <p class="mt-2 text-xs text-ink-muted">
                    @if ($revealLimit === 0)
                        আজ আপনি আনলিমিটেড ডোনারের নম্বর দেখতে পারবেন।
                    @else
                        প্রতিদিন সর্বোচ্চ {{ $revealLimit }} জন ডোনারের নম্বর দেখতে পারবেন। সীমা শেষ হলে পরদিন আবার চেষ্টা করুন।
                    @endif
                </p>
                <a href="{{ route('donors.index') }}" class="btn-secondary mt-3 w-full">ডোনার খুঁজুন</a>
            </div>

            <div class="card p-5">
                <h2 class="text-base font-semibold text-ink">অ্যাকাউন্ট নিরাপত্তা</h2>
                <ul class="mt-3 space-y-2 text-sm text-ink-soft">
                    <li class="flex items-center justify-between">
                        <span>মোবাইল ভেরিফাইড</span>
                        @if ($user->phone_verified_at)
                            <span class="text-[#15803d]">✓</span>
                        @elseif (\App\Models\Setting::phoneVerificationRequired())
                            <a href="{{ route('verification.notice') }}" class="text-xs font-semibold text-[#8a5a12] hover:underline">যাচাই করুন</a>
                        @else
                            <span class="text-xs text-ink-muted">ঐচ্ছিক</span>
                        @endif
                    </li>
                    <li class="flex items-center justify-between">
                        <span>ইমেইল ভেরিফাইড</span>
                        @if ($user->email_verified_at)
                            <span class="text-[#15803d]">✓</span>
                        @elseif (\App\Models\Setting::emailVerificationRequired())
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button class="text-xs font-semibold text-[#8a5a12] hover:underline">লিংক পাঠান</button>
                            </form>
                        @else
                            <span class="text-xs text-ink-muted">ঐচ্ছিক</span>
                        @endif
                    </li>
                </ul>
            </div>

            @if (\App\Models\Setting::get('donation.enabled', true))
                <a href="{{ route('donate.index') }}" class="group block rounded-[14px] border border-line bg-raised/60 p-4 transition-colors hover:border-[#d5cbb9]">
                    <div class="flex items-start gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-line bg-surface text-ink-soft">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M10 16.25S3.75 12.5 3.75 8.25A3.25 3.25 0 0 1 10 6.4a3.25 3.25 0 0 1 6.25 1.85c0 4.25-6.25 8-6.25 8Z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-ink">সাইটটি ফ্রি রাখতে সাহায্য করুন</p>
                            <p class="mt-0.5 text-xs leading-relaxed text-ink-soft">ছোট একটি সহযোগিতা সার্ভার ও ডোমেইন খরচে কাজে লাগে।</p>
                            <span class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-brand-700 group-hover:underline">সহযোগিতা করুন <span aria-hidden="true">→</span></span>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </div>

    @if ($donor?->isApproved())
        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            {{-- Add donation --}}
            <div class="card p-5">
                <h2 class="text-base font-semibold text-ink">রক্তদানের তথ্য যোগ করুন</h2>
                <p class="mt-1 text-xs text-ink-muted">রক্তদানের পর তারিখ যোগ করলে ৪ মাস আপনি “অনুপলব্ধ” হিসেবে দেখাবেন।</p>

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
                <h2 class="text-base font-semibold text-ink">রক্তদানের ইতিহাস</h2>
                <ol class="mt-4 space-y-3">
                    @forelse ($donor->donations as $donation)
                        <li class="flex items-center justify-between gap-3 rounded-lg bg-raised/60 px-3 py-2">
                            <div>
                                <p class="text-sm font-semibold text-ink">{{ $donation->donated_at->format('d M Y') }}</p>
                                <p class="text-xs text-ink-muted">{{ $donation->hospital ?: 'হাসপাতাল উল্লেখ নেই' }}</p>
                            </div>
                            <span class="badge bg-[#edf5ee] text-[#15803d]">সম্পন্ন</span>
                        </li>
                    @empty
                        <li class="text-sm text-ink-muted">এখনো কোনো রক্তদানের তথ্য নেই।</li>
                    @endforelse
                </ol>
            </div>
        </div>
    @endif

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="text-base font-semibold text-ink">আপনার রিভিউ</h2>
            <div class="mt-3 space-y-3">
                @forelse ($ratings as $rating)
                    <div class="rounded-lg bg-raised/60 p-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-ink">{{ $rating->user?->name ?? 'ইউজার' }}</p>
                            <span class="text-xs text-brand-700">{{ str_repeat('★', $rating->rating) }}</span>
                        </div>
                        @if ($rating->comment)
                            <p class="mt-1 text-sm text-ink-soft">{{ $rating->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-ink-muted">এখনো কোনো রিভিউ আসেনি।</p>
                @endforelse
            </div>
        </div>

        <div class="card p-5">
            <h2 class="text-base font-semibold text-ink">বার্তা</h2>
            <div class="mt-3 space-y-3">
                @forelse ($messages as $message)
                    <div class="rounded-lg border border-line p-3">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold text-ink">{{ $message->subject }}</p>
                            <span class="text-[11px] text-ink-muted">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ \Illuminate\Support\Str::limit($message->body, 160) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-ink-muted">কোনো বার্তা নেই।</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
