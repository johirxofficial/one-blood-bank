@extends('layouts.app')

@section('title', \App\Models\Setting::get('seo.title'))
@section('meta_description', \App\Models\Setting::get('seo.description'))

@section('content')
    {{-- Hero: a quiet page with one useful object, the donor register --}}
    <section class="grid items-center gap-10 py-8 lg:grid-cols-[1.02fr_.98fr] lg:gap-16 lg:py-14">
        <div>
            <p class="eyebrow">বাংলাদেশের ভেরিফাইড ডোনার নেটওয়ার্ক</p>

            <h1 class="display-title mt-5 text-[2.15rem] sm:text-5xl lg:text-[3.35rem]">
                রক্ত লাগবে?<br>
                কাছের ভেরিফাইড ডোনার খুঁজুন
            </h1>

            <p class="mt-5 max-w-lg text-[15px] leading-relaxed text-ink-soft sm:text-base">
                ২ মিনিটে, সম্পূর্ণ ফ্রি। জেলা, উপজেলা ও ইউনিয়ন ভিত্তিক ডোনার খুঁজে সরাসরি কল করুন।
                প্রতিটি নম্বর যাচাই করা — ভুয়া নম্বর বা দালালের ঝামেলা নেই।
            </p>

            <div class="mt-7 flex flex-wrap gap-3">
                <a href="#search" class="btn-primary h-12 px-6">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <circle cx="9" cy="9" r="5.25"/><path d="m13 13 3.5 3.5" stroke-linecap="round"/>
                    </svg>
                    ডোনার খুঁজুন
                </a>
                <a href="{{ route('donor.apply') }}" class="btn-secondary h-12 px-6">ডোনার হোন</a>
            </div>

            <ul class="mt-7 flex flex-wrap gap-x-6 gap-y-2 text-xs text-ink-muted">
                <li class="inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-brand-700" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 10.5 3.5 3.5L16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    নম্বর ভেরিফাইড
                </li>
                <li class="inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-brand-700" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 10.5 3.5 3.5L16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    লগইন ছাড়া নম্বর দেখা যায় না
                </li>
                <li class="inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-brand-700" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 10.5 3.5 3.5L16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    সম্পূর্ণ ফ্রি
                </li>
            </ul>
        </div>

        {{-- The one rich object: a real search form that looks like a register --}}
        <div id="search" class="rounded-[16px] border border-line bg-surface p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3 border-b border-line pb-4">
                <div>
                    <h2 class="font-display text-xl font-normal text-ink">ডোনার খুঁজুন</h2>
                    <p class="mt-0.5 text-xs text-ink-muted">ভেরিফাইড তালিকা · {{ number_format($stats['donors']) }} জন ডোনার</p>
                </div>
                <span class="mt-1 inline-flex items-center gap-1.5 text-[11px] text-ink-muted">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#16a34a]"></span>
                    সক্রিয়
                </span>
            </div>

            <form method="GET" action="{{ route('donors.index') }}" class="mt-4 space-y-3">
                <div>
                    <label for="home_blood_group" class="label">রক্তের গ্রুপ</label>
                    <select name="blood_group" id="home_blood_group" class="input">
                        <option value="">সব গ্রুপ</option>
                        @foreach (\App\Enums\BloodGroup::options() as $value => $label)
                            <option value="{{ $value }}" @selected(old('blood_group') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <x-location-selects :divisions="$divisions" :compact="false" />

                <button type="submit" class="btn-primary w-full">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <circle cx="9" cy="9" r="5.25"/><path d="m13 13 3.5 3.5" stroke-linecap="round"/>
                    </svg>
                    ডোনার খুঁজুন
                </button>
            </form>

            <p class="mt-3 flex items-start gap-1.5 text-[11px] leading-relaxed text-ink-muted">
                <svg class="mt-px h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <rect x="4.5" y="9" width="11" height="7.5" rx="1.5"/><path d="M7 9V6.75a3 3 0 0 1 6 0V9"/>
                </svg>
                ডোনারের নম্বর শুধু লগইন করা ও নম্বর-ভেরিফাইড ইউজাররা দেখতে পারেন।
            </p>
        </div>
    </section>

    {{-- Stats: one quiet row, no cards or icons --}}
    @if (\App\Models\Setting::get('appearance.show_stats', true))
        <section class="mt-10 border-y border-line">
            <div class="grid grid-cols-2 gap-px bg-line lg:grid-cols-4">
                @foreach ([
                    ['label' => 'ভেরিফাইড ডোনার', 'value' => number_format($stats['donors'])],
                    ['label' => 'জেলা কভার', 'value' => $stats['districts']],
                    ['label' => 'মোট রক্তদান', 'value' => number_format($stats['donations'])],
                    ['label' => 'রেজিস্টার্ড ইউজার', 'value' => number_format($stats['members'])],
                ] as $stat)
                    <div class="bg-paper px-6 py-6">
                        <p class="tnum text-2xl font-medium text-ink sm:text-[1.7rem]">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-xs text-ink-muted">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- How it works: numbered steps on a hairline, no cards --}}
    <section class="mt-16">
        <div class="max-w-2xl">
            <p class="eyebrow">যেভাবে কাজ করে</p>
            <h2 class="section-title mt-3">তিন ধাপে রক্তদাতার সাথে যুক্ত হোন</h2>
        </div>

        <div class="mt-8 grid gap-8 md:grid-cols-3">
            @foreach ([
                ['step' => '০১', 'title' => 'অ্যাকাউন্ট খুলুন', 'text' => 'নাম, ইউজারনেম, ইমেইল ও নম্বর দিয়ে ফ্রি অ্যাকাউন্ট খুলুন। নম্বরে OTP দিয়ে যাচাই করুন।', 'icon' => 'user'],
                ['step' => '০২', 'title' => 'ডোনার খুঁজুন', 'text' => 'ব্লাড গ্রুপ ও লোকেশন ফিল্টার দিয়ে কাছের ভেরিফাইড ডোনার খুঁজে নিন।', 'icon' => 'search'],
                ['step' => '০৩', 'title' => 'কল করুন', 'text' => 'নম্বর দেখে সরাসরি কল করুন, রক্তদানের সময় ও জায়গা নিশ্চিত করুন।', 'icon' => 'phone'],
            ] as $item)
                <div class="border-t border-line pt-5">
                    <div class="flex items-center justify-between">
                        <span class="tnum font-mono text-xs text-ink-muted">{{ $item['step'] }}</span>
                        @if ($item['icon'] === 'user')
                            <svg class="h-5 w-5 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <circle cx="12" cy="8" r="3.25"/><path d="M5.5 20a6.5 6.5 0 0 1 13 0" stroke-linecap="round"/>
                            </svg>
                        @elseif ($item['icon'] === 'search')
                            <svg class="h-5 w-5 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <circle cx="11" cy="11" r="6.25"/><path d="m16 16 4 4" stroke-linecap="round"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M5 4h3l1.5 4.5-2 1.25a12 12 0 0 0 5.75 5.75l1.25-2L19 15v3a2 2 0 0 1-2 2C10.3 20 4 13.7 4 6a2 2 0 0 1 1-2Z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @endif
                    </div>
                    <h3 class="mt-4 text-[15px] font-medium text-ink">{{ $item['title'] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-ink-soft">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Donors --}}
    <section class="mt-16">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div class="max-w-2xl">
                <p class="eyebrow">ডোনার কমিউনিটি</p>
                <h2 class="section-title mt-3">{{ $canViewDonors ? 'সেরা রেটিং পাওয়া ডোনার' : 'আমাদের ডোনার কমিউনিটি' }}</h2>
                <p class="section-sub">
                    {{ $canViewDonors ? 'সপ্তাহের সেরা রেটিং পাওয়া ভেরিফাইড ডোনাররা' : 'ডোনারদের নাম, এলাকা ও নম্বর দেখতে লগইন করুন' }}
                </p>
            </div>
            @if ($canViewDonors)
                <a href="{{ route('donors.index') }}" class="btn-secondary btn-sm">সব ডোনার দেখুন →</a>
            @endif
        </div>

        @if ($canViewDonors)
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($donors as $donor)
                    <x-donor-card :donor="$donor" />
                @endforeach
            </div>
        @else
            <div class="card mt-6 grid items-center gap-8 p-6 sm:p-8 lg:grid-cols-[1.05fr_.95fr]">
                <div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-line bg-raised text-ink-soft">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <rect x="4.5" y="9" width="11" height="7.5" rx="1.5"/><path d="M7 9V6.75a3 3 0 0 1 6 0V9"/>
                        </svg>
                    </span>
                    <h3 class="mt-4 text-xl font-medium text-ink">ডোনার ডাটাবেজ দেখতে লগইন করুন</h3>
                    <p class="mt-2 max-w-md text-sm leading-relaxed text-ink-soft">
                        ডোনারদের ফোন নম্বর পাবলিক নয়। শুধুমাত্র নম্বর-ভেরিফাইড অ্যাকাউন্টধারীরা ডোনার খুঁজতে পারেন —
                        এতে বট বা স্ক্র্যাপার দিয়ে নম্বর সংগ্রহ করা যায় না।
                    </p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('register') }}" class="btn-primary btn-sm">অ্যাকাউন্ট খুলুন</a>
                        <a href="{{ route('login') }}" class="btn-secondary btn-sm">লগইন করুন</a>
                    </div>
                </div>

                <div class="rounded-xl border border-line bg-raised/60 p-4">
                    <div class="flex items-center justify-between px-1 pb-3">
                        <p class="text-[11px] text-ink-muted">ডোনার তালিকার নমুনা</p>
                        <span class="text-[11px] text-ink-muted">নম্বর লুকানো</span>
                    </div>
                    <div class="space-y-2">
                        @foreach ([
                            ['name' => 'রাকিব হাসান', 'place' => 'কুমিল্লা, বুড়িচং', 'group' => 'O+'],
                            ['name' => 'সাদিয়া আক্তার', 'place' => 'ঢাকা, সাভার', 'group' => 'B+'],
                            ['name' => 'তানভীর আহমেদ', 'place' => 'চট্টগ্রাম, পটিয়া', 'group' => 'A+'],
                        ] as $row)
                            <div class="flex items-center gap-3 rounded-lg border border-line bg-surface p-3">
                                <span class="grid h-9 w-9 place-items-center rounded-md bg-raised text-xs font-medium text-ink-soft">
                                    {{ mb_substr($row['name'], 0, 1) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-ink">{{ $row['name'] }}</p>
                                    <p class="truncate text-xs text-ink-muted">{{ $row['place'] }}</p>
                                </div>
                                <span class="tnum text-xs font-medium text-brand-700">{{ $row['group'] }}</span>
                                <span class="tnum text-xs text-ink-muted">••••••</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- Urgent requests --}}
    @if (\App\Models\Setting::get('appearance.show_urgent_requests', true) && $urgentRequests->isNotEmpty())
        <section class="mt-16">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div class="max-w-2xl">
                    <p class="eyebrow">চলমান অনুরোধ</p>
                    <h2 class="section-title mt-3">জরুরি রক্তের অনুরোধ</h2>
                    <p class="section-sub">আজকের চলমান অনুরোধগুলো — সম্ভব হলে এগিয়ে আসুন</p>
                </div>
                <a href="{{ route('requests.index') }}" class="btn-secondary btn-sm">সব অনুরোধ →</a>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach ($urgentRequests as $request)
                    <div class="card border-l-2 border-l-brand-700 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-medium text-brand-700">জরুরি</p>
                                <p class="mt-1 text-sm font-medium text-ink">{{ $request->patient_name }}</p>
                                <p class="mt-0.5 text-xs text-ink-muted">{{ $request->hospital }} · {{ app()->getLocale() === 'en' ? ($request->upazila?->name ?: $request->district?->name) : ($request->upazila?->name_bn ?: $request->district?->name_bn) }}</p>
                            </div>
                            <x-blood-badge :group="$request->blood_group" size="sm" />
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-ink-muted">
                            <span class="tnum">{{ $request->units }} ব্যাগ</span>
                            <span>{{ $request->needed_at->diffForHumans(now(), ['parts' => 1, 'short' => true]) }}</span>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-ink-soft">{{ \Illuminate\Support\Str::limit($request->note ?: 'জরুরি রক্ত প্রয়োজন।', 90) }}</p>
                        @php
                            $canSeeRequestPhone = auth()->check() && (! \App\Models\Setting::phoneVerificationRequired() || auth()->user()->phone_verified_at);
                        @endphp
                        <p class="mt-3 flex items-center gap-1.5 text-sm {{ $canSeeRequestPhone ? 'font-medium text-ink' : 'text-ink-muted' }}">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M4 3.5h2.5l1.25 3.75-1.65 1a9.5 9.5 0 0 0 4.65 4.65l1-1.65L15.5 12.5V15a1.5 1.5 0 0 1-1.5 1.5C8.1 16.5 3.5 11.9 3.5 5A1.5 1.5 0 0 1 4 3.5Z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ $canSeeRequestPhone ? $request->contact_phone : 'লগইন করে নম্বর দেখুন' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ID card: the second rich moment, built as a real object --}}
    <section class="mt-16 overflow-hidden rounded-[16px] border border-line bg-raised">
        <div class="grid items-center gap-10 p-6 sm:p-10 lg:grid-cols-2 lg:gap-16">
            <div>
                <p class="eyebrow">ভেরিফাইড ডোনারদের জন্য</p>
                <h2 class="display-title mt-4 text-3xl sm:text-4xl">ডিজিটাল ডোনার আইডি কার্ড</h2>
                <p class="mt-4 max-w-lg text-sm leading-relaxed text-ink-soft">
                    ভেরিফাইড ডোনার হলে পাবেন QR কোডসহ ডিজিটাল আইডি কার্ড — PDF আকারে ডাউনলোড করে প্রিন্ট করতে পারবেন।
                    QR স্ক্যান করলেই সরাসরি আপনার ডোনার প্রোফাইল খুলবে।
                </p>
                <ul class="mt-5 space-y-2.5 text-sm text-ink-soft">
                    @foreach (['নাম, রক্তের গ্রুপ ও এলাকা', 'স্ক্যানযোগ্য QR কোড', 'A6 সাইজে PDF ডাউনলোড'] as $feature)
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-brand-700" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 10.5 3.5 3.5L16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ auth()->check() ? route('donor.apply') : route('register') }}" class="btn-primary mt-6">ডোনার হয়ে কার্ড নিন</a>
            </div>

            <div class="flex justify-center">
                <div class="w-full max-w-sm rounded-xl border border-line bg-surface p-5">
                    <div class="flex items-center justify-between border-b border-line pb-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-brand-700" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.25c-.32 0-.62.14-.82.39C9.4 5.1 5.25 10.4 5.25 14.25a6.75 6.75 0 0 0 13.5 0c0-3.85-4.15-9.15-5.93-11.61a1.04 1.04 0 0 0-.82-.39Z"/></svg>
                            <div class="leading-tight">
                                <p class="text-xs font-medium text-ink">One Blood Bank</p>
                                <p class="text-[10px] text-ink-muted">Verified donor</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-[#15803d]">
                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m4 10.5 3.5 3.5L16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            যাচাইকৃত
                        </span>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-lg bg-raised text-sm font-medium text-ink-soft">
                            {{ app()->getLocale() === 'en' ? 'RH' : 'রা' }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-ink">{{ app()->getLocale() === 'en' ? 'Rakib Hasan' : 'রাকিব হাসান' }}</p>
                            <p class="text-[11px] text-ink-muted">@rakib_hasan</p>
                            <p class="mt-0.5 text-[11px] text-ink-muted">{{ app()->getLocale() === 'en' ? 'Cumilla, Burichang' : 'কুমিল্লা, বুড়িচং' }}</p>
                        </div>
                        <span class="tnum ml-auto grid h-11 w-11 place-items-center rounded-lg border border-brand-200 bg-brand-50 text-base font-medium text-brand-700">O+</span>
                    </div>

                    <div class="mt-4 flex items-end justify-between gap-3 border-t border-dashed border-line pt-4">
                        <div class="space-y-0.5 text-[10px] leading-relaxed text-ink-muted">
                            <p class="tnum">মোবাইল: 01XXX-XXXXXX</p>
                            <p class="tnum">রক্তদান: ৫ বার</p>
                            <p class="tnum">সদস্য: ২০২৬</p>
                        </div>
                        <svg class="h-14 w-14 text-ink" viewBox="0 0 29 29" fill="currentColor" aria-hidden="true">
                            <path d="M0 0h7v7H0V0Zm1 1v5h5V1H1Zm1 1h3v3H2V2Z"/>
                            <path d="M9 0h1v2H9V0Zm2 0h3v1h-3V0Zm5 0h1v1h-1V0Zm2 0h2v1h-2V0Zm3 0h1v2h-1V0Zm2 0h6v7h-7V0Zm1 1v5h5V1h-5Zm1 1h3v3h-3V2ZM0 9h1v1H0V9Zm3 0h2v1H3V9Zm4 0h2v1H7V9Zm3 0h1v2h-1V9Zm2 0h1v1h-1V9Zm2 0h1v1h-1V9Zm3 0h1v1h-1V9Zm2 0h2v1h-2V9Zm3 0h1v1h-1V9Zm2 0h1v1h-1V9Zm2 0h1v1h-1V9ZM0 11h1v1H0v-1Zm2 0h2v1H2v-1Zm4 0h2v1H6v-1Zm4 0h3v1h-3v-1Zm5 0h1v2h-1v-2Zm2 0h4v1h-4v-1Zm6 0h1v1h-1v-1ZM0 13h2v1H0v-1Zm3 0h1v1H3v-1Zm3 0h1v1H6v-1Zm3 0h1v2H9v-2Zm3 0h2v1h-2v-1Zm4 0h2v1h-2v-1Zm3 0h1v1h-1v-1Zm2 0h1v1h-1v-1Zm3 0h1v1h-1v-1ZM0 15h1v2H0v-2Zm3 0h1v1H3v-1Zm2 0h2v1H5v-1Zm4 0h1v1H9v-1Zm3 0h2v1h-2v-1Zm4 0h1v1h-1v-1Zm2 0h1v2h-1v-2Zm2 0h1v1h-1v-1Zm2 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h1v1h-1v-1ZM0 17h2v1H0v-1Zm4 0h1v1H4v-1Zm3 0h2v1H7v-1Zm4 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h3v1h-3v-1Zm4 0h1v1h-1v-1ZM0 19h1v1H0v-1Zm2 0h1v1H2v-1Zm3 0h1v1H5v-1Zm3 0h2v1H8v-1Zm3 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h2v1h-2v-1Zm3 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h1v1h-1v-1ZM0 21h1v1H0v-1Zm2 0h2v1H2v-1Zm5 0h1v1H7v-1Zm2 0h2v1H9v-1Zm3 0h3v1h-3v-1Zm4 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h2v1h-2v-1Zm3 0h1v1h-1v-1Z" opacity=".85"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="mt-16 rounded-[16px] bg-ink px-6 py-12 text-center text-paper sm:px-10">
        <h2 class="font-display text-3xl font-normal leading-tight tracking-[-0.01em] sm:text-4xl">এক ব্যাগ রক্ত বাঁচাতে পারে একটি জীবন</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-paper/70">
            আপনি সুস্থ থাকলে আজই ডোনার হয়ে যুক্ত হন। আপনার নম্বর যাচাই করা থাকবে, তাই কেউ ভুয়া কল দেবে না।
        </p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}" class="btn h-11 bg-paper px-6 text-ink hover:bg-surface">অ্যাকাউন্ট খুলুন</a>
            <a href="{{ route('donor.apply') }}" class="btn h-11 border border-paper/25 px-6 text-paper hover:bg-surface/5">ডোনার আবেদন করুন</a>
        </div>
    </section>
@endsection
