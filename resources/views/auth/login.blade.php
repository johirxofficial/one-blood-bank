@extends('layouts.app')

@section('title', 'লগইন — One Blood Bank')

@section('content')
    <div class="mx-auto grid max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-gradient-to-br from-brand-700 via-brand-600 to-rose-500 p-10 text-white lg:block">
            <div class="hero-pattern absolute inset-0" aria-hidden="true"></div>
            <div class="relative">
                <x-logo compact inverse class="text-white" />
                <h2 class="mt-10 text-3xl font-extrabold leading-tight">রক্তের প্রয়োজনে<br>কাছের ডোনার খুঁজুন</h2>
                <p class="mt-4 text-sm leading-relaxed text-white/85">
                    বাংলাদেশের জেলা, উপজেলা ও ইউনিয়ন ভিত্তিক ভেরিফাইড ডোনার নেটওয়ার্ক। সম্পূর্ণ ফ্রি।
                </p>

                <ul class="mt-8 space-y-3 text-sm text-white/90">
                    <li class="flex items-center gap-2"><span class="grid h-5 w-5 place-items-center rounded-full bg-white/20 text-[10px]">✓</span> নম্বর-ভেরিফাইড ডোনার</li>
                    <li class="flex items-center gap-2"><span class="grid h-5 w-5 place-items-center rounded-full bg-white/20 text-[10px]">✓</span> স্ক্র্যাপিং থেকে সুরক্ষিত</li>
                    <li class="flex items-center gap-2"><span class="grid h-5 w-5 place-items-center rounded-full bg-white/20 text-[10px]">✓</span> ডিজিটাল ডোনার আইডি কার্ড</li>
                </ul>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">লগইন করুন</h1>
            <p class="mt-1 text-sm text-slate-600">ইমেইল, ইউজারনেম বা মোবাইল নম্বর দিয়ে লগইন করতে পারেন।</p>

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="label" for="login">ইমেইল / ইউজারনেম / নম্বর</label>
                    <input type="text" name="login" id="login" value="{{ old('login') }}" class="input" required autofocus autocomplete="username" placeholder="admin@thebloodbank.test">
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label class="label" for="password">পাসওয়ার্ড</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-700 hover:underline">পাসওয়ার্ড ভুলে গেছেন?</a>
                    </div>
                    <input type="password" name="password" id="password" class="input" required autocomplete="current-password" placeholder="••••••••">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    আমাকে মনে রাখুন
                </label>

                <button type="submit" class="btn-primary w-full py-3">লগইন</button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">
                অ্যাকাউন্ট নেই?
                <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:underline">ফ্রি রেজিস্টার করুন</a>
            </p>

            @if (app()->isLocal())
                <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-3 text-xs text-slate-500">
                    <p class="font-semibold text-slate-700">ডেমো অ্যাকাউন্ট</p>
                    <p class="mt-1">অ্যাডমিন: admin@thebloodbank.test / password</p>
                    <p>স্টাফ: staff@thebloodbank.test / password</p>
                    <p>ইউজার: donor@thebloodbank.test / password</p>
                </div>
            @endif
        </div>
    </div>
@endsection
