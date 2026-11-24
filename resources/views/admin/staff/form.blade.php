@extends('layouts.admin')

@php
    $isEdit = $member->exists;
@endphp

@section('title', $isEdit ? 'স্টাফ এডিট: '.$member->name : 'নতুন স্টাফ')
@section('heading', $isEdit ? 'স্টাফ এডিট' : 'নতুন স্টাফ')

@section('content')
    <nav class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
        <a href="{{ route('admin.staff.index') }}" class="hover:text-brand-700">স্টাফ</a>
        <span>/</span>
        <span class="text-slate-700">{{ $isEdit ? $member->name : 'নতুন' }}</span>
    </nav>

    <form method="POST" action="{{ $isEdit ? route('admin.staff.update', $member) : route('admin.staff.store') }}"
          class="card mt-4 max-w-3xl p-5 sm:p-6">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="name">পূর্ণ নাম <span class="text-brand-600">*</span></label>
                <input type="text" name="name" id="name" class="input" value="{{ old('name', $member->name) }}" required maxlength="120">
            </div>
            <div>
                <label class="label" for="username">ইউজারনেম <span class="text-brand-600">*</span></label>
                <input type="text" name="username" id="username" class="input font-mono" value="{{ old('username', $member->username) }}"
                       required minlength="3" maxlength="30" pattern="[a-z0-9_\-]+" placeholder="staff_rakib">
                <p class="help">শুধু ছোট হাতের ইংরেজি অক্ষর, সংখ্যা, ড্যাশ ও আন্ডারস্কোর।</p>
            </div>
            <div>
                <label class="label" for="email">ইমেইল <span class="text-brand-600">*</span></label>
                <input type="email" name="email" id="email" class="input" value="{{ old('email', $member->email) }}" required maxlength="255">
            </div>
            <div>
                <label class="label" for="phone">মোবাইল <span class="text-brand-600">*</span></label>
                <input type="tel" name="phone" id="phone" class="input" value="{{ old('phone', $member->phone) }}"
                       required pattern="\+?[0-9][0-9\s\-()]{6,19}" maxlength="20" placeholder="+8801712345678">
            </div>
            <div>
                <label class="label" for="role">রোল <span class="text-brand-600">*</span></label>
                <select name="role" id="role" class="input" required>
                    @foreach ([\App\Enums\UserRole::Admin, \App\Enums\UserRole::Manager, \App\Enums\UserRole::Staff] as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $member->role?->value) === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                <p class="help">শুধু অ্যাডমিন সেটিংস ও স্টাফ ম্যানেজমেন্ট করতে পারবে।</p>
            </div>
            <div class="flex items-end">
                <label class="flex w-full cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                           @checked((string) old('is_active', ($member->is_active ?? true) ? '1' : '0') === '1')>
                    <span class="text-sm font-medium text-slate-800">অ্যাকাউন্ট সক্রিয়</span>
                </label>
            </div>

            <div>
                <label class="label" for="password">পাসওয়ার্ড @unless ($isEdit) <span class="text-brand-600">*</span> @endunless</label>
                <input type="password" name="password" id="password" class="input" @required(! $isEdit) autocomplete="new-password">
                <p class="help">কমপক্ষে ৮ অক্ষর, অক্ষর ও সংখ্যা মিশিয়ে।</p>
            </div>
            <div>
                <label class="label" for="password_confirmation">পাসওয়ার্ড নিশ্চিত করুন</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="input" @required(! $isEdit) autocomplete="new-password">
                @if ($isEdit)
                    <p class="help">পরিবর্তন না করতে চাইলে খালি রাখুন।</p>
                @endif
            </div>
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-4">
            <a href="{{ route('admin.staff.index') }}" class="btn-ghost">বাতিল</a>
            <button type="submit" class="btn-primary">{{ $isEdit ? 'পরিবর্তন সংরক্ষণ করুন' : 'স্টাফ তৈরি করুন' }}</button>
        </div>
    </form>
@endsection
