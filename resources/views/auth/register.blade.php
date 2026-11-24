@extends('layouts.app')

@section('title', 'ফ্রি অ্যাকাউন্ট খুলুন — One Blood Bank')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="text-center">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">ফ্রি অ্যাকাউন্ট খুলুন</h1>
            <p class="mt-2 text-sm text-slate-600">ডোনার খুঁজতে ও ডোনার হতে অ্যাকাউন্ট তৈরি করুন। মাত্র ১ মিনিট।</p>
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
                    <div class="flex rounded-xl shadow-sm">
                        <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">@</span>
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

            <div class="rounded-xl bg-slate-50 p-3 text-xs leading-relaxed text-slate-600">
                <p class="font-semibold text-slate-700">পাসওয়ার্ডে থাকতে হবে:</p>
                <p>কমপক্ষে ৮ অক্ষর, অন্তত একটি অক্ষর ও একটি সংখ্যা।</p>
            </div>

            <label class="flex items-start gap-2 text-sm text-slate-600">
                <input type="checkbox" name="terms" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" required>
                <span>
                    @if (app()->getLocale() === 'en')
                        I agree to the <a href="{{ route('pages.show', 'terms') }}" class="font-semibold text-brand-700 hover:underline">Terms</a>
                        and <a href="{{ route('pages.show', 'privacy-policy') }}" class="font-semibold text-brand-700 hover:underline">Privacy Policy</a>.
                    @else
                        আমি <a href="{{ route('pages.show', 'terms') }}" class="font-semibold text-brand-700 hover:underline">শর্তাবলী</a>
                        ও <a href="{{ route('pages.show', 'privacy-policy') }}" class="font-semibold text-brand-700 hover:underline">গোপনীয়তা নীতি</a> সম্মত আছি।
                    @endif
                </span>
            </label>

            <button type="submit" class="btn-primary w-full py-3">অ্যাকাউন্ট তৈরি করুন</button>

            <p class="text-center text-sm text-slate-600">
                আগে থেকেই অ্যাকাউন্ট আছে?
                <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">লগইন করুন</a>
            </p>
        </form>
    </div>
@endsection
