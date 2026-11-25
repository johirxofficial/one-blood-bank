@extends('layouts.app')

@section('title', 'পাসওয়ার্ড রিসেট — One Blood Bank')

@section('content')
    <div class="mx-auto max-w-lg">
        <div class="card p-6 sm:p-8">
            <h1 class="text-xl font-semibold text-ink">পাসওয়ার্ড ভুলে গেছেন?</h1>
            <p class="mt-1 text-sm text-ink-soft">আপনার ইমেইল দিন — রিসেট লিংক পাঠানো হবে।</p>

            <form method="POST" action="{{ route('password.email') }}" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label class="label" for="email">ইমেইল</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="input" required autofocus placeholder="you@example.com">
                </div>

                <button type="submit" class="btn-primary w-full py-3">রিসেট লিংক পাঠান</button>

                <p class="text-center text-sm text-ink-soft">
                    <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">লগইনে ফিরে যান</a>
                </p>
            </form>
        </div>
    </div>
@endsection
