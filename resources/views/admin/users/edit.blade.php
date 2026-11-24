@extends('layouts.admin')

@section('title', 'ইউজার এডিট: '.$user->name)
@section('heading', 'ইউজার এডিট')

@section('content')
    <nav class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
        <a href="{{ route('admin.users.index') }}" class="hover:text-brand-700">ইউজার</a>
        <span>/</span>
        <span class="text-slate-700">{{ $user->name }}</span>
    </nav>

    <div class="mt-4 grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="card p-5 sm:p-6">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-14 w-14 rounded-xl object-cover">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-500">@ {{ $user->username }} · আইডি #{{ $user->id }}</p>
                </div>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="name">পূর্ণ নাম <span class="text-brand-600">*</span></label>
                    <input type="text" name="name" id="name" class="input" value="{{ old('name', $user->name) }}" required maxlength="120">
                </div>
                <div>
                    <label class="label" for="username">ইউজারনেম</label>
                    <input type="text" id="username" class="input bg-slate-50" value="{{ $user->username }}" disabled>
                    <p class="help">ইউজারনেম পরিবর্তন করা যায় না।</p>
                </div>
                <div>
                    <label class="label" for="email">ইমেইল <span class="text-brand-600">*</span></label>
                    <input type="email" name="email" id="email" class="input" value="{{ old('email', $user->email) }}" required maxlength="255">
                </div>
                <div>
                    <label class="label" for="phone">মোবাইল <span class="text-brand-600">*</span></label>
                    <input type="tel" name="phone" id="phone" class="input" value="{{ old('phone', $user->phone) }}" required pattern="\+?[0-9][0-9\s\-()]{6,19}" maxlength="20" placeholder="+8801712345678">
                </div>
                <div>
                    <label class="label" for="role">রোল <span class="text-brand-600">*</span></label>
                    <select name="role" id="role" class="input" required>
                        @foreach (\App\Enums\UserRole::options() as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $user->role->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex w-full cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                               @checked((string) old('is_active', $user->is_active ? '1' : '0') === '1')>
                        <span>
                            <span class="block text-sm font-medium text-slate-800">অ্যাকাউন্ট সক্রিয়</span>
                            <span class="block text-xs text-slate-500">নিষ্ক্রিয় করলে ইউজার লগইন করতে পারবে না।</span>
                        </span>
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <label class="label" for="ban_reason">নিষ্ক্রিয়/ব্যানের কারণ</label>
                    <textarea name="ban_reason" id="ban_reason" rows="3" class="input" maxlength="500"
                              placeholder="প্রয়োজনে কারণ লিখুন — ইউজারকে দেখানো হবে">{{ old('ban_reason', $user->ban_reason) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-4">
                <a href="{{ route('admin.users.index') }}" class="btn-ghost">বাতিল</a>
                <button type="submit" class="btn-primary">পরিবর্তন সংরক্ষণ করুন</button>
            </div>
        </form>

        <aside class="space-y-6">
            <div class="card p-5">
                <h3 class="text-base font-bold text-slate-900">অ্যাকাউন্ট সারসংক্ষেপ</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li class="flex items-center justify-between gap-2">
                        <span>মোবাইল ভেরিফাইড</span>
                        <span class="font-semibold {{ $user->phone_verified_at ? 'text-emerald-600' : 'text-rose-600' }}">{{ $user->phone_verified_at ? '✓' : '✗' }}</span>
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <span>ইমেইল ভেরিফাইড</span>
                        <span class="font-semibold {{ $user->email_verified_at ? 'text-emerald-600' : 'text-rose-600' }}">{{ $user->email_verified_at ? '✓' : '✗' }}</span>
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <span>শেষ লগইন</span>
                        <span class="font-semibold text-slate-800">{{ $user->last_login_at?->format('d M Y, h:i A') ?? '—' }}</span>
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <span>যোগদান</span>
                        <span class="font-semibold text-slate-800">{{ $user->created_at?->format('d M Y') }}</span>
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <span>ডোনার প্রোফাইল</span>
                        @if ($user->donor)
                            <span class="badge {{ $user->donor->status->badgeClasses() }}">{{ $user->donor->status->label() }}</span>
                        @else
                            <span class="text-slate-400">নেই</span>
                        @endif
                    </li>
                </ul>
            </div>

            @if (! $user->is(auth()->user()))
                <div class="card border-rose-200 p-5">
                    <h3 class="text-base font-bold text-rose-700">ডেঞ্জার জোন</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">
                        ইউজার স্থায়ীভাবে মুছে ফেললে তার ডোনার প্রোফাইল ও সংশ্লিষ্ট তথ্যও মুছে যাবে।
                    </p>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-3"
                          onsubmit="return confirm('এই ইউজারকে স্থায়ীভাবে মুছে ফেলতে চান?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger w-full">ইউজার মুছুন</button>
                    </form>
                </div>
            @endif
        </aside>
    </div>
@endsection
