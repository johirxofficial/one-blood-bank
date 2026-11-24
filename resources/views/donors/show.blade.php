@extends('layouts.app')

@section('title', $donor->user->name.' — ডোনার প্রোফাইল | One Blood Bank')
@section('meta_description', $donor->user->name.' ('.$donor->blood_group.') — '.$donor->locationLabel().'. ভেরিফাইড ব্লাড ডোনার, One Blood Bank.')

@section('content')
    <nav class="flex items-center gap-1.5 text-xs text-slate-500">
        <a href="{{ route('donors.index') }}" class="hover:text-brand-700">ডোনার লিস্ট</a>
        <span>/</span>
        <span class="text-slate-700">{{ $donor->user->name }}</span>
    </nav>

    <div class="mt-4 grid gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="space-y-6">
            {{-- Profile header --}}
            <div class="card overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-brand-700 via-brand-600 to-rose-500"></div>
                <div class="px-5 pb-5 sm:px-7">
                    <div class="-mt-10 flex flex-wrap items-end justify-between gap-4">
                        <div class="flex items-end gap-4">
                            <img src="{{ $donor->user->avatarUrl() }}" alt="{{ $donor->user->name }}"
                                 class="h-24 w-24 rounded-2xl border-4 border-white object-cover shadow-lg">
                            <div class="pb-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h1 class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $donor->user->name }}</h1>
                                    <span class="badge bg-sky-100 text-sky-700">✓ ভেরিফাইড</span>
                                    @if ($donor->is_available)
                                        <span class="badge bg-emerald-100 text-emerald-700">● রক্ত দিতে পারবেন</span>
                                    @else
                                        <span class="badge bg-rose-100 text-rose-700">● অনুপলব্ধ</span>
                                    @endif
                                </div>
                                <p class="mt-0.5 text-sm text-slate-500">@ {{ $donor->user->username }}</p>
                            </div>
                        </div>

                        <x-blood-badge :group="$donor->blood_group" size="lg" />
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-xl bg-slate-50 p-3 text-center">
                            <p class="text-lg font-extrabold text-slate-900">{{ $donor->rating_count ? number_format((float) $donor->rating_avg, 1) : '—' }}</p>
                            <p class="text-[11px] text-slate-500">রেটিং ({{ $donor->rating_count }})</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3 text-center">
                            <p class="text-lg font-extrabold text-slate-900">{{ $donor->total_donations }}</p>
                            <p class="text-[11px] text-slate-500">রক্তদান</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3 text-center">
                            <p class="text-lg font-extrabold text-slate-900">{{ $donor->last_donation_date?->format('d M Y') ?? '—' }}</p>
                            <p class="text-[11px] text-slate-500">সর্বশেষ রক্তদান</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3 text-center">
                            <p class="text-lg font-extrabold text-slate-900">{{ $donor->verified_at?->format('M Y') ?? '—' }}</p>
                            <p class="text-[11px] text-slate-500">ভেরিফাইড</p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @auth
                            <button type="button"
                                    data-reveal-phone="{{ route('donors.reveal', $donor->user->username) }}"
                                    data-donor-name="{{ $donor->user->name }}"
                                    class="btn-primary flex-1 sm:flex-none">
                                📞 নম্বর দেখুন
                                @if ($donor->contacts->isNotEmpty())
                                    <span class="ml-1 rounded-full bg-white/20 px-1.5 py-0.5 text-[10px]">+{{ $donor->contacts->count() }}</span>
                                @endif
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary flex-1 sm:flex-none">📞 নম্বর দেখতে লগইন করুন</a>
                        @endauth
                        <a href="{{ route('requests.index') }}" class="btn-secondary">রক্তের অনুরোধ দেখুন</a>
                    </div>

                    <p class="mt-2 text-[11px] text-slate-500">
                        প্রধান নম্বরসহ মোট {{ 1 + $donor->contacts->count() }}টি যোগাযোগ নম্বর আছে। নম্বর দেখলে সবগুলোই পাবেন।
                    </p>
                </div>
            </div>

            {{-- Details --}}
            <div class="card p-5 sm:p-7">
                <h2 class="text-base font-bold text-slate-900">ডোনারের তথ্য</h2>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">দেশ</dt>
                        <dd class="mt-0.5 text-sm text-slate-800">{{ $donor->country ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">রাজ্য / প্রদেশ</dt>
                        <dd class="mt-0.5 text-sm text-slate-800">{{ $donor->state ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">শহর / জেলা (আন্তর্জাতিক)</dt>
                        <dd class="mt-0.5 text-sm text-slate-800">{{ $donor->city ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">বিভাগ</dt>
                        <dd class="mt-0.5 text-sm text-slate-800">{{ $donor->division?->localizedName() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">জেলা</dt>
                        <dd class="mt-0.5 text-sm text-slate-800">{{ $donor->district?->localizedName() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">উপজেলা</dt>
                        <dd class="mt-0.5 text-sm text-slate-800">{{ $donor->upazila?->localizedName() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">ইউনিয়ন</dt>
                        <dd class="mt-0.5 text-sm text-slate-800">{{ $donor->union?->localizedName() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">গ্রাম / এলাকা / মহল্লা</dt>
                        <dd class="mt-0.5 text-sm text-slate-800">{{ $donor->village ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">পরবর্তী রক্তদান সম্ভব</dt>
                        <dd class="mt-0.5 text-sm text-slate-800">
                            {{ $donor->is_available ? 'এখনই' : ($donor->available_from?->format('d M Y') ?? '—') }}
                        </dd>
                    </div>
                    @if ($donor->address_details)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">বিস্তারিত ঠিকানা</dt>
                            <dd class="mt-0.5 text-sm leading-relaxed text-slate-800">{{ $donor->address_details }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Reviews --}}
            <div class="card p-5 sm:p-7">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">ইউজার রিভিউ</h2>
                    <x-star-rating :rating="$donor->rating_avg" :count="$donor->rating_count" />
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($donor->ratings as $rating)
                        <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-800">{{ $rating->user?->name ?? 'ইউজার' }}</p>
                                <span class="text-xs text-amber-500">{{ str_repeat('★', $rating->rating) }}<span class="text-slate-300">{{ str_repeat('★', 5 - $rating->rating) }}</span></span>
                            </div>
                            @if ($rating->comment)
                                <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $rating->comment }}</p>
                            @endif
                            <p class="mt-1.5 text-[11px] text-slate-400">{{ $rating->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">এখনো কোনো রিভিউ নেই।</p>
                    @endforelse
                </div>

                @auth
                    @unless ($donor->user_id === auth()->id())
                        <form method="POST" action="{{ route('donors.rate', $donor->user->username) }}" class="mt-6 rounded-xl border border-brand-100 bg-brand-50/40 p-4">
                            @csrf
                            <p class="text-sm font-semibold text-slate-800">{{ $userRating ? 'আপনার রেটিং আপডেট করুন' : 'আপনার অভিজ্ঞতা জানান' }}</p>

                            <div class="mt-3 flex items-center gap-4">
                                <div class="flex flex-row-reverse gap-1">
                                    @for ($star = 5; $star >= 1; $star--)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="rating" value="{{ $star }}" class="peer sr-only" @checked((int) old('rating', $userRating) === $star)>
                                            <span class="text-2xl text-slate-300 transition peer-checked:text-amber-500 hover:text-amber-400">★</span>
                                        </label>
                                    @endfor
                                </div>
                                <span class="text-xs text-slate-500">১ থেকে ৫ স্টার</span>
                            </div>

                            <textarea name="comment" rows="3" class="input mt-3" placeholder="আপনার মতামত লিখুন (ঐচ্ছিক)">{{ old('comment') }}</textarea>

                            <button type="submit" class="btn-primary mt-3">রেটিং জমা দিন</button>
                        </form>
                    @endunless
                @else
                    <div class="mt-6 rounded-xl border border-brand-100 bg-brand-50/40 p-4 text-sm text-slate-700">
                        রেটিং দিতে <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">লগইন করুন</a>।
                    </div>
                @endauth
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="space-y-6">
            <div class="card p-5">
                <h2 class="text-base font-bold text-slate-900">রক্তদানের ইতিহাস</h2>
                <ol class="mt-4 space-y-3">
                    @forelse ($donor->donations as $donation)
                        <li class="flex gap-3">
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $donation->donated_at->format('d M Y') }}</p>
                                <p class="text-xs text-slate-500">{{ $donation->hospital ?: 'হাসপাতাল উল্লেখ নেই' }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">এখনো কোনো রক্তদানের তথ্য যোগ করা হয়নি।</li>
                    @endforelse
                </ol>
            </div>

            <div class="card p-5">
                <h2 class="text-base font-bold text-slate-900">নিরাপত্তা</h2>
                <p class="mt-2 text-xs leading-relaxed text-slate-600">
                    ডোনারের নম্বর শুধুমাত্র রক্তের প্রয়োজনে ব্যবহার করুন। প্রতিটি নম্বর দেখার লগ রাখা হয়।
                    অপব্যবহার প্রমাণিত হলে অ্যাকাউন্ট বন্ধ করা হবে।
                </p>
                <a href="{{ route('pages.show', 'terms') }}" class="mt-3 inline-block text-xs font-semibold text-brand-700 hover:underline">শর্তাবলী পড়ুন →</a>
            </div>

            <x-ad placement="sidebar" />
        </aside>
    </div>
@endsection
