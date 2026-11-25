@extends('layouts.app')

@section('title', 'অ্যাকাউন্ট খুলুন — One Blood Bank')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="max-w-xl">
            <p class="eyebrow">নতুন অ্যাকাউন্ট</p>
            <h1 class="display-title mt-3 text-3xl sm:text-4xl">অ্যাকাউন্ট খুলুন</h1>
            <p class="mt-2 text-sm text-ink-soft">ডোনার খুঁজতে ও ডোনার হতে অ্যাকাউন্ট তৈরি করুন। মাত্র ১ মিনিট।</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="card mt-6 space-y-5 p-6 sm:p-8">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="name">পূর্ণ নাম <span class="text-brand-600">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="input" required autofocus autocomplete="name" placeholder="আপনার পুরো নাম">
                </div>

                <div>
                    <label class="label" for="username">ইউজারনেম <span class="text-brand-600">*</span></label>
                    <div class="flex">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-line bg-raised px-3 text-sm text-ink-muted">@</span>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" class="input rounded-l-none" required autocomplete="username" placeholder="rakib_hasan" pattern="[a-z0-9_\-]+">
                    </div>
                    <p class="help">ছোট হাতের অক্ষর, সংখ্যা, - ও _ ব্যবহার করুন। এটাই হবে আপনার প্রোফাইল লিংক।</p>
                </div>

                <div>
                    <label class="label" for="email">ইমেইল <span class="text-brand-600">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="input" required autocomplete="email" placeholder="you@example.com">
                </div>

                <div>
                    <label class="label" for="phone">মোবাইল নম্বর <span class="text-brand-600">*</span></label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="input" required autocomplete="tel" placeholder="+8801712345678" pattern="\+?[0-9][0-9\s\-()]{6,19}" maxlength="20">
                    <p class="help">
                        @if (\App\Models\Setting::phoneVerificationRequired())
                            এই নম্বরে OTP পাঠিয়ে যাচাই করা হবে।
                        @else
                            দেশের কোডসহ যেকোনো দেশের নম্বর ব্যবহার করা যাবে।
                        @endif
                    </p>
                </div>

                <div>
                    <label class="label" for="password">পাসওয়ার্ড <span class="text-brand-600">*</span></label>
                    <input type="password" name="password" id="password" class="input" required autocomplete="new-password" placeholder="কমপক্ষে ৮ অক্ষর">
                </div>

                <div>
                    <label class="label" for="password_confirmation">পাসওয়ার্ড নিশ্চিত করুন <span class="text-brand-600">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="input" required autocomplete="new-password" placeholder="আবার লিখুন">
                </div>
            </div>

            <div class="rounded-lg bg-raised/60 p-3 text-xs leading-relaxed text-ink-soft">
                <p class="font-medium text-ink">পাসওয়ার্ডে থাকতে হবে</p>
                <p class="mt-0.5">কমপক্ষে ৮ অক্ষর, অন্তত একটি অক্ষর ও একটি সংখ্যা।</p>
            </div>

            <label class="flex items-start gap-2 text-sm text-ink-soft">
                <input type="checkbox" name="terms" value="1" class="mt-0.5 h-4 w-4 rounded border-line text-brand-700 focus:ring-brand-600/30" required>
                <span>
                    @if (app()->getLocale() === 'en')
                        I agree to the <a href="{{ route('pages.show', 'terms') }}" class="font-medium text-brand-700 hover:underline">Terms</a>
                        and <a href="{{ route('pages.show', 'privacy-policy') }}" class="font-medium text-brand-700 hover:underline">Privacy Policy</a>.
                    @else
                        আমি <a href="{{ route('pages.show', 'terms') }}" class="font-medium text-brand-700 hover:underline">শর্তাবলী</a>
                        ও <a href="{{ route('pages.show', 'privacy-policy') }}" class="font-medium text-brand-700 hover:underline">গোপনীয়তা নীতি</a> সম্মত আছি।
                    @endif
                </span>
            </label>

            <button type="submit" class="btn-primary w-full">অ্যাকাউন্ট তৈরি করুন</button>

            <p class="text-center text-sm text-ink-soft">
                আগে থেকেই অ্যাকাউন্ট আছে?
                <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:underline">লগইন করুন</a>
            </p>
        </form>
    </div>
@endsection
