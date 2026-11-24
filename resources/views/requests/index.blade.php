@extends('layouts.app')

@section('title', 'জরুরি রক্তের অনুরোধ — One Blood Bank')
@section('meta_description', 'চলমান রক্তের অনুরোধগুলো দেখুন অথবা নিজের প্রয়োজন পোস্ট করুন।')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">জরুরি রক্তের অনুরোধ</h1>
            <p class="mt-1 text-sm text-slate-600">চলমান {{ number_format($requests->total()) }} টি অনুরোধ</p>
        </div>

        @auth
            <button type="button" data-menu-toggle="request-form" class="btn-primary">+ অনুরোধ পোস্ট করুন</button>
        @else
            <a href="{{ route('login') }}" class="btn-primary">অনুরোধ পোস্ট করতে লগইন করুন</a>
        @endauth
    </div>

    @auth
        <div id="request-form" class="mt-5 hidden">
            <form method="POST" action="{{ route('requests.store') }}" class="card space-y-4 p-5 sm:p-6">
                @csrf
                <h2 class="text-base font-bold text-slate-900">নতুন রক্তের অনুরোধ</h2>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="label" for="patient_name">রোগীর নাম</label>
                        <input type="text" name="patient_name" id="patient_name" class="input" value="{{ old('patient_name') }}" required>
                    </div>
                    <div>
                        <label class="label" for="blood_group">রক্তের গ্রুপ</label>
                        <select name="blood_group" id="blood_group" class="input" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach (\App\Enums\BloodGroup::options() as $value => $text)
                                <option value="{{ $value }}" @selected(old('blood_group') === $value)>{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="units">ব্যাগ</label>
                        <input type="number" name="units" id="units" class="input" value="{{ old('units', 1) }}" min="1" max="10" required>
                    </div>
                    <div>
                        <label class="label" for="contact_phone">যোগাযোগের নম্বর</label>
                        <input type="tel" name="contact_phone" id="contact_phone" class="input" value="{{ old('contact_phone') }}" placeholder="+8801712345678" pattern="\+?[0-9][0-9\s\-()]{6,19}" maxlength="20" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="hospital">হাসপাতাল</label>
                        <input type="text" name="hospital" id="hospital" class="input" value="{{ old('hospital') }}" placeholder="যেমন: ঢাকা মেডিকেল কলেজ হাসপাতাল" required>
                    </div>
                    <div>
                        <label class="label" for="district_id">জেলা</label>
                        <select name="district_id" id="district_id" class="input">
                            <option value="">নির্বাচন করুন</option>
                            @foreach ($districts as $district)
                                <option value="{{ $district->id }}" @selected((string) old('district_id') === (string) $district->id)>{{ $district->localizedName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="needed_at">কখন প্রয়োজন</label>
                        <input type="datetime-local" name="needed_at" id="needed_at" class="input" value="{{ old('needed_at', now()->addHours(6)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-4">
                        <label class="label" for="note">অতিরিক্ত তথ্য</label>
                        <textarea name="note" id="note" rows="2" class="input" placeholder="রোগীর অবস্থা, ওয়ার্ড/বেড নম্বর ইত্যাদি">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">অনুরোধ পোস্ট করুন</button>
                </div>
            </form>
        </div>
    @endauth

    <form method="GET" action="{{ route('requests.index') }}" class="card mt-5 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-36">
            <label class="label" for="filter_blood_group">রক্তের গ্রুপ</label>
            <select name="blood_group" id="filter_blood_group" class="input">
                <option value="">সব গ্রুপ</option>
                @foreach (\App\Enums\BloodGroup::options() as $value => $text)
                    <option value="{{ $value }}" @selected(($filters['blood_group'] ?? null) === $value)>{{ $text }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-48">
            <label class="label" for="filter_district">জেলা</label>
            <select name="district_id" id="filter_district" class="input">
                <option value="">সব জেলা</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}" @selected((string) ($filters['district_id'] ?? '') === (string) $district->id)>{{ $district->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">ফিল্টার</button>
        <a href="{{ route('requests.index') }}" class="btn-ghost">রিসেট</a>
    </form>

    @if ($requests->isEmpty())
        <div class="card mt-6 p-10 text-center">
            <p class="text-4xl">✅</p>
            <h2 class="mt-3 text-lg font-bold text-slate-900">এখন কোনো চলমান অনুরোধ নেই</h2>
            <p class="mt-1 text-sm text-slate-600">আপনার প্রয়োজন থাকলে উপরে “অনুরোধ পোস্ট করুন” ক্লিক করুন।</p>
        </div>
    @else
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @foreach ($requests as $bloodRequest)
                <article class="card border-l-4 border-l-brand-600 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ $bloodRequest->patient_name }}</h2>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $bloodRequest->hospital }}</p>
                        </div>
                        <x-blood-badge :group="$bloodRequest->blood_group" />
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        <span class="badge bg-brand-50 text-brand-700">{{ $bloodRequest->units }} ব্যাগ</span>
                        <span class="badge bg-slate-100 text-slate-600">{{ $bloodRequest->district?->localizedName() ?? 'এলাকা নেই' }}</span>
                        <span class="badge bg-amber-50 text-amber-800">{{ $bloodRequest->needed_at->diffForHumans(now(), ['parts' => 1, 'short' => true]) }}</span>
                    </div>

                    @if ($bloodRequest->note)
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $bloodRequest->note }}</p>
                    @endif

                    <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                        <p class="text-sm font-semibold text-slate-900">
                            @if ($canSeePhone)
                                ☎ {{ $bloodRequest->contact_phone }}
                            @else
                                <span class="text-slate-400">☎ লগইন করে নম্বর দেখুন</span>
                            @endif
                        </p>
                        <span class="text-[11px] text-slate-400">{{ $bloodRequest->created_at->diffForHumans() }}</span>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $requests->links() }}</div>
    @endif
@endsection
