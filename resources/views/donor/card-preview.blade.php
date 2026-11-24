@extends('layouts.app')

@section('title', 'ডোনার আইডি কার্ড — One Blood Bank')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">ডিজিটাল ডোনার আইডি কার্ড</h1>
                <p class="mt-1 text-sm text-slate-600">প্রিন্ট বা ফোনে সংরক্ষণ করতে PDF ডাউনলোড করুন।</p>
            </div>
            <a href="{{ route('donor.card.download') }}" class="btn-primary">PDF ডাউনলোড</a>
        </div>

        <div class="mt-6 flex justify-center">
            <div class="w-full max-w-md rounded-2xl bg-gradient-to-br from-brand-700 to-brand-500 p-1 shadow-2xl shadow-brand-900/25">
                <div class="rounded-xl bg-white p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-600 text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.25c-.32 0-.62.14-.82.39C9.4 5.1 5.25 10.4 5.25 14.25a6.75 6.75 0 0 0 13.5 0c0-3.85-4.15-9.15-5.93-11.61a1.04 1.04 0 0 0-.82-.39Z"/></svg>
                            </span>
                            <div class="leading-tight">
                                <p class="text-sm font-extrabold text-slate-900">One Blood Bank</p>
                                <p class="text-[9px] font-bold tracking-wide text-brand-600">VERIFIED BLOOD DONOR</p>
                            </div>
                        </div>
                        <span class="rounded bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700">✓ VERIFIED</span>
                    </div>

                    <div class="mt-5 flex items-center gap-3">
                        <img src="{{ $donor->user->avatarUrl() }}" alt="" class="h-16 w-16 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-base font-bold text-slate-900">{{ $donor->user->name }}</p>
                            <p class="truncate text-[11px] text-slate-500">@ {{ $donor->user->username }}</p>
                            <p class="mt-1 truncate text-[11px] text-slate-500">{{ $donor->locationLabel() }}</p>
                        </div>
                        <span class="grid h-12 w-12 place-items-center rounded-lg bg-brand-50 text-lg font-extrabold text-brand-700">{{ $donor->blood_group }}</span>
                    </div>

                    <div class="mt-5 flex items-end justify-between gap-3 border-t border-dashed border-slate-200 pt-4">
                        <div class="text-[10px] leading-relaxed text-slate-500">
                            <p>মোবাইল: {{ $donor->user->phone }}</p>
                            @foreach ($donor->contacts->take(2) as $contact)
                                <p>{{ $contact->displayLabel() }}: {{ $contact->phone }}</p>
                            @endforeach
                            <p>রক্তদান: {{ $donor->total_donations }} বার</p>
                            <p>ইস্যু: {{ now()->format('M Y') }}</p>
                        </div>
                        <div class="rounded-md bg-white p-1 ring-1 ring-slate-200">{!! $qrCode !!}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-6 p-5 text-sm text-slate-600">
            <p class="font-semibold text-slate-800">QR কোড কী কাজ করে?</p>
            <p class="mt-1 leading-relaxed">QR স্ক্যান করলে সরাসরি আপনার ডোনার প্রোফাইল খুলবে — সেখানে আপনার রক্তের গ্রুপ, এলাকা ও রেটিং দেখা যাবে। ফোন নম্বর শুধু লগইন করা ইউজাররা দেখতে পাবেন।</p>
        </div>
    </div>
@endsection
