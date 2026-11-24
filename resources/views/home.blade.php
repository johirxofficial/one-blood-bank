@extends('layouts.app')

@section('title', \App\Models\Setting::get('seo.title'))
@section('meta_description', \App\Models\Setting::get('seo.description'))

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-600 to-rose-500 text-white shadow-xl shadow-brand-900/20">
        <div class="hero-pattern absolute inset-0" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-16 -top-24 h-72 w-72 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-black/10 blur-2xl" aria-hidden="true"></div>

        <div class="relative grid items-center gap-10 px-5 py-10 sm:px-8 sm:py-14 lg:grid-cols-[1.05fr_.95fr] lg:px-12 lg:py-16">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold ring-1 ring-inset ring-white/25">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-300"></span>
                    বাংলাদেশের ভেরিফাইড ডোনার নেটওয়ার্ক
                </span>

                <h1 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl lg:text-[2.9rem]">
                    রক্ত লাগবে?<br class="hidden sm:block">
                    কাছের ভেরিফাইড ডোনার খুঁজুন।
                </h1>

                <p class="mt-4 max-w-xl text-sm leading-relaxed text-white/85 sm:text-base">
                    ২ মিনিটে, সম্পূর্ণ ফ্রি। জেলা, উপজেলা ও ইউনিয়ন ভিত্তিক ডোনার খুঁজে সরাসরি কল করুন।
                    প্রতিটি নম্বর যাচাই করা — ভুয়া নম্বর বা দালালের ঝামেলা নেই।
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="#search" class="btn bg-white px-6 py-3 text-brand-700 shadow-lg hover:bg-brand-50">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.45 4.39l3.08 3.08a1 1 0 0 1-1.42 1.42l-3.08-3.08A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
                        এখনই ডোনার খুঁজুন
                    </a>
                    <a href="{{ route('donor.apply') }}" class="btn border border-white/40 bg-white/10 px-6 py-3 text-white backdrop-blur hover:bg-white/20">
                        🩸 আমি ডোনার হতে চাই
                    </a>
                </div>

                <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-xs font-medium text-white/80">
                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-300">✓</span> নম্বর ভেরিফাইড</span>
                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-300">✓</span> লগইন ছাড়া নম্বর দেখা যায় না</span>
                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-300">✓</span> সম্পূর্ণ ফ্রি</span>
                </div>
            </div>

            {{-- Search card --}}
            <div id="search" class="rounded-2xl bg-white p-5 text-slate-900 shadow-2xl shadow-black/20 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-base font-bold">ডোনার খুঁজুন</h2>
                    <span class="badge bg-brand-50 text-brand-700">লাইভ ডাটাবেজ</span>
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

                    <button type="submit" class="btn-primary w-full py-3">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.45 4.39l3.08 3.08a1 1 0 0 1-1.42 1.42l-3.08-3.08A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
                        ডোনার খুঁজুন
                    </button>
                </form>

                <p class="mt-3 text-center text-[11px] leading-relaxed text-slate-500">
                    🔒 ডোনারের নম্বর শুধু লগইন করা ও নম্বর-ভেরিফাইড ইউজাররা দেখতে পারেন।
                </p>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    @if (\App\Models\Setting::get('appearance.show_stats', true))
        <section class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            @foreach ([
                ['label' => 'ভেরিফাইড ডোনার', 'value' => number_format($stats['donors']), 'icon' => '🩸'],
                ['label' => 'জেলা কভার', 'value' => $stats['districts'], 'icon' => '📍'],
                ['label' => 'মোট রক্তদান', 'value' => number_format($stats['donations']), 'icon' => '❤️'],
                ['label' => 'রেজিস্টার্ড ইউজার', 'value' => number_format($stats['members']), 'icon' => '👥'],
            ] as $stat)
                <div class="card flex items-center gap-3 p-4">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-50 text-lg">{{ $stat['icon'] }}</span>
                    <div>
                        <p class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $stat['value'] }}</p>
                        <p class="text-xs text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    {{-- How it works --}}
    <section class="mt-14">
        <div class="text-center">
            <h2 class="section-title">যেভাবে কাজ করে</h2>
            <p class="section-sub">তিন ধাপে রক্তদাতার সাথে যুক্ত হোন</p>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-3">
            @foreach ([
                ['step' => '০১', 'title' => 'অ্যাকাউন্ট খুলুন', 'text' => 'নাম, ইউজারনেম, ইমেইল ও নম্বর দিয়ে ফ্রি অ্যাকাউন্ট খুলুন। নম্বরে OTP দিয়ে যাচাই করুন।', 'icon' => 'user'],
                ['step' => '০২', 'title' => 'ডোনার খুঁজুন', 'text' => 'ব্লাড গ্রুপ ও লোকেশন ফিল্টার দিয়ে কাছের ভেরিফাইড ডোনার খুঁজে নিন।', 'icon' => 'search'],
                ['step' => '০৩', 'title' => 'কল করুন', 'text' => 'নম্বর দেখে সরাসরি কল করুন, রক্তদানের সময় ও জায়গা নিশ্চিত করুন।', 'icon' => 'phone'],
            ] as $item)
                <div class="card p-6">
                    <div class="flex items-center justify-between">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-600/10 text-brand-700">
                            @if ($item['icon'] === 'user')
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.5 14.5A4.5 4.5 0 0 1 8 10h4a4.5 4.5 0 0 1 4.5 4.5v.5a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1v-.5Z"/></svg>
                            @elseif ($item['icon'] === 'search')
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.45 4.39l3.08 3.08a1 1 0 0 1-1.42 1.42l-3.08-3.08A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
                            @else
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3.5A1.5 1.5 0 0 1 3.5 2h2.6a1 1 0 0 1 .97.76l.86 3.45a1 1 0 0 1-.5 1.12l-1.6.9a11.3 11.3 0 0 0 5.94 5.94l.9-1.6a1 1 0 0 1 1.12-.5l3.45.86a1 1 0 0 1 .76.97v2.6A1.5 1.5 0 0 1 16.5 18C8.5 18 2 11.5 2 3.5Z"/></svg>
                            @endif
                        </span>
                        <span class="text-3xl font-extrabold text-slate-100">{{ $item['step'] }}</span>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $item['title'] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Donors --}}
    <section class="mt-14">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="section-title">{{ $canViewDonors ? 'সেরা রেটিং পাওয়া ডোনার' : 'আমাদের ডোনার কমিউনিটি' }}</h2>
                <p class="section-sub">
                    {{ $canViewDonors ? 'সপ্তাহের সেরা রেটিং পাওয়া ভেরিফাইড ডোনাররা' : 'ডোনারদের নাম, এলাকা ও নম্বর দেখতে লগইন করুন' }}
                </p>
            </div>
            @if ($canViewDonors)
                <a href="{{ route('donors.index') }}" class="btn-secondary">সব ডোনার দেখুন →</a>
            @endif
        </div>

        @if ($canViewDonors)
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($donors as $donor)
                    <x-donor-card :donor="$donor" />
                @endforeach
            </div>
        @else
            <div class="card mt-6 overflow-hidden">
                <div class="grid items-center gap-6 p-6 sm:p-8 lg:grid-cols-[1.2fr_.8fr]">
                    <div>
                        <span class="badge bg-amber-100 text-amber-800">🔒 ডেটা সুরক্ষিত</span>
                        <h3 class="mt-3 text-xl font-bold text-slate-900">ডোনার ডাটাবেজ দেখতে লগইন করুন</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">
                            ডোনারদের ফোন নম্বর পাবলিক নয়। শুধুমাত্র নম্বর-ভেরিফাইড অ্যাকাউন্টধারীরা ডোনার খুঁজতে পারেন —
                            এতে বট বা স্ক্র্যাপার দিয়ে নম্বর সংগ্রহ করা যায় না।
                        </p>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <a href="{{ route('register') }}" class="btn-primary">ফ্রি অ্যাকাউন্ট খুলুন</a>
                            <a href="{{ route('login') }}" class="btn-secondary">লগইন করুন</a>
                        </div>
                    </div>

                    <div class="relative rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="space-y-3">
                            @foreach ([1, 2, 3] as $row)
                                <div class="flex items-center gap-3 rounded-lg bg-white p-3 shadow-sm">
                                    <div class="h-10 w-10 rounded-lg bg-slate-200"></div>
                                    <div class="flex-1 space-y-1.5">
                                        <div class="h-2.5 w-24 rounded bg-slate-200"></div>
                                        <div class="h-2 w-32 rounded bg-slate-100"></div>
                                    </div>
                                    <span class="rounded-md bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-400">••••••</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="absolute inset-0 grid place-items-center rounded-xl bg-white/40 backdrop-blur-[2px]">
                            <span class="rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow">লগইন প্রয়োজন</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- Urgent requests --}}
    @if (\App\Models\Setting::get('appearance.show_urgent_requests', true) && $urgentRequests->isNotEmpty())
        <section class="mt-14">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="section-title flex items-center gap-2">
                        <span class="relative flex h-3 w-3"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-400 opacity-75"></span><span class="relative inline-flex h-3 w-3 rounded-full bg-brand-600"></span></span>
                        জরুরি রক্তের অনুরোধ
                    </h2>
                    <p class="section-sub">আজকের চলমান অনুরোধগুলো — সম্ভব হলে এগিয়ে আসুন</p>
                </div>
                <a href="{{ route('requests.index') }}" class="btn-secondary">সব অনুরোধ →</a>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach ($urgentRequests as $request)
                    <div class="card border-l-4 border-l-brand-600 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $request->patient_name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $request->hospital }} · {{ app()->getLocale() === 'en' ? ($request->upazila?->name ?: $request->district?->name) : ($request->upazila?->name_bn ?: $request->district?->name_bn) }}</p>
                            </div>
                            <x-blood-badge :group="$request->blood_group" size="sm" />
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                            <span class="badge bg-brand-50 text-brand-700">{{ $request->units }} ব্যাগ</span>
                            <span class="badge bg-slate-100 text-slate-600">{{ $request->needed_at->diffForHumans(now(), ['parts' => 1, 'short' => true]) }}</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($request->note ?: 'জরুরি রক্ত প্রয়োজন।', 90) }}</p>
                        @php
                            $canSeeRequestPhone = auth()->check() && (! \App\Models\Setting::phoneVerificationRequired() || auth()->user()->phone_verified_at);
                        @endphp
                        <p class="mt-3 text-sm font-semibold {{ $canSeeRequestPhone ? 'text-slate-900' : 'text-slate-400' }}">
                            ☎ {{ $canSeeRequestPhone ? $request->contact_phone : 'লগইন করে নম্বর দেখুন' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ID card --}}
    <section class="mt-14 overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <div class="grid items-center gap-8 p-6 sm:p-10 lg:grid-cols-2">
            <div>
                <span class="badge bg-brand-50 text-brand-700">ভেরিফাইড ডোনারদের জন্য</span>
                <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">ডিজিটাল ডোনার আইডি কার্ড</h2>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">
                    ভেরিফাইড ডোনার হলে পাবেন QR কোডসহ ডিজিটাল আইডি কার্ড — PDF আকারে ডাউনলোড করে প্রিন্ট করতে পারবেন।
                    QR স্ক্যান করলেই সরাসরি আপনার ডোনার প্রোফাইল খুলবে।
                </p>
                <ul class="mt-4 space-y-2 text-sm text-slate-600">
                    <li class="flex gap-2"><span class="text-emerald-500">✓</span> নাম, রক্তের গ্রুপ ও এলাকা</li>
                    <li class="flex gap-2"><span class="text-emerald-500">✓</span> স্ক্যানযোগ্য QR কোড</li>
                    <li class="flex gap-2"><span class="text-emerald-500">✓</span> A6 সাইজে PDF ডাউনলোড</li>
                </ul>
                <a href="{{ auth()->check() ? route('donor.apply') : route('register') }}" class="btn-primary mt-6">ডোনার হয়ে কার্ড নিন</a>
            </div>

            <div class="flex justify-center">
                <div class="w-full max-w-sm rotate-1 rounded-2xl bg-gradient-to-br from-brand-700 to-brand-500 p-1 shadow-2xl shadow-brand-900/25 transition hover:rotate-0">
                    <div class="rounded-xl bg-white p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-600 text-white">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25c-.32 0-.62.14-.82.39C9.4 5.1 5.25 10.4 5.25 14.25a6.75 6.75 0 0 0 13.5 0c0-3.85-4.15-9.15-5.93-11.61a1.04 1.04 0 0 0-.82-.39Z"/></svg>
                                </span>
                                <div class="leading-tight">
                                    <p class="text-[11px] font-extrabold text-slate-900">One Blood Bank</p>
                                    <p class="text-[8px] font-semibold text-brand-600">VERIFIED DONOR</p>
                                </div>
                            </div>
                            <span class="rounded bg-emerald-50 px-1.5 py-0.5 text-[8px] font-bold text-emerald-700">✓ VERIFIED</span>
                        </div>

                        <div class="mt-4 flex items-center gap-3">
                            <div class="h-14 w-14 rounded-lg bg-slate-200"></div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900">{{ app()->getLocale() === 'en' ? 'Rakib Hasan' : 'রাকিব হাসান' }}</p>
                                <p class="text-[10px] text-slate-500">@rakib_hasan</p>
                                <p class="mt-1 text-[10px] text-slate-500">{{ app()->getLocale() === 'en' ? 'Cumilla, Burichang' : 'কুমিল্লা, বুড়িচং' }}</p>
                            </div>
                            <span class="ml-auto grid h-10 w-10 place-items-center rounded-lg bg-brand-50 text-sm font-extrabold text-brand-700">O+</span>
                        </div>

                        <div class="mt-4 flex items-end justify-between gap-3 border-t border-dashed border-slate-200 pt-3">
                            <div class="text-[9px] leading-relaxed text-slate-500">
                                <p>মোবাইল: 01XXX-XXXXXX</p>
                                <p>রক্তদান: ৫ বার</p>
                                <p>সদস্য: ২০২৬</p>
                            </div>
                            <svg class="h-14 w-14 text-slate-900" viewBox="0 0 29 29" fill="currentColor" aria-hidden="true">
                                <path d="M0 0h7v7H0V0Zm1 1v5h5V1H1Zm1 1h3v3H2V2Z"/>
                                <path d="M9 0h1v2H9V0Zm2 0h3v1h-3V0Zm5 0h1v1h-1V0Zm2 0h2v1h-2V0Zm3 0h1v2h-1V0Zm2 0h6v7h-7V0Zm1 1v5h5V1h-5Zm1 1h3v3h-3V2ZM0 9h1v1H0V9Zm3 0h2v1H3V9Zm4 0h2v1H7V9Zm3 0h1v2h-1V9Zm2 0h1v1h-1V9Zm2 0h1v1h-1V9Zm3 0h1v1h-1V9Zm2 0h2v1h-2V9Zm3 0h1v1h-1V9Zm2 0h1v1h-1V9Zm2 0h1v1h-1V9ZM0 11h1v1H0v-1Zm2 0h2v1H2v-1Zm4 0h2v1H6v-1Zm4 0h3v1h-3v-1Zm5 0h1v2h-1v-2Zm2 0h4v1h-4v-1Zm6 0h1v1h-1v-1ZM0 13h2v1H0v-1Zm3 0h1v1H3v-1Zm3 0h1v1H6v-1Zm3 0h1v2H9v-2Zm3 0h2v1h-2v-1Zm4 0h2v1h-2v-1Zm3 0h1v1h-1v-1Zm2 0h1v1h-1v-1Zm3 0h1v1h-1v-1ZM0 15h1v2H0v-2Zm3 0h1v1H3v-1Zm2 0h2v1H5v-1Zm4 0h1v1H9v-1Zm3 0h2v1h-2v-1Zm4 0h1v1h-1v-1Zm2 0h1v2h-1v-2Zm2 0h1v1h-1v-1Zm2 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h1v1h-1v-1ZM0 17h2v1H0v-1Zm4 0h1v1H4v-1Zm3 0h2v1H7v-1Zm4 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h3v1h-3v-1Zm4 0h1v1h-1v-1ZM0 19h1v1H0v-1Zm2 0h1v1H2v-1Zm3 0h1v1H5v-1Zm3 0h2v1H8v-1Zm3 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h2v1h-2v-1Zm3 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h1v1h-1v-1ZM0 21h1v1H0v-1Zm2 0h2v1H2v-1Zm5 0h1v1H7v-1Zm2 0h2v1H9v-1Zm3 0h3v1h-3v-1Zm4 0h1v1h-1v-1Zm3 0h1v1h-1v-1Zm2 0h2v1h-2v-1Zm3 0h1v1h-1v-1Z" opacity=".85"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="mt-14 rounded-3xl bg-slate-900 px-6 py-10 text-center text-white sm:px-10">
        <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">এক ব্যাগ রক্ত বাঁচাতে পারে একটি জীবন</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-slate-300">
            আপনি সুস্থ থাকলে আজই ডোনার হয়ে যুক্ত হন। আপনার নম্বর যাচাই করা থাকবে, তাই কেউ ভুয়া কল দেবে না।
        </p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}" class="btn-primary px-6 py-3">ফ্রি অ্যাকাউন্ট খুলুন</a>
            <a href="{{ route('donor.apply') }}" class="btn border border-slate-600 bg-slate-800 px-6 py-3 text-white hover:bg-slate-700">ডোনার আবেদন করুন</a>
        </div>
    </section>
@endsection
