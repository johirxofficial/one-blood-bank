@extends('layouts.admin')

@section('title', 'নতুন বার্তা')
@section('heading', 'নতুন বার্তা')

@section('content')
    <nav class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
        <a href="{{ route('admin.messages.index') }}" class="hover:text-brand-700">বার্তা</a>
        <span>/</span>
        <span class="text-slate-700">নতুন</span>
    </nav>

    <form method="POST" action="{{ route('admin.messages.store') }}" class="card mt-4 max-w-3xl p-5 sm:p-6">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="audience">প্রাপকের গ্রুপ <span class="text-brand-600">*</span></label>
                <select name="audience" id="audience" class="input" required>
                    <option value="all" @selected(old('audience', 'all') === 'all')>সব সক্রিয় ইউজার</option>
                    <option value="donors" @selected(old('audience') === 'donors')>শুধু অনুমোদিত ডোনার</option>
                    <option value="staff" @selected(old('audience') === 'staff')>শুধু স্টাফ</option>
                    <option value="user" @selected(old('audience') === 'user')>নির্দিষ্ট একজন ইউজার</option>
                </select>
            </div>

            <div>
                <label class="label" for="user_id">নির্দিষ্ট ইউজার</label>
                <select name="user_id" id="user_id" class="input">
                    <option value="">ইউজার নির্বাচন করুন</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }} (@ {{ $user->username }})</option>
                    @endforeach
                </select>
                <p class="help">“নির্দিষ্ট একজন ইউজার” নির্বাচন করলে এটি বাধ্যতামূলক।</p>
            </div>

            <div class="sm:col-span-2">
                <label class="label" for="subject">বিষয় <span class="text-brand-600">*</span></label>
                <input type="text" name="subject" id="subject" class="input" value="{{ old('subject') }}" required maxlength="160">
            </div>

            <div class="sm:col-span-2">
                <label class="label" for="body">বার্তা <span class="text-brand-600">*</span></label>
                <textarea name="body" id="body" rows="8" class="input" required maxlength="5000">{{ old('body') }}</textarea>
                <p class="help">সর্বোচ্চ ৫,০০০ অক্ষর।</p>
            </div>

            <div class="sm:col-span-2">
                <label class="label" for="channel">ডেলিভারি চ্যানেল <span class="text-brand-600">*</span></label>
                <select name="channel" id="channel" class="input" required>
                    <option value="email" @selected(old('channel', 'email') === 'email')>শুধু ইমেইল</option>
                    <option value="internal" @selected(old('channel') === 'internal')>শুধু সাইটের ভেতরে</option>
                    <option value="both" @selected(old('channel') === 'both')>ইমেইল + সাইটের ভেতরে</option>
                </select>
            </div>
        </div>

        <div class="mt-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs leading-relaxed text-sky-900">
            <p class="font-semibold">ডেলিভারি তথ্য</p>
            <p class="mt-1">বর্তমানে মেইলার <span class="font-mono">{{ config('mail.default') }}</span> ড্রাইভারে চলছে। লোকাল পরিবেশে মেইল <span class="font-mono">storage/logs/laravel.log</span>-এ জমা হবে। সর্বোচ্চ ৫০০ জন প্রাপককে একবারে পাঠানো যাবে।</p>
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-4">
            <a href="{{ route('admin.messages.index') }}" class="btn-ghost">বাতিল</a>
            <button type="submit" class="btn-primary">বার্তা পাঠান</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const audience = document.getElementById('audience');
            const userSelect = document.getElementById('user_id');
            if (!audience || !userSelect) return;

            const sync = () => {
                const isSingle = audience.value === 'user';
                userSelect.required = isSingle;
                userSelect.disabled = !isSingle;
                userSelect.closest('div').classList.toggle('opacity-50', !isSingle);
            };

            audience.addEventListener('change', sync);
            sync();
        })();
    </script>
@endpush
