@extends('layouts.app')

@section('title', 'ডোনার আবেদন — One Blood Bank')

@section('content')
    @php
        $editing = $donor !== null;
        $isApproved = $donor?->isApproved() ?? false;
    @endphp

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                    {{ $editing ? 'ডোনার তথ্য আপডেট' : 'ডোনার হতে আবেদন করুন' }}
                </h1>
                <p class="mt-1 text-sm text-slate-600">
                    যাচাইয়ের পর আপনার প্রোফাইল ভেরিফাইড ডোনার লিস্টে দেখা যাবে।
                </p>
            </div>
            @if ($editing)
                <span class="badge {{ $donor->status->badgeClasses() }}">{{ $donor->status->label() }}</span>
            @endif
        </div>

        @if ($isApproved)
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                আপনি ভেরিফাইড ডোনার। ঠিকানা বা অন্যান্য তথ্য আপডেট করলে ভেরিফিকেশন বাতিল হবে না।
            </div>
        @elseif ($editing)
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                আপনার আবেদন অপেক্ষমাণ। তথ্য পরিবর্তন করলে আবেদনটি আবার যাচাইয়ের জন্য যাবে।
            </div>
        @endif

        <form method="POST" action="{{ route('donor.apply.store') }}" enctype="multipart/form-data" class="mt-5 space-y-5">
            @csrf

            {{-- Personal --}}
            <div class="card p-5 sm:p-7">
                <h2 class="text-base font-bold text-slate-900">ব্যক্তিগত তথ্য</h2>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2 flex items-center gap-4">
                        <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-200">
                        <div class="flex-1">
                            <label class="label" for="photo">প্রোফাইল ছবি (ঐচ্ছিক)</label>
                            <input type="file" name="photo" id="photo" accept="image/*" class="input py-2">
                            <p class="help">JPG/PNG, সর্বোচ্চ ২ MB। পরিষ্কার মুখের ছবি দিলে ডোনার কার্ডে সুন্দর দেখাবে।</p>
                        </div>
                    </div>

                    <div>
                        <label class="label" for="blood_group">রক্তের গ্রুপ <span class="text-brand-600">*</span></label>
                        <select name="blood_group" id="blood_group" class="input" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach (\App\Enums\BloodGroup::options() as $value => $text)
                                <option value="{{ $value }}" @selected(old('blood_group', $donor?->blood_group) === $value)>{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="gender">লিঙ্গ</label>
                        <select name="gender" id="gender" class="input">
                            <option value="">নির্বাচন করুন</option>
                            @foreach (\App\Enums\Gender::options() as $value => $text)
                                <option value="{{ $value }}" @selected(old('gender', $donor?->gender) === $value)>{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label" for="date_of_birth">জন্ম তারিখ</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="input"
                               value="{{ old('date_of_birth', $donor?->date_of_birth?->toDateString()) }}"
                               max="{{ now()->subYears((int) config('bloodbank.minimum_donor_age'))->toDateString() }}">
                        <p class="help">ডোনার হতে কমপক্ষে {{ config('bloodbank.minimum_donor_age') }} বছর বয়স হতে হবে।</p>
                    </div>

                    <div>
                        <label class="label" for="weight_kg">ওজন (কেজি)</label>
                        <input type="number" name="weight_kg" id="weight_kg" class="input" min="30" max="250"
                               value="{{ old('weight_kg', $donor?->weight_kg) }}" placeholder="যেমন: ৬৫">
                        <p class="help">কমপক্ষে {{ config('bloodbank.minimum_donor_weight_kg') }} কেজি প্রয়োজন।</p>
                    </div>

                    <div>
                        <label class="label" for="last_donation_date">সর্বশেষ রক্তদানের তারিখ</label>
                        <input type="date" name="last_donation_date" id="last_donation_date" class="input"
                               value="{{ old('last_donation_date', $donor?->last_donation_date?->toDateString()) }}"
                               max="{{ now()->toDateString() }}">
                        <p class="help">আগে রক্ত না দিলে খালি রাখুন।</p>
                    </div>
                </div>
            </div>

            {{-- Extra contact numbers --}}
            <div class="card p-5 sm:p-7" data-contact-repeater>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">অতিরিক্ত যোগাযোগ নম্বর</h2>
                        <p class="mt-1 text-xs text-slate-500">
                            আপনার প্রধান নম্বর <span class="font-semibold text-slate-700">{{ auth()->user()->phone }}</span> ইতিমধ্যে সংরক্ষিত আছে।
                            প্রয়োজনে আরও ৫টি পর্যন্ত নম্বর যোগ করুন — রোগীরা সহজে যোগাযোগ করতে পারবে।
                        </p>
                    </div>
                    <button type="button" data-add-contact class="btn-secondary btn-sm">+ আরও নম্বর</button>
                </div>

                @error('contacts')
                    <p class="mt-3 rounded-lg bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700">{{ $message }}</p>
                @enderror

                <div class="mt-4 space-y-3" data-contact-rows>
                    @php
                        $contactRows = old('contacts', $donor?->contacts?->map(fn ($contact) => [
                            'label' => $contact->label,
                            'phone' => $contact->phone,
                        ])->all() ?? []);

                        if (empty($contactRows)) {
                            $contactRows = [['label' => '', 'phone' => '']];
                        }
                    @endphp

                    @foreach ($contactRows as $index => $contact)
                        <div class="grid gap-3 sm:grid-cols-[1fr_1.4fr_auto] sm:items-start" data-contact-row>
                            <div>
                                <label class="label" for="contact-label-{{ $index }}">লেবেল</label>
                                <input type="text" name="contacts[{{ $index }}][label]" id="contact-label-{{ $index }}"
                                       class="input" maxlength="40" value="{{ $contact['label'] ?? '' }}"
                                       placeholder="যেমন: বাবা, অফিস, বিকল্প">
                            </div>
                            <div>
                                <label class="label" for="contact-phone-{{ $index }}">নম্বর</label>
                                <input type="tel" name="contacts[{{ $index }}][phone]" id="contact-phone-{{ $index }}"
                                       class="input @error('contacts.'.$index.'.phone') border-rose-300 @enderror"
                                       maxlength="30" value="{{ $contact['phone'] ?? '' }}" placeholder="+8801712345678">
                                @error('contacts.'.$index.'.phone')
                                    <p class="help text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:pt-6">
                                <button type="button" data-remove-contact
                                        class="btn-ghost w-full px-3 py-2 text-xs text-rose-600 hover:bg-rose-50 sm:w-auto">
                                    ✕ সরান
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <template data-contact-template>
                    <div class="grid gap-3 sm:grid-cols-[1fr_1.4fr_auto] sm:items-start" data-contact-row>
                        <div>
                            <label class="label">লেবেল</label>
                            <input type="text" name="contacts[__INDEX__][label]" class="input" maxlength="40"
                                   placeholder="যেমন: বাবা, অফিস, বিকল্প">
                        </div>
                        <div>
                            <label class="label">নম্বর</label>
                            <input type="tel" name="contacts[__INDEX__][phone]" class="input" maxlength="30"
                                   placeholder="+8801712345678">
                        </div>
                        <div class="sm:pt-6">
                            <button type="button" data-remove-contact
                                    class="btn-ghost w-full px-3 py-2 text-xs text-rose-600 hover:bg-rose-50 sm:w-auto">
                                ✕ সরান
                            </button>
                        </div>
                    </div>
                </template>

                <p class="mt-3 text-[11px] text-slate-500">একই নম্বর একাধিকবার যোগ করা যাবে না। ইমেইল/ফোন ভেরিফিকেশনের জন্য ব্যবহৃত নম্বরটিই প্রধান নম্বর।</p>
            </div>

            {{-- Location --}}
            <div class="card p-5 sm:p-7">
                <h2 class="text-base font-bold text-slate-900">ঠিকানা</h2>
                <p class="mt-1 text-xs text-slate-500">বাংলাদেশসহ যেকোনো দেশের জন্য প্রযোজ্য। সঠিক এলাকা দিলে কাছের রোগীরা আপনাকে সহজে খুঁজে পাবে।</p>

                @php
                    $selectedCountry = old('country', $donor?->country ?: \App\Models\Setting::get('site.country', 'Bangladesh'));
                @endphp

                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="label" for="country">দেশ <span class="text-brand-600">*</span></label>
                        <input type="text" name="country" id="country" class="input" list="country-list" required
                               value="{{ $selectedCountry }}" placeholder="যেমন: Bangladesh">
                        <datalist id="country-list">
                            @foreach ($countries as $country)
                                <option value="{{ $country }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="label" for="state">রাজ্য / প্রদেশ / বিভাগ</label>
                        <input type="text" name="state" id="state" class="input" value="{{ old('state', $donor?->state) }}" placeholder="যেমন: Dhaka / Kerala / Ontario">
                    </div>
                    <div>
                        <label class="label" for="city">শহর / জেলা</label>
                        <input type="text" name="city" id="city" class="input" value="{{ old('city', $donor?->city) }}" placeholder="যেমন: Dhaka / Dubai / London">
                    </div>
                </div>

                <details class="mt-5 rounded-xl border border-slate-200 bg-slate-50/60 p-4" {{ filled($donor?->division_id) || ! filled($donor?->state) ? 'open' : '' }}>
                    <summary class="cursor-pointer text-sm font-semibold text-slate-800">বাংলাদেশের বিস্তারিত ঠিকানা (ঐচ্ছিক)</summary>
                    <p class="mt-1 text-xs text-slate-500">আপনি বাংলাদেশে থাকলে বিভাগ, জেলা, উপজেলা ও ইউনিয়ন নির্বাচন করুন।</p>
                    <div class="mt-4">
                        <x-location-selects
                            :divisions="$divisions"
                            :districts="$districts"
                            :upazilas="$upazilas"
                            :unions="$unions"
                            :selected="[
                                'division_id' => $donor?->division_id,
                                'district_id' => $donor?->district_id,
                                'upazila_id' => $donor?->upazila_id,
                                'union_id' => $donor?->union_id,
                            ]"
                            :required="false"
                        />
                    </div>
                </details>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="village">গ্রাম / এলাকা / মহল্লা</label>
                        <input type="text" name="village" id="village" class="input" value="{{ old('village', $donor?->village) }}" placeholder="যেমন: পূর্ব পাড়া, মধ্যম বাজার">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="label" for="address_details">বিস্তারিত ঠিকানা (ঐচ্ছিক)</label>
                        <textarea name="address_details" id="address_details" rows="3" class="input" placeholder="বাসা/রোড, ল্যান্ডমার্ক — যা দিয়ে সহজে খুঁজে পাওয়া যায়">{{ old('address_details', $donor?->address_details) }}</textarea>
                        <p class="help">এটি ডোনার প্রোফাইলে দেখা যাবে। খুব সংবেদনশীল তথ্য (বাসার নম্বর) না দেওয়াই ভালো।</p>
                    </div>
                </div>
            </div>

            <div class="card flex flex-wrap items-center justify-between gap-3 p-5">
                <p class="text-xs leading-relaxed text-slate-500">
                    আবেদন করলে আপনি নিশ্চিত করছেন যে তথ্যগুলো সত্য এবং আপনি স্বেচ্ছায় রক্তদানে সম্মত।
                </p>
                <button type="submit" class="btn-primary px-6 py-3">
                    {{ $editing ? 'তথ্য সংরক্ষণ করুন' : 'আবেদন জমা দিন' }}
                </button>
            </div>
        </form>
    </div>
@endsection
