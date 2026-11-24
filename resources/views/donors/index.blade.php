@extends('layouts.app')

@section('title', 'ব্লাড ডোনার খুঁজুন — One Blood Bank')
@section('meta_description', 'বিভাগ, জেলা, উপজেলা ও রক্তের গ্রুপ অনুযায়ী ভেরিফাইড ব্লাড ডোনার খুঁজুন।')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">ডোনার খুঁজুন</h1>
            <p class="mt-1 text-sm text-slate-600">
                {{ number_format($donors->total()) }} জন ভেরিফাইড ডোনার পাওয়া গেছে
                @if (filled($filters['q'] ?? null))
                    — “{{ $filters['q'] }}”
                @endif
            </p>
        </div>
        <a href="{{ route('requests.index') }}" class="btn-secondary">🩸 রক্তের অনুরোধ দেখুন</a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('donors.index') }}" class="card mt-5 p-4 sm:p-5">
        <div class="grid gap-3 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <label class="label" for="q">নাম / গ্রাম / নম্বর</label>
                <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}" class="input" placeholder="যেমন: রাকিব / বুড়িচং / 01712...">
            </div>

            <div class="lg:col-span-2">
                <label class="label" for="blood_group">রক্তের গ্রুপ</label>
                <select name="blood_group" id="blood_group" class="input">
                    <option value="">সব গ্রুপ</option>
                    @foreach (\App\Enums\BloodGroup::options() as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['blood_group'] ?? null) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-6">
                <x-location-selects
                    :divisions="$divisions"
                    :districts="$districts"
                    :upazilas="$upazilas"
                    :unions="$unions"
                    :selected="$filters"
                />
            </div>
        </div>

        <div class="mt-3 grid gap-3 sm:grid-cols-3">
            <div>
                <label class="label" for="country">দেশ (আন্তর্জাতিক)</label>
                <select name="country" id="country" class="input">
                    <option value="">সব দেশ</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country }}" @selected(($filters['country'] ?? null) === $country)>{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="state">রাজ্য / প্রদেশ</label>
                <input type="text" name="state" id="state" class="input" value="{{ $filters['state'] ?? '' }}" placeholder="যেমন: Dhaka / Kerala">
            </div>
            <div>
                <label class="label" for="city">শহর / জেলা</label>
                <input type="text" name="city" id="city" class="input" value="{{ $filters['city'] ?? '' }}" placeholder="যেমন: Dhaka / Dubai">
            </div>
        </div>

        <div class="mt-3 flex flex-wrap items-end gap-3">
            <div class="min-w-40">
                <label class="label" for="available">উপলব্ধতা</label>
                <select name="available" id="available" class="input">
                    <option value="">সবাই</option>
                    <option value="1" @selected(($filters['available'] ?? null) === '1')>এখন রক্ত দিতে পারবেন</option>
                    <option value="0" @selected(($filters['available'] ?? null) === '0')>এখন পারবেন না</option>
                </select>
            </div>

            <div class="min-w-40">
                <label class="label" for="sort">সাজানো</label>
                <select name="sort" id="sort" class="input">
                    <option value="">সেরা রেটিং</option>
                    <option value="recent" @selected(($filters['sort'] ?? null) === 'recent')>সাম্প্রতিক রক্তদান</option>
                    <option value="newest" @selected(($filters['sort'] ?? null) === 'newest')>নতুন ডোনার</option>
                </select>
            </div>

            <div class="ml-auto flex gap-2">
                <a href="{{ route('donors.index') }}" class="btn-ghost">রিসেট</a>
                <button type="submit" class="btn-primary">ফিল্টার করুন</button>
            </div>
        </div>
    </form>

    @if ($donors->isEmpty())
        <div class="card mt-6 p-10 text-center">
            <p class="text-4xl">🔍</p>
            <h2 class="mt-3 text-lg font-bold text-slate-900">কোনো ডোনার পাওয়া যায়নি</h2>
            <p class="mt-1 text-sm text-slate-600">ফিল্টার একটু শিথিল করে আবার চেষ্টা করুন, অথবা কাছের জেলা নির্বাচন করুন।</p>
            <a href="{{ route('donors.index') }}" class="btn-secondary mt-4">সব ডোনার দেখুন</a>
        </div>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($donors as $donor)
                <x-donor-card :donor="$donor" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $donors->links() }}
        </div>
    @endif
@endsection
