@extends('layouts.admin')

@section('title', 'সাইট সেটিংস')
@section('heading', 'সেটিংস')

@php
    $val = fn (string $key, mixed $fallback = '') => old($key, $values[$key] ?? $fallback);
    $bool = fn (string $key) => (string) old($key, ($values[$key] ?? false) ? '1' : '0') === '1';
    $activeTab = old('_tab', request('tab', 'site'));
    $tabs = [
        'site' => 'সাইট তথ্য',
        'appearance' => 'ব্র্যান্ড ও লেআউট',
        'seo' => 'SEO',
        'security' => 'নিরাপত্তা ও ভেরিফিকেশন',
        'mail' => 'মেইল / SMTP',
        'sms' => 'SMS / OTP',
        'donors' => 'ডোনার নীতি',
        'donation' => 'সাইট ডোনেশন',
        'ads' => 'বিজ্ঞাপন',
        'others' => 'অন্যান্য',
    ];
@endphp

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">সাইট সেটিংস</h2>
            <p class="mt-1 text-sm text-ink-soft">যেকোনো দেশে সাইটটি চালানোর জন্য সব প্রয়োজনীয় কনফিগারেশন এক জায়গায়।</p>
        </div>
    </div>

    <div class="mt-5 overflow-x-auto">
        <div class="flex min-w-max gap-1 rounded-[14px] border border-line bg-surface p-1.5 shadow-sm" role="tablist">
            @foreach ($tabs as $key => $label)
                <button type="button" data-settings-tab="{{ $key }}"
                        class="settings-tab whitespace-nowrap rounded-xl px-3.5 py-2 text-sm font-semibold text-ink-soft transition hover:bg-raised {{ $activeTab === $key ? 'bg-brand-600 text-white hover:bg-brand-700' : '' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="mt-5">
        @csrf
        @method('PUT')
        <input type="hidden" name="_tab" id="settings-active-tab" value="{{ $activeTab }}">

        {{-- Site information --}}
        <section data-settings-panel="site" class="{{ $activeTab === 'site' ? '' : 'hidden' }}">
            <div class="grid gap-6 xl:grid-cols-2">
                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">সাইট তথ্য</h3>
                    <p class="text-xs text-ink-muted">নাম, ট্যাগলাইন, যোগাযোগের তথ্য ও ডিফল্ট দেশ/কারেন্সি।</p>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="label" for="site_name">সাইটের নাম <span class="text-brand-600">*</span></label>
                            <input type="text" name="site[name]" id="site_name" class="input" value="{{ $val('site.name') }}" required maxlength="80">
                        </div>
                        <div>
                            <label class="label" for="site_tagline">ট্যাগলাইন</label>
                            <input type="text" name="site[tagline]" id="site_tagline" class="input" value="{{ $val('site.tagline') }}" maxlength="160">
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label" for="site_contact_email">যোগাযোগ ইমেইল</label>
                                <input type="email" name="site[contact_email]" id="site_contact_email" class="input" value="{{ $val('site.contact_email') }}" maxlength="120">
                            </div>
                            <div>
                                <label class="label" for="site_contact_phone">যোগাযোগ ফোন</label>
                                <input type="text" name="site[contact_phone]" id="site_contact_phone" class="input" value="{{ $val('site.contact_phone') }}" maxlength="30">
                            </div>
                            <div>
                                <label class="label" for="site_country">ডিফল্ট দেশ</label>
                                <input type="text" name="site[country]" id="site_country" class="input" value="{{ $val('site.country', 'Bangladesh') }}" maxlength="80">
                            </div>
                            <div>
                                <label class="label" for="site_currency">কারেন্সি কোড</label>
                                <input type="text" name="site[currency]" id="site_currency" class="input" value="{{ $val('site.currency', 'BDT') }}" maxlength="10" placeholder="BDT / USD / EUR">
                            </div>
                        </div>
                        <div>
                            <label class="label" for="site_address">ঠিকানা</label>
                            <input type="text" name="site[address]" id="site_address" class="input" value="{{ $val('site.address') }}" maxlength="200">
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">হোমপেজ সেকশন</h3>
                    <p class="text-xs text-ink-muted">কোন সেকশনগুলো পাবলিক হোমপেজে দেখানো হবে।</p>

                    <div class="mt-4 space-y-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                            <input type="hidden" name="appearance[show_stats]" value="0">
                            <input type="checkbox" name="appearance[show_stats]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('appearance.show_stats'))>
                            <span>
                                <span class="block text-sm font-medium text-ink">পরিসংখ্যান সেকশন</span>
                                <span class="block text-xs text-ink-muted">ডোনার, ইউজার ও রক্তদানের সংখ্যা।</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                            <input type="hidden" name="appearance[show_urgent_requests]" value="0">
                            <input type="checkbox" name="appearance[show_urgent_requests]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('appearance.show_urgent_requests'))>
                            <span>
                                <span class="block text-sm font-medium text-ink">জরুরি অনুরোধ সেকশন</span>
                                <span class="block text-xs text-ink-muted">সাম্প্রতিক রক্তের অনুরোধগুলো।</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                            <input type="hidden" name="others[blog_enabled]" value="0">
                            <input type="checkbox" name="others[blog_enabled]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('others.blog_enabled'))>
                            <span>
                                <span class="block text-sm font-medium text-ink">ব্লগ সেকশন চালু</span>
                                <span class="block text-xs text-ink-muted">পাবলিক মেনুতে ব্লগ লিংক দেখানো হবে।</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </section>

        {{-- Appearance --}}
        <section data-settings-panel="appearance" class="{{ $activeTab === 'appearance' ? '' : 'hidden' }}">
            <div class="grid gap-6 xl:grid-cols-2">
                <div class="card p-5 sm:p-6 xl:col-span-2">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-ink">ব্র্যান্ড ও লেআউট</h3>
                            <p class="text-xs text-ink-muted">কালার, ফন্ট, হেডার, হোমপেজ, ফুটার ও কাস্টম CSS—সব এক জায়গায়।</p>
                        </div>
                        <span class="badge bg-brand-50 text-brand-700">লাইভ সাইটে প্রযোজ্য</span>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ([
                            'primary_color' => ['ব্র্যান্ড কালার', '#D32F2F'],
                            'paper_color' => ['পেজ ব্যাকগ্রাউন্ড', '#f7f4ee'],
                            'surface_color' => ['কার্ড / সারফেস', '#fdfbf7'],
                            'raised_color' => ['সহায়ক ব্যাকগ্রাউন্ড', '#efe9df'],
                            'line_color' => ['বর্ডার / হেয়ারলাইন', '#e4ddd0'],
                            'ink_color' => ['টেক্সট কালার', '#1f1b16'],
                        ] as $colorKey => [$colorLabel, $colorFallback])
                            <div>
                                <label class="label" for="appearance_{{ $colorKey }}">{{ $colorLabel }}</label>
                                <div class="flex gap-2">
                                    <input type="color" name="appearance[{{ $colorKey }}]" id="appearance_{{ $colorKey }}" class="h-11 w-14 cursor-pointer rounded-xl border border-line bg-surface p-1" value="{{ $val('appearance.'.$colorKey, $colorFallback) }}" @if ($colorKey === 'primary_color') required @endif data-color-picker>
                                    <input type="text" class="input font-mono" value="{{ $val('appearance.'.$colorKey, $colorFallback) }}" readonly data-color-value aria-label="{{ $colorLabel }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="help mt-3">ব্র্যান্ড কালার থেকে বাটন, লিংক, ব্যাজ ও হাইলাইটের সব শেড স্বয়ংক্রিয়ভাবে তৈরি হবে।</p>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">টাইপোগ্রাফি</h3>
                    <p class="text-xs text-ink-muted">বাংলা ও ইংরেজি—দুই ভাষার জন্য আলাদা ফন্ট বেছে নিন।</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label" for="appearance_font_sans">বডি ফন্ট</label>
                            <select name="appearance[font_sans]" id="appearance_font_sans" class="input">
                                <option value="hind" @selected($val('appearance.font_sans', 'hind') === 'hind')>Hind Siliguri</option>
                                <option value="inter" @selected($val('appearance.font_sans', 'hind') === 'inter')>Inter</option>
                                <option value="noto-sans" @selected($val('appearance.font_sans', 'hind') === 'noto-sans')>Noto Sans Bengali</option>
                                <option value="system" @selected($val('appearance.font_sans', 'hind') === 'system')>System Sans</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="appearance_font_display">হেডিং ফন্ট</label>
                            <select name="appearance[font_display]" id="appearance_font_display" class="input">
                                <option value="tiro" @selected($val('appearance.font_display', 'tiro') === 'tiro')>Tiro Bangla</option>
                                <option value="noto-serif" @selected($val('appearance.font_display', 'tiro') === 'noto-serif')>Noto Serif Bengali</option>
                                <option value="georgia" @selected($val('appearance.font_display', 'tiro') === 'georgia')>Georgia</option>
                                <option value="system" @selected($val('appearance.font_display', 'tiro') === 'system')>System Serif</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="appearance_font_size">বেস ফন্ট সাইজ</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="appearance[font_size]" id="appearance_font_size" class="input" min="14" max="18" value="{{ $val('appearance.font_size', 16) }}">
                                <span class="text-sm text-ink-muted">px</span>
                            </div>
                            <p class="help">১৪–১৮ px। পুরো সাইটের রিম-ভিত্তিক টেক্সট এতে স্কেল হবে।</p>
                        </div>
                        <div>
                            <label class="label" for="appearance_logo_height">লোগো সাইজ</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="appearance[logo_height]" id="appearance_logo_height" class="input" min="24" max="64" value="{{ $val('appearance.logo_height', 36) }}">
                                <span class="text-sm text-ink-muted">px</span>
                            </div>
                            <p class="help">হেডার ও ফুটারের লোগো/আইকনের উচ্চতা।</p>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">লেআউট ও স্টাইল</h3>
                    <p class="text-xs text-ink-muted">কন্টেইনার, কর্নার, স্পেসিং ও বাটন/কার্ডের চেহারা।</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label" for="appearance_container_width">কন্টেইনার প্রস্থ</label>
                            <select name="appearance[container_width]" id="appearance_container_width" class="input">
                                <option value="normal" @selected($val('appearance.container_width', 'normal') === 'normal')>স্বাভাবিক</option>
                                <option value="wide" @selected($val('appearance.container_width', 'normal') === 'wide')>প্রশস্ত</option>
                                <option value="full" @selected($val('appearance.container_width', 'normal') === 'full')>ফুল উইডথ</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="appearance_radius">কর্নার স্টাইল</label>
                            <select name="appearance[radius]" id="appearance_radius" class="input">
                                <option value="sharp" @selected($val('appearance.radius', 'soft') === 'sharp')>শার্প</option>
                                <option value="soft" @selected($val('appearance.radius', 'soft') === 'soft')>সফট</option>
                                <option value="round" @selected($val('appearance.radius', 'soft') === 'round')>রাউন্ড</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="appearance_section_spacing">সেকশন স্পেসিং</label>
                            <select name="appearance[section_spacing]" id="appearance_section_spacing" class="input">
                                <option value="compact" @selected($val('appearance.section_spacing', 'normal') === 'compact')>কমপ্যাক্ট</option>
                                <option value="normal" @selected($val('appearance.section_spacing', 'normal') === 'normal')>স্বাভাবিক</option>
                                <option value="relaxed" @selected($val('appearance.section_spacing', 'normal') === 'relaxed')>রিলাক্সড</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="appearance_button_style">প্রাইমারি বাটন</label>
                            <select name="appearance[button_style]" id="appearance_button_style" class="input">
                                <option value="ink" @selected($val('appearance.button_style', 'ink') === 'ink')>কালো (ডিফল্ট)</option>
                                <option value="brand" @selected($val('appearance.button_style', 'ink') === 'brand')>ব্র্যান্ড কালার</option>
                                <option value="outline" @selected($val('appearance.button_style', 'ink') === 'outline')>আউটলাইন</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="appearance_card_style">কার্ড স্টাইল</label>
                            <select name="appearance[card_style]" id="appearance_card_style" class="input">
                                <option value="flat" @selected($val('appearance.card_style', 'flat') === 'flat')>ফ্ল্যাট (ডিফল্ট)</option>
                                <option value="bordered" @selected($val('appearance.card_style', 'flat') === 'bordered')>শক্ত বর্ডার</option>
                                <option value="raised" @selected($val('appearance.card_style', 'flat') === 'raised')>হালকা শ্যাডো</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="appearance_layout">ওভারঅল লেআউট <span class="text-brand-600">*</span></label>
                            <select name="appearance[layout]" id="appearance_layout" class="input" required>
                                <option value="classic" @selected($val('appearance.layout', 'classic') === 'classic')>ক্লাসিক</option>
                                <option value="minimal" @selected($val('appearance.layout', 'classic') === 'minimal')>মিনিমাল</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">হেডার ও ঘোষণা</h3>
                    <p class="text-xs text-ink-muted">উপরের বার, নেভিগেশন ও ঘোষণার আচরণ।</p>
                    <div class="mt-4 space-y-2">
                        @foreach ([
                            'sticky_header' => ['স্টিকি হেডার', 'স্ক্রল করলেও হেডার উপরে আটকে থাকবে।'],
                            'show_header_tagline' => ['হেডারে ট্যাগলাইন', 'লোগোর নিচে সাইটের ট্যাগলাইন দেখাবে।'],
                            'show_header_cta' => ['হেডারে রেজিস্টার বাটন', 'গেস্ট ইউজারদের জন্য রেজিস্টার বাটন।'],
                            'show_contact_bar' => ['উপরের যোগাযোগ বার', 'হেডারের উপরে ঠিকানা, ইমেইল ও ফোন দেখাবে।'],
                            'announcement_enabled' => ['ঘোষণা বার চালু', 'হেডারের উপরে একটি ছোট ঘোষণা দেখাবে।'],
                        ] as $toggleKey => [$toggleLabel, $toggleHelp])
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                                <input type="hidden" name="appearance[{{ $toggleKey }}]" value="0">
                                <input type="checkbox" name="appearance[{{ $toggleKey }}]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('appearance.'.$toggleKey))>
                                <span>
                                    <span class="block text-sm font-medium text-ink">{{ $toggleLabel }}</span>
                                    <span class="block text-xs text-ink-muted">{{ $toggleHelp }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-4 grid gap-4">
                        <div>
                            <label class="label" for="appearance_announcement_text">ঘোষণার লেখা</label>
                            <input type="text" name="appearance[announcement_text]" id="appearance_announcement_text" class="input" value="{{ $val('appearance.announcement_text') }}" maxlength="200" placeholder="যেমন: আজ রক্তদান শিবির চলছে—সবার অংশগ্রহণ কাম্য।">
                        </div>
                        <div>
                            <label class="label" for="appearance_announcement_url">ঘোষণার লিংক</label>
                            <input type="text" name="appearance[announcement_url]" id="appearance_announcement_url" class="input" value="{{ $val('appearance.announcement_url') }}" maxlength="500" placeholder="/requests অথবা https://example.com">
                            <p class="help">খালি রাখলে শুধু লেখা দেখাবে, লিংক থাকলে ক্লিকযোগ্য হবে।</p>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6 xl:col-span-2">
                    <h3 class="text-base font-semibold text-ink">হোমপেজ সেকশন</h3>
                    <p class="text-xs text-ink-muted">হিরোর ধরন এবং কোন সেকশনগুলো পাবলিক হোমপেজে দেখানো হবে।</p>
                    <div class="mt-4 max-w-md">
                        <label class="label" for="appearance_hero_style">হিরো স্টাইল</label>
                        <select name="appearance[hero_style]" id="appearance_hero_style" class="input">
                            <option value="split" @selected($val('appearance.hero_style', 'split') === 'split')>স্প্লিট (টেক্সট + সার্চ)</option>
                            <option value="centered" @selected($val('appearance.hero_style', 'split') === 'centered')>সেন্টারড</option>
                            <option value="minimal" @selected($val('appearance.hero_style', 'split') === 'minimal')>মিনিমাল</option>
                        </select>
                    </div>
                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach ([
                            'show_hero_search' => ['হিরো সার্চ ফর্ম', 'হিরোতে ডোনার খোঁজার ফর্ম।'],
                            'show_hero_points' => ['হিরো ট্রাস্ট পয়েন্ট', 'ভেরিফাইড, ফ্রি ইত্যাদি ছোট পয়েন্ট।'],
                            'show_stats' => ['পরিসংখ্যান সেকশন', 'ডোনার, ইউজার ও রক্তদানের সংখ্যা।'],
                            'show_urgent_requests' => ['জরুরি অনুরোধ সেকশন', 'সাম্প্রতিক রক্তের অনুরোধগুলো।'],
                            'show_steps' => ['তিন ধাপের সেকশন', 'অ্যাকাউন্ট → খোঁজা → কল করার গাইড।'],
                            'show_community' => ['ডোনার কমিউনিটি', 'ডোনার কার্ড বা লগইন প্রম্পট।'],
                            'show_donor_card' => ['ডিজিটাল আইডি কার্ড', 'QR ডোনার কার্ডের প্রচারমূলক সেকশন।'],
                            'show_cta' => ['শেষের CTA', 'অ্যাকাউন্ট খোলা/আবেদনের বড় ব্যানার।'],
                        ] as $toggleKey => [$toggleLabel, $toggleHelp])
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                                <input type="hidden" name="appearance[{{ $toggleKey }}]" value="0">
                                <input type="checkbox" name="appearance[{{ $toggleKey }}]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('appearance.'.$toggleKey))>
                                <span>
                                    <span class="block text-sm font-medium text-ink">{{ $toggleLabel }}</span>
                                    <span class="block text-xs text-ink-muted">{{ $toggleHelp }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">ফুটার</h3>
                    <p class="text-xs text-ink-muted">ফুটারের লেআউট ও কোন তথ্য দেখাবে।</p>
                    <div class="mt-4">
                        <label class="label" for="appearance_footer_style">ফুটার স্টাইল</label>
                        <select name="appearance[footer_style]" id="appearance_footer_style" class="input">
                            <option value="columns" @selected($val('appearance.footer_style', 'columns') === 'columns')>কলাম (ডিফল্ট)</option>
                            <option value="minimal" @selected($val('appearance.footer_style', 'columns') === 'minimal')>মিনিমাল</option>
                        </select>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach ([
                            'show_footer_contact' => ['যোগাযোগ তথ্য', 'ফুটারে ইমেইল ও ফোন।'],
                            'show_footer_districts' => ['জনপ্রিয় জেলা', 'ফুটারে জেলা ভিত্তিক ডোনার লিংক।'],
                            'show_footer_credit' => ['ডেভেলপার ক্রেডিট', 'ফুটারের নিচে ডেভেলপার ও GitHub লিংক।'],
                        ] as $toggleKey => [$toggleLabel, $toggleHelp])
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                                <input type="hidden" name="appearance[{{ $toggleKey }}]" value="0">
                                <input type="checkbox" name="appearance[{{ $toggleKey }}]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('appearance.'.$toggleKey))>
                                <span>
                                    <span class="block text-sm font-medium text-ink">{{ $toggleLabel }}</span>
                                    <span class="block text-xs text-ink-muted">{{ $toggleHelp }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">লোগো ও ফেভিকন</h3>
                    <p class="text-xs text-ink-muted">লোগো না দিলে ডিফল্ট রক্তবিন্দু আইকন দেখানো হবে।</p>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="label" for="logo">সাইট লোগো</label>
                            @if ($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo))
                                <img src="{{ asset('storage/'.$logo) }}" alt="লোগো" class="mb-2 h-16 w-16 rounded-xl border border-line bg-raised/60 object-contain p-1">
                            @else
                                <div class="mb-2 grid h-16 w-16 place-items-center rounded-xl border border-line bg-raised/60 text-ink-muted">
                                    <svg class="h-6 w-6 text-brand-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 3.5c-2.4 3-6 7.1-6 10.25a6 6 0 0 0 12 0c0-3.15-3.6-7.25-6-10.25Z" stroke-linejoin="round"/></svg>
                                </div>
                            @endif
                            <input type="file" name="logo" id="logo" class="input file:mr-3 file:rounded-lg file:border-0 file:bg-raised file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-ink-soft" accept=".png,.jpg,.jpeg,.webp,.svg">
                            <p class="help">PNG, JPG, WebP, SVG — সর্বোচ্চ ২ MB।</p>
                        </div>
                        <div>
                            <label class="label" for="favicon">ফেভিকন</label>
                            @if ($favicon && \Illuminate\Support\Facades\Storage::disk('public')->exists($favicon))
                                <img src="{{ asset('storage/'.$favicon) }}" alt="ফেভিকন" class="mb-2 h-16 w-16 rounded-xl border border-line bg-raised/60 object-contain p-1">
                            @else
                                <div class="mb-2 grid h-16 w-16 place-items-center rounded-xl border border-line bg-raised/60 text-ink-muted">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.4 2.3 3.6 5.1 3.6 8.5S14.4 18.2 12 20.5C9.6 18.2 8.4 15.4 8.4 12S9.6 5.8 12 3.5Z"/></svg>
                                </div>
                            @endif
                            <input type="file" name="favicon" id="favicon" class="input file:mr-3 file:rounded-lg file:border-0 file:bg-raised file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-ink-soft" accept=".png,.jpg,.jpeg,.webp,.svg,.ico">
                            <p class="help">PNG, SVG বা ICO — সর্বোচ্চ ১ MB।</p>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6 xl:col-span-2">
                    <h3 class="text-base font-semibold text-ink">কাস্টম CSS</h3>
                    <p class="text-xs text-ink-muted">উন্নত ব্যবহারের জন্য। এখানে লেখা CSS পাবলিক সাইটের সব পেজে যুক্ত হবে।</p>
                    <textarea name="appearance[custom_css]" id="appearance_custom_css" rows="6" class="input mt-4 font-mono text-xs" maxlength="20000" spellcheck="false" placeholder=".site-announcement { letter-spacing: .02em; }">{{ $val('appearance.custom_css') }}</textarea>
                    <p class="help">সর্বোচ্চ ২০,০০০ অক্ষর। থিমের ডিফল্ট স্টাইল না ভাঙার জন্য নির্দিষ্ট সিলেক্টর ব্যবহার করুন।</p>
                </div>
            </div>
        </section>

        {{-- SEO --}}
        <section data-settings-panel="seo" class="{{ $activeTab === 'seo' ? '' : 'hidden' }}">
            <div class="card max-w-4xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-ink">সার্চ ইঞ্জিন অপটিমাইজেশন</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="label" for="seo_title">SEO টাইটেল</label>
                        <input type="text" name="seo[title]" id="seo_title" class="input" value="{{ $val('seo.title') }}" maxlength="180">
                    </div>
                    <div>
                        <label class="label" for="seo_description">SEO ডেসক্রিপশন</label>
                        <textarea name="seo[description]" id="seo_description" rows="3" class="input" maxlength="300">{{ $val('seo.description') }}</textarea>
                    </div>
                    <div>
                        <label class="label" for="seo_keywords">কীওয়ার্ড</label>
                        <input type="text" name="seo[keywords]" id="seo_keywords" class="input" value="{{ $val('seo.keywords') }}" maxlength="300" placeholder="কমা দিয়ে আলাদা করুন">
                    </div>
                </div>
            </div>
        </section>

        {{-- Security --}}
        <section data-settings-panel="security" class="{{ $activeTab === 'security' ? '' : 'hidden' }}">
            <div class="grid gap-6 xl:grid-cols-2">
                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">ভেরিফিকেশন</h3>
                    <p class="text-xs text-ink-muted">মেইল ও মোবাইল ভেরিফিকেশন বাধ্যতামূলক বা ঐচ্ছিক করুন।</p>
                    <div class="mt-4 space-y-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                            <input type="hidden" name="security[require_phone_verification]" value="0">
                            <input type="checkbox" name="security[require_phone_verification]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('security.require_phone_verification'))>
                            <span>
                                <span class="block text-sm font-medium text-ink">মোবাইল OTP ভেরিফিকেশন বাধ্যতামূলক</span>
                                <span class="block text-xs text-ink-muted">বন্ধ থাকলে ইউজার OTP ছাড়াই ডোনার তালিকা দেখতে পারবে।</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                            <input type="hidden" name="security[require_email_verification]" value="0">
                            <input type="checkbox" name="security[require_email_verification]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('security.require_email_verification'))>
                            <span>
                                <span class="block text-sm font-medium text-ink">ইমেইল ভেরিফিকেশন বাধ্যতামূলক</span>
                                <span class="block text-xs text-ink-muted">লিংক ক্লিক করে ইমেইল যাচাই করতে হবে।</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                            <input type="hidden" name="security[allow_registration]" value="0">
                            <input type="checkbox" name="security[allow_registration]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('security.allow_registration'))>
                            <span>
                                <span class="block text-sm font-medium text-ink">নতুন রেজিস্ট্রেশন চালু</span>
                                <span class="block text-xs text-ink-muted">বন্ধ করলে নতুন কেউ অ্যাকাউন্ট খুলতে পারবে না।</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">ডোনার তালিকা</h3>
                    <div class="mt-4 space-y-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                            <input type="hidden" name="donors[require_login]" value="0">
                            <input type="checkbox" name="donors[require_login]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('donors.require_login'))>
                            <span>
                                <span class="block text-sm font-medium text-ink">ডোনার দেখতে লগইন বাধ্যতামূলক</span>
                                <span class="block text-xs text-ink-muted">বন্ধ থাকলে গেস্টরাও তালিকা দেখতে পারবে (নম্বর লুকানো থাকবে)।</span>
                            </span>
                        </label>
                        <div class="rounded-xl border border-line px-4 py-3">
                            <label class="label" for="donors_daily_reveal_limit">দৈনিক নম্বর দেখার সীমা</label>
                            <input type="number" name="donors[daily_reveal_limit]" id="donors_daily_reveal_limit" class="input" min="0" max="100000" value="{{ $val('donors.daily_reveal_limit', 15) }}" required>
                            <p class="help"><strong>0</strong> দিলে আনলিমিটেড। উদাহরণ: 15 = প্রতিদিন সর্বোচ্চ ১৫টি নম্বর।</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Mail --}}
        <section data-settings-panel="mail" class="{{ $activeTab === 'mail' ? '' : 'hidden' }}">
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="card p-5 sm:p-6 xl:col-span-2">
                    <h3 class="text-base font-semibold text-ink">মেইল / SMTP কনফিগারেশন</h3>
                    <p class="mt-1 text-xs text-ink-muted">cPanel বা যেকোনো SMTP প্রোভাইডারের তথ্য দিন। পাসওয়ার্ড খালি রাখলে আগেরটাই থাকবে।</p>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label" for="mail_driver">মেইল ড্রাইভার</label>
                            <select name="mail[driver]" id="mail_driver" class="input">
                                <option value="log" @selected($val('mail.driver', 'log') === 'log')>Log (ডেভেলপমেন্ট)</option>
                                <option value="smtp" @selected($val('mail.driver', 'log') === 'smtp')>SMTP</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="mail_encryption">এনক্রিপশন</label>
                            <select name="mail[encryption]" id="mail_encryption" class="input">
                                <option value="tls" @selected($val('mail.encryption', 'tls') === 'tls')>TLS</option>
                                <option value="ssl" @selected($val('mail.encryption', 'tls') === 'ssl')>SSL</option>
                                <option value="starttls" @selected($val('mail.encryption', 'tls') === 'starttls')>STARTTLS</option>
                                <option value="" @selected($val('mail.encryption', 'tls') === '')>কোনোটি নয়</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="mail_host">SMTP হোস্ট</label>
                            <input type="text" name="mail[host]" id="mail_host" class="input" value="{{ $val('mail.host') }}" maxlength="180" placeholder="smtp.example.com">
                        </div>
                        <div>
                            <label class="label" for="mail_port">পোর্ট</label>
                            <input type="number" name="mail[port]" id="mail_port" class="input" value="{{ $val('mail.port', 587) }}" min="1" max="65535">
                        </div>
                        <div>
                            <label class="label" for="mail_username">ইউজারনেম</label>
                            <input type="text" name="mail[username]" id="mail_username" class="input" value="{{ $val('mail.username') }}" maxlength="180">
                        </div>
                        <div>
                            <label class="label" for="mail_password">পাসওয়ার্ড</label>
                            <input type="password" name="mail[password]" id="mail_password" class="input" placeholder="পরিবর্তন না করলে খালি রাখুন" autocomplete="new-password">
                        </div>
                        <div>
                            <label class="label" for="mail_from_address">প্রেরকের ইমেইল</label>
                            <input type="email" name="mail[from_address]" id="mail_from_address" class="input" value="{{ $val('mail.from_address') }}" maxlength="180" placeholder="no-reply@example.com">
                        </div>
                        <div>
                            <label class="label" for="mail_from_name">প্রেরকের নাম</label>
                            <input type="text" name="mail[from_name]" id="mail_from_name" class="input" value="{{ $val('mail.from_name') }}" maxlength="120">
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">মেইল নোটিফিকেশন</h3>
                    <div class="mt-4 space-y-2">
                        @foreach ([
                            'mail.welcome_enabled' => ['স্বাগতম মেইল', 'নতুন রেজিস্ট্রেশনে'],
                            'mail.application_enabled' => ['আবেদন জমার মেইল', 'ডোনার আবেদন গ্রহণে'],
                            'mail.status_enabled' => ['স্ট্যাটাস মেইল', 'অনুমোদন/বাতিলে'],
                            'mail.rating_enabled' => ['রিভিউ মেইল', 'নতুন রেটিং পেলে'],
                        ] as $key => $info)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                                <input type="hidden" name="{{ str_replace('.', '[', $key).']' }}" value="0">
                                <input type="checkbox" name="{{ str_replace('.', '[', $key).']' }}" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool($key))>
                                <span>
                                    <span class="block text-sm font-medium text-ink">{{ $info[0] }}</span>
                                    <span class="block text-xs text-ink-muted">{{ $info[1] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- SMS --}}
        <section data-settings-panel="sms" class="{{ $activeTab === 'sms' ? '' : 'hidden' }}">
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="card p-5 sm:p-6 xl:col-span-2">
                    <h3 class="text-base font-semibold text-ink">SMS / OTP গেটওয়ে</h3>
                    <p class="mt-1 text-xs text-ink-muted">যেকোনো দেশের SMS গেটওয়ের জন্য URL-এ <code>{phone}</code>, <code>{message}</code>, <code>{api_key}</code>, <code>{sender_id}</code> ব্যবহার করুন।</p>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label" for="sms_driver">ড্রাইভার</label>
                            <select name="sms[driver]" id="sms_driver" class="input">
                                <option value="log" @selected($val('sms.driver', 'log') === 'log')>Log (ডেভেলপমেন্ট)</option>
                                <option value="http" @selected($val('sms.driver', 'log') === 'http')>HTTP API</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="sms_sender_id">সেন্ডার আইডি</label>
                            <input type="text" name="sms[sender_id]" id="sms_sender_id" class="input" value="{{ $val('sms.sender_id') }}" maxlength="60" placeholder="BLOODBANK">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="sms_gateway_url">গেটওয়ে URL</label>
                            <input type="text" name="sms[gateway_url]" id="sms_gateway_url" class="input font-mono text-xs" value="{{ $val('sms.gateway_url') }}" maxlength="500" placeholder="https://api.provider.com/send?to={phone}&text={message}&api_key={api_key}">
                        </div>
                        <div>
                            <label class="label" for="sms_api_key">API Key</label>
                            <input type="password" name="sms[api_key]" id="sms_api_key" class="input" placeholder="পরিবর্তন না করলে খালি রাখুন" autocomplete="new-password">
                        </div>
                        <div>
                            <label class="label" for="sms_enabled_help">SMS স্ট্যাটাস</label>
                            <div class="rounded-xl border border-line px-4 py-3">
                                <label class="flex cursor-pointer items-center gap-3">
                                    <input type="hidden" name="sms[enabled]" value="0">
                                    <input type="checkbox" name="sms[enabled]" value="1" class="h-4 w-4 rounded border-line text-brand-600" @checked($bool('sms.enabled'))>
                                    <span class="text-sm font-medium text-ink">SMS পাঠানো চালু</span>
                                </label>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="sms_otp_template">OTP টেমপ্লেট</label>
                            <textarea name="sms[otp_template]" id="sms_otp_template" rows="3" class="input" maxlength="300">{{ $val('sms.otp_template') }}</textarea>
                            <p class="help">প্লেসহোল্ডার: <code>{site}</code>, <code>{code}</code>, <code>{minutes}</code>।</p>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">লোকাল টেস্টিং</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-soft">ড্রাইভার <strong>Log</strong> থাকলে OTP কোড <code>storage/logs/laravel.log</code>-এ লেখা হবে। প্রোডাকশনে HTTP গেটওয়ে সেট করলে আসল SMS যাবে।</p>
                </div>
            </div>
        </section>

        {{-- Donors --}}
        <section data-settings-panel="donors" class="{{ $activeTab === 'donors' ? '' : 'hidden' }}">
            <div class="card max-w-3xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-ink">ডোনার নীতি</h3>
                <div class="mt-4 space-y-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                        <input type="hidden" name="donors[auto_approve]" value="0">
                        <input type="checkbox" name="donors[auto_approve]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('donors.auto_approve'))>
                        <span>
                            <span class="block text-sm font-medium text-ink">ডোনার আবেদন স্বয়ংক্রিয় অনুমোদন</span>
                            <span class="block text-xs text-ink-muted">সতর্কতা: ম্যানুয়াল যাচাই ছাড়া প্রোফাইল পাবলিক হবে।</span>
                        </span>
                    </label>
                </div>
                <p class="mt-4 text-xs leading-relaxed text-ink-muted">লগইন বাধ্যতামূলক ও দৈনিক নম্বর দেখার সীমা “নিরাপত্তা ও ভেরিফিকেশন” ট্যাবে আছে। 0 = আনলিমিটেড।</p>
            </div>
        </section>

        {{-- Donation --}}
        <section data-settings-panel="donation" class="{{ $activeTab === 'donation' ? '' : 'hidden' }}">
            <div class="grid gap-6 xl:grid-cols-2">
                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">সাইট ডোনেশন</h3>
                    <p class="mt-1 text-xs text-ink-muted">সাইটের খরচ চালাতে স্বেচ্ছা সহযোগিতা নেওয়ার অপশন।</p>

                    <div class="mt-4 space-y-4">
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                            <input type="hidden" name="donation[enabled]" value="0">
                            <input type="checkbox" name="donation[enabled]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('donation.enabled'))>
                            <span>
                                <span class="block text-sm font-medium text-ink">ডোনেশন সিস্টেম চালু</span>
                                <span class="block text-xs text-ink-muted">পাবলিক সাইটে “সহযোগিতা করুন” পেজ দেখানো হবে।</span>
                            </span>
                        </label>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label" for="donation_currency">কারেন্সি</label>
                                <input type="text" name="donation[currency]" id="donation_currency" class="input" value="{{ $val('donation.currency', 'BDT') }}" maxlength="10">
                            </div>
                            <div>
                                <label class="label" for="donation_min_amount">সর্বনিম্ন পরিমাণ</label>
                                <input type="number" name="donation[min_amount]" id="donation_min_amount" class="input" min="1" value="{{ $val('donation.min_amount', 50) }}">
                            </div>
                            <div>
                                <label class="label" for="donation_goal_amount">লক্ষ্য (0 = নেই)</label>
                                <input type="number" name="donation[goal_amount]" id="donation_goal_amount" class="input" min="0" value="{{ $val('donation.goal_amount', 0) }}">
                            </div>
                            <div class="flex items-end">
                                <label class="flex w-full cursor-pointer items-center gap-3 rounded-xl border border-line px-4 py-3">
                                    <input type="hidden" name="donation[show_goal]" value="0">
                                    <input type="checkbox" name="donation[show_goal]" value="1" class="h-4 w-4 rounded border-line text-brand-600" @checked($bool('donation.show_goal'))>
                                    <span class="text-sm font-medium text-ink">লক্ষ্য প্রোগ্রেস দেখান</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">পেমেন্ট মাধ্যম</h3>
                    <div class="mt-4 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="label" for="donation_bkash_number">বিকাশ নম্বর</label>
                                <input type="text" name="donation[bkash_number]" id="donation_bkash_number" class="input" value="{{ $val('donation.bkash_number') }}" maxlength="30">
                            </div>
                            <div>
                                <label class="label" for="donation_nagad_number">নগদ নম্বর</label>
                                <input type="text" name="donation[nagad_number]" id="donation_nagad_number" class="input" value="{{ $val('donation.nagad_number') }}" maxlength="30">
                            </div>
                            <div>
                                <label class="label" for="donation_rocket_number">রকেট নম্বর</label>
                                <input type="text" name="donation[rocket_number]" id="donation_rocket_number" class="input" value="{{ $val('donation.rocket_number') }}" maxlength="30">
                            </div>
                        </div>
                        <div>
                            <label class="label" for="donation_bank_details">ব্যাংক তথ্য</label>
                            <textarea name="donation[bank_details]" id="donation_bank_details" rows="3" class="input" maxlength="1000">{{ $val('donation.bank_details') }}</textarea>
                        </div>
                        <div>
                            <label class="label" for="donation_instructions">নির্দেশনা</label>
                            <textarea name="donation[instructions]" id="donation_instructions" rows="3" class="input" maxlength="1000">{{ $val('donation.instructions') }}</textarea>
                        </div>
                        <div>
                            <label class="label" for="donation_thank_you">ধন্যবাদ বার্তা</label>
                            <input type="text" name="donation[thank_you]" id="donation_thank_you" class="input" value="{{ $val('donation.thank_you') }}" maxlength="300">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Ads --}}
        <section data-settings-panel="ads" class="{{ $activeTab === 'ads' ? '' : 'hidden' }}">
            <div class="card max-w-4xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-ink">বিজ্ঞাপন</h3>
                <div class="mt-4 space-y-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                        <input type="hidden" name="ads[enabled]" value="0">
                        <input type="checkbox" name="ads[enabled]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('ads.enabled'))>
                        <span>
                            <span class="block text-sm font-medium text-ink">সাইটে বিজ্ঞাপন চালু</span>
                            <span class="block text-xs text-ink-muted">ব্যানার ও অন্যান্য অ্যাড স্লট।</span>
                        </span>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line px-4 py-3">
                        <input type="hidden" name="ads[popunder_enabled]" value="0">
                        <input type="checkbox" name="ads[popunder_enabled]" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-600" @checked($bool('ads.popunder_enabled'))>
                        <span>
                            <span class="block text-sm font-medium text-ink">পপআন্ডার চালু</span>
                            <span class="block text-xs text-ink-muted">নিচের কোড পাবলিক পেজে লোড হবে।</span>
                        </span>
                    </label>
                </div>
                <div class="mt-4">
                    <label class="label" for="ads_popunder_code">পপআন্ডার কোড</label>
                    <textarea name="ads[popunder_code]" id="ads_popunder_code" rows="6" class="input font-mono text-xs" spellcheck="false">{{ $val('ads.popunder_code') }}</textarea>
                </div>
            </div>
        </section>

        {{-- Others --}}
        <section data-settings-panel="others" class="{{ $activeTab === 'others' ? '' : 'hidden' }}">
            <div class="grid gap-6 xl:grid-cols-2">
                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">অন্যান্য</h3>
                    <div class="mt-4 space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="label" for="others_timezone">টাইমজোন</label>
                                <select name="others[timezone]" id="others_timezone" class="input">
                                    @foreach (['Asia/Dhaka', 'Asia/Kolkata', 'Asia/Karachi', 'Asia/Dubai', 'Asia/Riyadh', 'Asia/Singapore', 'Europe/London', 'Europe/Paris', 'America/New_York', 'America/Los_Angeles', 'Australia/Sydney', 'UTC'] as $timezone)
                                        <option value="{{ $timezone }}" @selected($val('others.timezone', 'Asia/Dhaka') === $timezone)>{{ $timezone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label" for="others_locale">ভাষা</label>
                                <select name="others[locale]" id="others_locale" class="input">
                                    @foreach ($locales as $code => $label)
                                        <option value="{{ $code }}" @selected($val('others.locale', 'bn') === $code)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="help">নতুন ভাষা যোগ করতে নিচে JSON ডিকশনারি আপলোড করুন।</p>
                            </div>
                            <div>
                                <label class="label" for="others_location_country_code">লোকেশন দেশের কোড</label>
                                <input type="text" name="others[location_country_code]" id="others_location_country_code"
                                       class="input uppercase" maxlength="2" placeholder="BD"
                                       value="{{ strtoupper((string) $val('others.location_country_code', 'BD')) }}">
                                <p class="help">ISO দুই অক্ষরের কোড (যেমন BD, IN, US)। এই দেশের বিভাগ, জেলা ও উপজেলা দেখানো হবে।</p>
                            </div>
                        </div>
                        <div>
                            <label class="label" for="others_footer_text">ফুটার টেক্সট</label>
                            <textarea name="others[footer_text]" id="others_footer_text" rows="2" class="input" maxlength="500">{{ $val('others.footer_text') }}</textarea>
                        </div>
                        <div>
                            <label class="label" for="others_analytics_code">অ্যানালিটিক্স / কাস্টম কোড</label>
                            <textarea name="others[analytics_code]" id="others_analytics_code" rows="5" class="input font-mono text-xs" spellcheck="false">{{ $val('others.analytics_code') }}</textarea>
                            <p class="help">Google Analytics বা অন্য ট্র্যাকিং কোড।</p>
                        </div>
                    </div>
                </div>

                <div class="card p-5 sm:p-6">
                    <h3 class="text-base font-semibold text-ink">তথ্য</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-soft">সাইটটি বাংলাদেশের পাশাপাশি যেকোনো দেশে ব্যবহারের জন্য প্রস্তুত। ডোনার আবেদনে দেশ অনুযায়ী ঠিকানা নেওয়া হয়, আর SMS/মেইল/পেমেন্ট সবই অ্যাডমিন প্যানেল থেকে কনফিগার করা যায়।</p>
                </div>

                <div class="card p-5 sm:p-6 xl:col-span-2">
                    <h3 class="text-base font-semibold text-ink">নতুন ভাষা ইমপোর্ট</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-soft">
                        সাইটের লেখাগুলো বাংলায় লেখা। অন্য ভাষায় দেখাতে চাইলে বাংলা টেক্সটকে কী-ধরে একটি JSON ডিকশনারি আপলোড করুন।
                        ফরম্যাট: <code class="rounded bg-raised px-1.5 py-0.5 text-xs">{"বাংলা টেক্সট": "Your translation"}</code>।
                        ইমপোর্ট করলে ভাষাটি সাথে সাথে সক্রিয় হবে।
                    </p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label" for="language_code">ভাষা কোড</label>
                            <input type="text" name="language_code" id="language_code" class="input lowercase" maxlength="12"
                                   placeholder="hi, ar, es, pt_BR" value="{{ old('language_code') }}">
                            <p class="help">একই কোড দিয়ে আবার আপলোড করলে পুরোনো অনুবাদগুলোর সাথে নতুনগুলো যোগ হবে।</p>
                        </div>
                        <div>
                            <label class="label" for="language_file">JSON ডিকশনারি</label>
                            <input type="file" name="language_file" id="language_file" class="input" accept=".json,application/json">
                            @error('language_file') <p class="mt-1 text-xs font-medium text-brand-700">{{ $message }}</p> @enderror
                            @error('language_code') <p class="mt-1 text-xs font-medium text-brand-700">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <p class="help mt-3">ডিকশনারি ফাইল <code>storage/app/languages/{locale}.json</code> এ রাখা হয়। উদাহরণ ফাইল: <code>docs/language-dictionary-example.json</code>।</p>
                </div>
            </div>
        </section>

        <div class="mt-6 flex flex-wrap justify-end gap-2">
            <button type="reset" class="btn-ghost">রিসেট</button>
            <button type="submit" class="btn-primary">সব সেটিংস সংরক্ষণ করুন</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const tabs = Array.from(document.querySelectorAll('[data-settings-tab]'));
            const panels = Array.from(document.querySelectorAll('[data-settings-panel]'));
            const hidden = document.getElementById('settings-active-tab');

            function activate(key) {
                tabs.forEach((tab) => {
                    const active = tab.dataset.settingsTab === key;
                    tab.classList.toggle('bg-brand-600', active);
                    tab.classList.toggle('text-white', active);
                    tab.classList.toggle('hover:bg-brand-700', active);
                    tab.classList.toggle('text-ink-soft', !active);
                    tab.classList.toggle('hover:bg-raised', !active);
                });

                panels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.settingsPanel !== key));

                if (hidden) {
                    hidden.value = key;
                }
            }

            tabs.forEach((tab) => tab.addEventListener('click', () => activate(tab.dataset.settingsTab)));

            document.querySelectorAll('[data-color-picker]').forEach((picker) => {
                const value = picker.parentElement.querySelector('[data-color-value]');

                if (!value) {
                    return;
                }

                const sync = () => {
                    value.value = picker.value.toUpperCase();
                };

                picker.addEventListener('input', sync);
                sync();
            });
        })();
    </script>
@endpush
