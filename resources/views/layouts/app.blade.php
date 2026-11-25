<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-pt-20" style="--color-brand-600: {{ \App\Models\Setting::get('appearance.primary_color', '#a51c1c') }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', \App\Models\Setting::get('seo.title', 'One Blood Bank — বাংলাদেশের ভেরিফাইড ব্লাড ডোনার ডাটাবেজ'))</title>
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::get('seo.description'))">
    <meta name="keywords" content="@yield('meta_keywords', \App\Models\Setting::get('seo.keywords'))">
    <meta name="theme-color" content="#a51c1c">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ \App\Models\Setting::get('site.name', 'One Blood Bank') }}">
    <meta property="og:title" content="@yield('title', \App\Models\Setting::get('seo.title'))">
    <meta property="og:description" content="@yield('meta_description', \App\Models\Setting::get('seo.description'))">
    <meta property="og:url" content="{{ url()->current() }}">
    @if (\App\Models\Setting::get('seo.og_image'))
        <meta property="og:image" content="{{ asset('storage/'.\App\Models\Setting::get('seo.og_image')) }}">
    @endif

    @php
        $faviconPath = \App\Models\Setting::get('appearance.favicon_path');
    @endphp
    <link rel="icon" href="{{ $faviconPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($faviconPath) ? asset('storage/'.$faviconPath) : asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600&family=Tiro+Bangla:ital@0;1&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-paper text-ink">
    <x-ad placement="header" class="mx-auto mt-3 max-w-7xl px-4" />

    <header class="sticky top-0 z-40 border-b border-line bg-paper/95 backdrop-blur-sm">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4 sm:px-6 lg:px-8">
            <x-logo />

            <nav class="ml-8 hidden items-center gap-0.5 lg:flex">
                <a href="{{ route('donors.index') }}" class="nav-link {{ request()->routeIs('donors.*') ? 'nav-link-active' : '' }}">ডোনার খুঁজুন</a>
                <a href="{{ route('requests.index') }}" class="nav-link {{ request()->routeIs('requests.*') ? 'nav-link-active' : '' }}">রক্তের অনুরোধ</a>
                <a href="{{ route('donor.apply') }}" class="nav-link {{ request()->routeIs('donor.apply') ? 'nav-link-active' : '' }}">ডোনার হোন</a>
                @if (\App\Models\Setting::get('others.blog_enabled', true))
                    <a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog.*') ? 'nav-link-active' : '' }}">ব্লগ</a>
                @endif
                <a href="{{ route('pages.show', 'about') }}" class="nav-link">আমাদের সম্পর্কে</a>
            </nav>

            <div class="ml-auto flex items-center gap-1.5">
                @auth
                    @if (auth()->user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="hidden btn-secondary btn-sm sm:inline-flex">অ্যাডমিন</a>
                    @endif

                    <a href="{{ route('dashboard') }}" class="btn-secondary btn-sm hidden sm:inline-flex">ড্যাশবোর্ড</a>

                    <details class="relative">
                        <summary class="flex cursor-pointer list-none items-center gap-2 rounded-lg p-1 pr-2 transition-colors hover:bg-raised">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-md object-cover">
                            <span class="hidden text-sm font-medium text-ink sm:block">{{ auth()->user()->username }}</span>
                            <svg class="hidden h-4 w-4 text-ink-muted sm:block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.2 7.2a1 1 0 0 1 1.4 0L10 10.6l3.4-3.4a1 1 0 1 1 1.4 1.4l-4.1 4.2a1 1 0 0 1-1.4 0L5.2 8.6a1 1 0 0 1 0-1.4Z" clip-rule="evenodd"/></svg>
                        </summary>
                        <div class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-line bg-surface py-1 shadow-[0_8px_24px_rgba(31,27,22,0.08)]">
                            <div class="border-b border-line px-4 py-2.5">
                                <p class="truncate text-sm font-medium text-ink">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-ink-muted">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-ink-soft transition-colors hover:bg-raised hover:text-ink">ড্যাশবোর্ড</a>
                            <a href="{{ route('donor.apply') }}" class="block px-4 py-2 text-sm text-ink-soft transition-colors hover:bg-raised hover:text-ink">ডোনার প্রোফাইল</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-ink-soft transition-colors hover:bg-raised hover:text-ink">প্রোফাইল সেটিংস</a>
                            @if (auth()->user()->isApprovedDonor())
                                <a href="{{ route('donor.card') }}" class="block px-4 py-2 text-sm text-ink-soft transition-colors hover:bg-raised hover:text-ink">ডোনার কার্ড</a>
                            @endif
                            @if (auth()->user()->isStaff())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-ink-soft transition-colors hover:bg-raised hover:text-ink">অ্যাডমিন প্যানেল</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-line">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm font-medium text-brand-700 transition-colors hover:bg-brand-50">লগআউট</button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost btn-sm">লগইন</a>
                    <a href="{{ route('register') }}" class="btn-primary btn-sm">রেজিস্টার</a>
                @endauth

                <button type="button" data-menu-toggle="mobile-menu" aria-expanded="false" aria-label="মেনু"
                        class="grid h-9 w-9 place-items-center rounded-lg border border-line text-ink-soft transition-colors hover:bg-raised lg:hidden">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 5.75A.75.75 0 0 1 3.75 5h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 5.75Zm0 4.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 10Zm0 4.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden border-t border-line bg-paper px-4 py-3 lg:hidden">
            <nav class="grid gap-1">
                <a href="{{ route('donors.index') }}" class="nav-link">ডোনার খুঁজুন</a>
                <a href="{{ route('requests.index') }}" class="nav-link">রক্তের অনুরোধ</a>
                <a href="{{ route('donor.apply') }}" class="nav-link">ডোনার হোন</a>
                @if (\App\Models\Setting::get('others.blog_enabled', true))
                    <a href="{{ route('blog.index') }}" class="nav-link">ব্লগ</a>
                @endif
                @if (\App\Models\Setting::get('donation.enabled', true))
                    <a href="{{ route('donate.index') }}" class="nav-link">সাইটে সহযোগিতা</a>
                @endif
                <a href="{{ route('pages.show', 'about') }}" class="nav-link">আমাদের সম্পর্কে</a>
                <a href="{{ route('pages.show', 'contact') }}" class="nav-link">যোগাযোগ</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <x-alerts />

        @if (auth()->check() && auth()->user()->needsEmailVerification())
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-[#e5d3a8] bg-[#f8efd9] px-4 py-3 text-sm text-[#6b4e16]">
                <span>আপনার ইমেইল এখনো ভেরিফাই করা হয়নি। ইমেইলে পাঠানো লিংকে ক্লিক করুন।</span>
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="btn-secondary btn-sm">আবার পাঠান</button>
                </form>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-20 border-t border-line bg-surface">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div class="lg:col-span-1">
                <x-logo />
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-ink-soft">
                    বাংলাদেশের জেলা, উপজেলা ও ইউনিয়ন ভিত্তিক ভেরিফাইড ব্লাড ডোনার নেটওয়ার্ক। ফ্রি, দ্রুত ও নিরাপদ।
                </p>
                <div class="mt-4 space-y-1 text-sm text-ink-muted">
                    <p>ইমেইল: {{ \App\Models\Setting::get('site.contact_email') }}</p>
                    <p>ফোন: {{ \App\Models\Setting::get('site.contact_phone') }}</p>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium text-ink">দ্রুত লিংক</h3>
                <ul class="mt-4 space-y-2.5 text-sm text-ink-soft">
                    <li><a class="transition-colors hover:text-brand-700" href="{{ route('donors.index') }}">ডোনার খুঁজুন</a></li>
                    <li><a class="transition-colors hover:text-brand-700" href="{{ route('donor.apply') }}">ডোনার হোন</a></li>
                    <li><a class="transition-colors hover:text-brand-700" href="{{ route('requests.index') }}">রক্তের অনুরোধ</a></li>
                    @if (\App\Models\Setting::get('others.blog_enabled', true))
                        <li><a class="transition-colors hover:text-brand-700" href="{{ route('blog.index') }}">ব্লগ</a></li>
                    @endif
                    @if (\App\Models\Setting::get('donation.enabled', true))
                        <li><a class="transition-colors hover:text-brand-700" href="{{ route('donate.index') }}">সাইটে সহযোগিতা</a></li>
                    @endif
                    <li><a class="transition-colors hover:text-brand-700" href="{{ route('pages.show', 'about') }}">আমাদের সম্পর্কে</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-medium text-ink">নীতিমালা</h3>
                <ul class="mt-4 space-y-2.5 text-sm text-ink-soft">
                    <li><a class="transition-colors hover:text-brand-700" href="{{ route('pages.show', 'privacy-policy') }}">গোপনীয়তা নীতি</a></li>
                    <li><a class="transition-colors hover:text-brand-700" href="{{ route('pages.show', 'terms') }}">শর্তাবলী</a></li>
                    <li><a class="transition-colors hover:text-brand-700" href="{{ route('pages.show', 'contact') }}">যোগাযোগ</a></li>
                    <li><a class="transition-colors hover:text-brand-700" href="{{ route('register') }}">অ্যাকাউন্ট খুলুন</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-medium text-ink">জনপ্রিয় জেলা</h3>
                <ul class="mt-4 space-y-2.5 text-sm text-ink-soft">
                    @foreach ($footerDistricts as $district)
                        <li>
                            <a class="transition-colors hover:text-brand-700" href="{{ route('donors.index', ['district_id' => $district['id']]) }}">
                                @if (app()->getLocale() === 'en')
                                    {{ $district['name'] ?: $district['name_bn'] }} blood donors
                                @else
                                    {{ $district['name_bn'] }}-এ ব্লাড ডোনার
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="border-t border-line px-4 py-6 text-center text-xs text-ink-muted">
            @if (\App\Models\Setting::get('others.footer_text'))
                <p class="mb-1">{{ \App\Models\Setting::get('others.footer_text') }}</p>
            @endif
            <p>© {{ now()->year }} {{ \App\Models\Setting::get('site.name', 'One Blood Bank') }} — সর্বস্বত্ব সংরক্ষিত।</p>
            <p class="mt-1">
                ডেভেলপার ও রক্ষণাবেক্ষণ:
                <a href="https://github.com/johirxofficial" target="_blank" rel="noopener" class="font-medium text-ink-soft transition-colors hover:text-brand-700">johirxofficial</a>
                · ওপেন সোর্স কোড:
                <a href="https://github.com/johirxofficial/one-blood-bank" target="_blank" rel="noopener" class="font-medium text-ink-soft transition-colors hover:text-brand-700">GitHub</a>
            </p>
        </div>
    </footer>

    @auth
        @if (! \App\Models\Setting::phoneVerificationRequired() || auth()->user()->phone_verified_at)
            <div id="phone-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-ink/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-md rounded-[14px] border border-line bg-surface p-6 shadow-[0_8px_24px_rgba(31,27,22,0.12)]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-medium text-brand-700">ডোনারের নম্বর</p>
                            <h3 class="mt-1 text-xl font-medium text-ink" data-phone-name>ডোনার</h3>
                        </div>
                        <button type="button" data-phone-close class="grid h-8 w-8 place-items-center rounded-lg text-ink-muted transition-colors hover:bg-raised">✕</button>
                    </div>

                    <div class="mt-4 space-y-2" data-phone-list>
                        <div class="rounded-xl bg-raised px-4 py-3 text-center">
                            <p class="text-[11px] text-ink-muted" data-phone-label>প্রধান নম্বর</p>
                            <p class="tnum mt-0.5 text-xl font-medium tracking-wider text-ink" data-phone-number>—</p>
                        </div>
                    </div>
                    <p class="mt-2 text-center text-xs text-ink-muted" data-phone-note></p>

                    <div class="mt-5 grid grid-cols-2 gap-2">
                        <a href="#" data-phone-call class="btn-primary">কল করুন</a>
                        <button type="button" data-phone-copy class="btn-secondary">সব নম্বর কপি করুন</button>
                    </div>

                    <p class="mt-4 text-center text-[11px] leading-relaxed text-ink-muted">
                        শুধুমাত্র রক্তের প্রয়োজনে যোগাযোগ করুন। অপব্যবহার করলে অ্যাকাউন্ট বন্ধ হবে।
                    </p>
                </div>
            </div>
        @endif
    @endauth

    @if (\App\Models\Setting::get('ads.enabled') && \App\Models\Setting::get('ads.popunder_enabled') && \App\Models\Setting::get('ads.popunder_code'))
        {!! \App\Models\Setting::get('ads.popunder_code') !!}
    @endif

    @if (\App\Models\Setting::get('others.analytics_code'))
        {!! \App\Models\Setting::get('others.analytics_code') !!}
    @endif

    @stack('scripts')
</body>
</html>
