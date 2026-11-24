@extends('layouts.admin')

@section('title', 'রক্তের অনুরোধ')
@section('heading', 'রক্তের অনুরোধ')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">রক্তের অনুরোধ</h2>
            <p class="mt-1 text-sm text-slate-600">মোট {{ number_format($requests->total()) }} টি অনুরোধ। সম্পন্ন বা মেয়াদোত্তীর্ণ হিসেবে চিহ্নিত করুন।</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.requests.index') }}" class="card mt-5 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-44">
            <label class="label" for="status">স্ট্যাটাস</label>
            <select name="status" id="status" class="input">
                <option value="">সব স্ট্যাটাস</option>
                @foreach (\App\Enums\BloodRequestStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">ফিল্টার করুন</button>
        <a href="{{ route('admin.requests.index') }}" class="btn-ghost">রিসেট</a>
    </form>

    <div class="mt-5 space-y-4">
        @forelse ($requests as $bloodRequest)
            <article class="card p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-base font-bold text-slate-900">{{ $bloodRequest->patient_name }}</h3>
                            <span class="badge bg-brand-50 text-brand-700">{{ $bloodRequest->blood_group }}</span>
                            <span class="badge bg-slate-100 text-slate-600">{{ $bloodRequest->units }} ব্যাগ</span>
                            <span class="badge {{ $bloodRequest->status === \App\Enums\BloodRequestStatus::Open ? 'bg-emerald-50 text-emerald-700' : ($bloodRequest->status === \App\Enums\BloodRequestStatus::Fulfilled ? 'bg-sky-50 text-sky-700' : 'bg-slate-200 text-slate-600') }}">
                                {{ $bloodRequest->status->label() }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $bloodRequest->hospital }} · {{ $bloodRequest->upazila?->localizedName() ?? $bloodRequest->district?->localizedName() ?? 'এলাকা নেই' }}
                        </p>
                    </div>
                    <p class="text-xs text-slate-400">{{ $bloodRequest->created_at->diffForHumans() }}</p>
                </div>

                <div class="mt-3 grid gap-3 rounded-xl bg-slate-50 px-4 py-3 text-xs sm:grid-cols-3">
                    <div>
                        <p class="font-semibold uppercase tracking-wide text-slate-400">যোগাযোগ</p>
                        <p class="mt-0.5 text-sm font-semibold text-slate-800">☎ {{ $bloodRequest->contact_phone }}</p>
                    </div>
                    <div>
                        <p class="font-semibold uppercase tracking-wide text-slate-400">কখন প্রয়োজন</p>
                        <p class="mt-0.5 text-sm text-slate-800">{{ $bloodRequest->needed_at->format('d M Y, h:i A') }}</p>
                        <p class="text-slate-500">{{ $bloodRequest->needed_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p class="font-semibold uppercase tracking-wide text-slate-400">পোস্ট করেছেন</p>
                        <p class="mt-0.5 text-sm text-slate-800">{{ $bloodRequest->user?->name ?? 'গেস্ট' }}</p>
                        <p class="text-slate-500">{{ $bloodRequest->user?->phone ?? '—' }}</p>
                    </div>
                </div>

                @if ($bloodRequest->note)
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $bloodRequest->note }}</p>
                @endif

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">
                    <form method="POST" action="{{ route('admin.requests.update', $bloodRequest) }}" class="flex flex-wrap items-end gap-2">
                        @csrf
                        @method('PUT')
                        <div class="min-w-36">
                            <label class="label text-xs" for="request-status-{{ $bloodRequest->id }}">স্ট্যাটাস বদলান</label>
                            <select name="status" id="request-status-{{ $bloodRequest->id }}" class="input py-1.5 text-xs">
                                @foreach (\App\Enums\BloodRequestStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($bloodRequest->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary btn-sm">আপডেট</button>
                    </form>

                    <form method="POST" action="{{ route('admin.requests.destroy', $bloodRequest) }}"
                          onsubmit="return confirm('এই অনুরোধ মুছে ফেলতে চান?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost btn-sm text-rose-600 hover:bg-rose-50">মুছুন</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="card p-10 text-center">
                <p class="text-4xl">🚑</p>
                <p class="mt-3 text-sm font-semibold text-slate-700">কোনো অনুরোধ পাওয়া যায়নি</p>
                <p class="mt-1 text-xs text-slate-500">ফিল্টার পরিবর্তন করে আবার দেখুন।</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $requests->links() }}</div>
@endsection
