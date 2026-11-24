@extends('layouts.admin')

@section('title', 'সাইট ডোনেশন')
@section('heading', 'সাইট ডোনেশন')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">সাইট ডোনেশন</h2>
            <p class="mt-1 text-sm text-slate-600">ব্যবহারকারীর জমা দেওয়া সহযোগিতা যাচাই বা বাতিল করুন।</p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-4">
            <p class="text-xs text-slate-500">মোট যাচাইকৃত</p>
            <p class="mt-1 text-2xl font-extrabold text-emerald-600">{{ number_format($stats['verified_amount'], 0) }} {{ \App\Models\Setting::get('donation.currency', 'BDT') }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500">সহযোগী</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($stats['verified_count']) }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500">অপেক্ষমাণ</p>
            <p class="mt-1 text-2xl font-extrabold text-amber-600">{{ number_format($stats['pending_count']) }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-500">এই মাসে</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($stats['month_amount'], 0) }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.site-donations.index') }}" class="card mt-5 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-52 flex-1">
            <label class="label" for="q">নাম / ফোন / ইমেইল / ট্রানজেকশন</label>
            <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}" class="input" placeholder="খুঁজুন...">
        </div>
        <div class="min-w-40">
            <label class="label" for="status">স্ট্যাটাস</label>
            <select name="status" id="status" class="input">
                <option value="">সব</option>
                <option value="pending" @selected(($filters['status'] ?? null) === 'pending')>অপেক্ষমাণ</option>
                <option value="verified" @selected(($filters['status'] ?? null) === 'verified')>যাচাইকৃত</option>
                <option value="rejected" @selected(($filters['status'] ?? null) === 'rejected')>বাতিল</option>
            </select>
        </div>
        <button class="btn-primary" type="submit">ফিল্টার</button>
        <a href="{{ route('admin.site-donations.index') }}" class="btn-ghost">রিসেট</a>
    </form>

    <div class="table-wrap mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th>সহযোগী</th>
                    <th>পরিমাণ</th>
                    <th>মাধ্যম</th>
                    <th>ট্রানজেকশন</th>
                    <th>স্ট্যাটাস</th>
                    <th>তারিখ</th>
                    <th class="text-right">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($donations as $donation)
                    <tr>
                        <td>
                            <p class="font-semibold text-slate-900">{{ $donation->name }}</p>
                            <p class="text-xs text-slate-500">{{ $donation->phone ?: $donation->email ?: '—' }}</p>
                            @if ($donation->user)
                                <p class="text-[11px] text-slate-400">@ {{ $donation->user->username }}</p>
                            @endif
                        </td>
                        <td class="whitespace-nowrap font-bold text-slate-900">{{ number_format($donation->amount, 0) }} {{ $donation->currency }}</td>
                        <td class="text-xs text-slate-600">{{ $donation->methodLabel() }}</td>
                        <td class="font-mono text-xs text-slate-600">{{ $donation->transaction_id ?: '—' }}</td>
                        <td><span class="badge {{ $donation->statusBadgeClasses() }}">{{ $donation->statusLabel() }}</span></td>
                        <td class="whitespace-nowrap text-xs text-slate-500">{{ $donation->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-1.5">
                                <details class="relative">
                                    <summary class="btn-secondary btn-sm cursor-pointer list-none">আপডেট</summary>
                                    <div class="absolute right-0 z-20 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-4 text-left shadow-xl">
                                        <form method="POST" action="{{ route('admin.site-donations.update', $donation) }}" class="space-y-3">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="label">স্ট্যাটাস</label>
                                                <select name="status" class="input">
                                                    <option value="pending" @selected($donation->status === 'pending')>অপেক্ষমাণ</option>
                                                    <option value="verified" @selected($donation->status === 'verified')>যাচাইকৃত</option>
                                                    <option value="rejected" @selected($donation->status === 'rejected')>বাতিল</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="label">অ্যাডমিন নোট</label>
                                                <textarea name="admin_note" rows="2" class="input">{{ old('admin_note', $donation->admin_note) }}</textarea>
                                            </div>
                                            <button class="btn-primary btn-sm w-full" type="submit">সংরক্ষণ করুন</button>
                                        </form>
                                    </div>
                                </details>
                                <form method="POST" action="{{ route('admin.site-donations.destroy', $donation) }}" onsubmit="return confirm('রেকর্ডটি মুছে ফেলতে চান?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-ghost btn-sm text-rose-600 hover:bg-rose-50">মুছুন</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @if ($donation->message || $donation->admin_note)
                        <tr class="bg-slate-50/60">
                            <td colspan="7" class="text-xs text-slate-600">
                                @if ($donation->message)<span class="font-semibold">বার্তা:</span> {{ $donation->message }} @endif
                                @if ($donation->admin_note)<span class="ml-2 font-semibold">নোট:</span> {{ $donation->admin_note }} @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="7" class="py-10 text-center text-sm text-slate-500">কোনো ডোনেশন রেকর্ড নেই।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $donations->links() }}</div>
@endsection
