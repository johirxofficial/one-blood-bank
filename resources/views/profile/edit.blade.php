@extends('layouts.app')

@section('title', 'প্রোফাইল সেটিংস — '.\App\Models\Setting::get('site.name', 'One Blood Bank'))

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">প্রোফাইল সেটিংস</h1>
                <p class="mt-1 text-sm text-slate-600">নাম, ছবি, ইমেইল, মোবাইল ও পাসওয়ার্ড পরিচালনা করুন।</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-secondary">ড্যাশবোর্ড</a>
        </div>

        <div class="mt-5 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card p-5 sm:p-6">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-wrap items-center gap-4 border-b border-slate-100 pb-5">
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-2xl object-cover ring-2 ring-white shadow">
                        <div class="min-w-0 flex-1">
                            <label class="label" for="avatar">প্রোফাইল ছবি</label>
                            <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp" class="input py-2">
                            <p class="help">JPG, PNG বা WebP — সর্বোচ্চ ২ MB। ছবি না দিলে ডিফল্ট ছবি দেখানো হবে।</p>
                            @if ($user->avatar_path)
                                <label class="mt-2 flex cursor-pointer items-center gap-2 text-xs text-slate-600">
                                    <input type="checkbox" name="remove_avatar" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600">
                                    বর্তমান ছবি মুছে ফেলুন
                                </label>
                            @endif
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
                            <input type="tel" name="phone" id="phone" class="input" value="{{ old('phone', $user->phone) }}" required maxlength="20" placeholder="+8801712345678 অথবা 01712345678">
                            <p class="help">দেশের কোডসহ যেকোনো দেশের নম্বর ব্যবহার করা যাবে।</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="btn-primary">প্রোফাইল সংরক্ষণ করুন</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('profile.password') }}" class="card p-5 sm:p-6">
                    @csrf
                    @method('PUT')
                    <h2 class="text-base font-bold text-slate-900">পাসওয়ার্ড পরিবর্তন</h2>
                    <p class="mt-1 text-xs text-slate-500">নিরাপত্তার জন্য বর্তমান পাসওয়ার্ড দিয়ে নতুন পাসওয়ার্ড দিন।</p>

                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="label" for="current_password">বর্তমান পাসওয়ার্ড</label>
                            <input type="password" name="current_password" id="current_password" class="input" required autocomplete="current-password">
                        </div>
                        <div>
                            <label class="label" for="password">নতুন পাসওয়ার্ড</label>
                            <input type="password" name="password" id="password" class="input" required autocomplete="new-password">
                        </div>
                        <div>
                            <label class="label" for="password_confirmation">নিশ্চিত করুন</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="input" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end border-t border-slate-100 pt-4">
                        <button type="submit" class="btn-secondary">পাসওয়ার্ড আপডেট করুন</button>
                    </div>
                </form>
            </div>

            <aside class="space-y-6">
                <div class="card p-5">
                    <h2 class="text-base font-bold text-slate-900">অ্যাকাউন্ট স্ট্যাটাস</h2>
                    <ul class="mt-3 space-y-3 text-sm text-slate-600">
                        <li class="flex items-center justify-between gap-2">
                            <span>মোবাইল</span>
                            @if ($user->phone_verified_at)
                                <span class="badge bg-emerald-50 text-emerald-700">ভেরিফাইড</span>
                            @else
                                <a href="{{ route('verification.notice') }}" class="badge bg-amber-50 text-amber-700 hover:bg-amber-100">যাচাই করুন</a>
                            @endif
                        </li>
                        <li class="flex items-center justify-between gap-2">
                            <span>ইমেইল</span>
                            @if ($user->email_verified_at)
                                <span class="badge bg-emerald-50 text-emerald-700">ভেরিফাইড</span>
                            @else
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button class="badge bg-amber-50 text-amber-700 hover:bg-amber-100">লিংক পাঠান</button>
                                </form>
                            @endif
                        </li>
                        <li class="flex items-center justify-between gap-2">
                            <span>রোল</span>
                            <span class="badge bg-slate-100 text-slate-700">{{ $user->role->label() }}</span>
                        </li>
                        <li class="flex items-center justify-between gap-2">
                            <span>যোগদান</span>
                            <span class="text-xs font-medium text-slate-700">{{ $user->created_at?->format('d M Y') }}</span>
                        </li>
                    </ul>
                </div>

                <div class="card p-5 text-sm leading-relaxed text-slate-600">
                    <p class="font-semibold text-slate-800">প্রোফাইল ছবি</p>
                    <p class="mt-1">আপনার ছবি ডোনার কার্ডেও ব্যবহার হবে। ছবি না থাকলে কার্ডে আপনার নামের প্রথম অক্ষর দেখানো হবে।</p>
                </div>
            </aside>
        </div>
    </div>
@endsection
