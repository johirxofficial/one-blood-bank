@extends('layouts.app')

@section('title', 'ডোনার আইডি কার্ড — One Blood Bank')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-ink">ডিজিটাল ডোনার আইডি কার্ড</h1>
                <p class="mt-1 text-sm text-ink-soft">প্রিন্ট বা ফোনে সংরক্ষণ করতে PDF ডাউনলোড করুন।</p>
            </div>
            <a href="{{ route('donor.card.download') }}" class="btn-primary">PDF ডাউনলোড</a>
        </div>

        <div class="mt-6 flex justify-center">
            <div class="w-full max-w-md overflow-hidden rounded-[14px] border border-line bg-surface">
                <div class="border-t-2 border-t-brand-700 p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-6 w-6 text-brand-700" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.25c-.32 0-.62.14-.82.39C9.4 5.1 5.25 10.4 5.25 14.25a6.75 6.75 0 0 0 13.5 0c0-3.85-4.15-9.15-5.93-11.61a1.04 1.04 0 0 0-.82-.39Z"/></svg>
                            <div class="leading-tight">
                                <p class="text-sm font-medium text-ink">One Blood Bank</p>
                                <p class="text-[10px] text-ink-muted">Verified blood donor</p>
                            </div>
                        </div>
                        <span class="rounded-[5px] bg-[#edf5ee] px-2 py-0.5 text-[10px] font-medium text-[#15803d]">যাচাইকৃত</span>
                    </div>

                    <div class="mt-5 flex items-center gap-3">
                        <img src="{{ $donor->user->avatarUrl() }}" alt="" class="h-16 w-16 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-base font-medium text-ink">{{ $donor->user->name }}</p>
                            <p class="truncate text-[11px] text-ink-muted">@ {{ $donor->user->username }}</p>
                            <p class="mt-1 truncate text-[11px] text-ink-muted">{{ $donor->locationLabel() }}</p>
                        </div>
                        <span class="tnum grid h-12 w-12 place-items-center rounded-lg border border-brand-200 bg-brand-50 text-lg font-medium text-brand-700">{{ $donor->blood_group }}</span>
                    </div>

                    <div class="mt-5 flex items-end justify-between gap-3 border-t border-dashed border-line pt-4">
                        <div class="text-[10px] leading-relaxed text-ink-muted">
                            <p>মোবাইল: {{ $donor->user->phone }}</p>
                            @foreach ($donor->contacts->take(2) as $contact)
                                <p>{{ $contact->displayLabel() }}: {{ $contact->phone }}</p>
                            @endforeach
                            <p>রক্তদান: {{ $donor->total_donations }} বার</p>
                            <p>ইস্যু: {{ now()->format('M Y') }}</p>
                        </div>
                        <div class="rounded-md border border-line bg-surface p-1">{!! $qrCode !!}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-6 p-5 text-sm text-ink-soft">
            <p class="font-medium text-ink">QR কোড কী কাজ করে?</p>
            <p class="mt-1 leading-relaxed">QR স্ক্যান করলে সরাসরি আপনার ডোনার প্রোফাইল খুলবে — সেখানে আপনার রক্তের গ্রুপ, এলাকা ও রেটিং দেখা যাবে। ফোন নম্বর শুধু লগইন করা ইউজাররা দেখতে পাবেন।</p>
        </div>
    </div>
@endsection
