@extends('layouts.app')

@section('title', 'নতুন পাসওয়ার্ড সেট করুন — One Blood Bank')

@section('content')
    <div class="mx-auto max-w-lg">
        <div class="card p-6 sm:p-8">
            <h1 class="text-xl font-extrabold text-slate-900">নতুন পাসওয়ার্ড সেট করুন</h1>

            <form method="POST" action="{{ route('password.store') }}" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="label" for="email">ইমেইল</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $email) }}" class="input" required>
                </div>

                <div>
                    <label class="label" for="password">নতুন পাসওয়ার্ড</label>
                    <input type="password" name="password" id="password" class="input" required autocomplete="new-password">
                </div>

                <div>
                    <label class="label" for="password_confirmation">আবার লিখুন</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="input" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn-primary w-full py-3">পাসওয়ার্ড রিসেট করুন</button>
            </form>
        </div>
    </div>
@endsection
