@extends('layouts.app')

@section('title', 'লগইন — One Blood Bank')

@section('content')
    <div class="mx-auto grid max-w-5xl overflow-hidden rounded-[16px] border border-line bg-surface lg:grid-cols-2">
        <div class="hidden bg-raised p-10 lg:flex lg:flex-col">
            <x-logo compact />
            <div class="my-auto py-10">
                <h2 class="display-title text-3xl leading-tight">রক্তের প্রয়োজনে<br>কাছের ডোনার খুঁজুন</h2>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-ink-soft">
                    বাংলাদেশের জেলা, উপজেলা ও ইউনিয়ন ভিত্তিক ভেরিফাইড ডোনার নেটওয়ার্ক। সম্পূর্ণ ফ্রি।
                </p>

                <ul class="mt-8 space-y-3 text-sm text-ink-soft">
                    @foreach (['নম্বর-ভেরিফাইড ডোনার', 'স্ক্র্যাপিং থেকে সুরক্ষিত', 'ডিজিটাল ডোনার আইডি কার্ড'] as $item)
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-brand-700" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 10.5 3.5 3.5L16 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            <p class="eyebrow">অ্যাকাউন্ট</p>
            <h1 class="display-title mt-3 text-3xl">লগইন করুন</h1>
            <p class="mt-2 text-sm text-ink-soft">ইমেইল, ইউজারনেম বা মোবাইল নম্বর দিয়ে লগইন করতে পারেন।</p>

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="label" for="login">ইমেইল / ইউজারনেম / নম্বর</label>
                    <input type="text" name="login" id="login" value="{{ old('login') }}" class="input" required autofocus autocomplete="username" placeholder="আপনার ইমেইল / ইউজারনেম">
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label class="label" for="password">পাসওয়ার্ড</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-brand-700 hover:underline">পাসওয়ার্ড ভুলে গেছেন?</a>
                    </div>
                    <input type="password" name="password" id="password" class="input" required autocomplete="current-password" placeholder="••••••••">
                </div>

                <label class="flex items-center gap-2 text-sm text-ink-soft">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-line text-brand-700 focus:ring-brand-600/30">
                    আমাকে মনে রাখুন
                </label>

                <button type="submit" class="btn-primary w-full">লগইন</button>
            </form>

            <p class="mt-6 text-center text-sm text-ink-soft">
                অ্যাকাউন্ট নেই?
                <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline">রেজিস্টার করুন</a>
            </p>

            @if (app()->isLocal())
                <div class="mt-6 rounded-lg border border-dashed border-line bg-raised/60 p-3 text-xs text-ink-muted">
                    <p class="font-medium text-ink-soft">ডেমো অ্যাকাউন্ট</p>
                    <p class="mt-1">অ্যাডমিন: admin@thebloodbank.test / password</p>
                    <p>স্টাফ: staff@thebloodbank.test / password</p>
                    <p>ইউজার: donor@thebloodbank.test / password</p>
                </div>
            @endif
        </div>
    </div>
@endsection
