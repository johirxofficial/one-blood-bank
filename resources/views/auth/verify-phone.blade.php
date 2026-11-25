@extends('layouts.app')

@section('title', 'মোবাইল নম্বর ভেরিফাই করুন — One Blood Bank')

@section('content')
    <div class="mx-auto max-w-lg">
        <div class="card p-6 sm:p-8">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-50 text-brand-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path d="M7.5 4.5h-2A1.5 1.5 0 0 0 4 6c0 8.3 6.7 15 15 15a1.5 1.5 0 0 0 1.5-1.5v-2a1.5 1.5 0 0 0-1.2-1.47l-2.4-.48a1.5 1.5 0 0 0-1.45.5l-.9 1.1a12.5 12.5 0 0 1-5.2-5.2l1.1-.9a1.5 1.5 0 0 0 .5-1.45l-.48-2.4A1.5 1.5 0 0 0 7.5 4.5Z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <div>
                    <h1 class="text-xl font-semibold text-ink">মোবাইল নম্বর ভেরিফাই করুন</h1>
                    <p class="text-sm text-ink-soft">{{ auth()->user()->phone }}</p>
                </div>
            </div>

            <p class="mt-4 text-sm leading-relaxed text-ink-soft">
                আপনার নম্বরে ৬ ডিজিটের একটি OTP পাঠানো হয়েছে।
                @if (\App\Models\Setting::phoneVerificationRequired())
                    ডোনারদের তথ্য দেখতে ও ডোনার হতে এটি অবশ্যই যাচাই করতে হবে।
                @else
                    ভেরিফিকেশন বর্তমানে ঐচ্ছিক, তবে যাচাই করলে অ্যাকাউন্ট আরও নিরাপদ হয়।
                @endif
            </p>

            @unless (\App\Models\Setting::phoneVerificationRequired())
                <a href="{{ route('dashboard') }}" class="mt-3 inline-block text-sm font-semibold text-brand-700 hover:underline">এখনই বাদ দিয়ে ড্যাশবোর্ডে যান →</a>
            @endunless

            @if (session('dev_otp'))
                <div class="mt-4 rounded-xl border border-dashed border-[#e5d3a8] bg-[#f8efd9] p-3 text-sm text-[#6b4e16]">
                    <p class="font-semibold">ডেভেলপমেন্ট মোড (SMS গেটওয়ে নেই)</p>
                    <p class="mt-1">আপনার OTP: <span class="font-mono text-lg font-semibold tracking-widest">{{ session('dev_otp') }}</span></p>
                    <p class="mt-1 text-xs">লোকাল টেস্টের জন্য দেখানো হচ্ছে। প্রোডাকশনে SMS গেটওয়ে দিয়ে যাবে।</p>
                </div>
            @endif

            <form method="POST" action="{{ route('verification.verify') }}" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label class="label" for="code">OTP কোড</label>
                    <input type="text" name="code" id="code" inputmode="numeric" maxlength="6" required autofocus
                           class="input text-center text-2xl font-semibold tracking-[0.5em]" placeholder="••••••">
                </div>

                <button type="submit" class="btn-primary w-full py-3">যাচাই করুন</button>
            </form>

            <form method="POST" action="{{ route('verification.resend') }}" class="mt-3 text-center">
                @csrf
                <button type="submit" class="text-sm font-semibold text-brand-700 hover:underline">নতুন কোড পাঠান</button>
            </form>
        </div>
    </div>
@endsection
