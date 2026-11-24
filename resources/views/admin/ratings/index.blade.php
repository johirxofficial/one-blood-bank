@extends('layouts.admin')

@section('title', 'রেটিং ও রিভিউ')
@section('heading', 'রেটিং ও রিভিউ')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">রেটিং ও রিভিউ মডারেশন</h2>
            <p class="mt-1 text-sm text-slate-600">মোট {{ number_format($ratings->total()) }} টি রিভিউ। অপ্রকাশিত বা অপমানজনক রিভিউ লুকিয়ে রাখুন।</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.ratings.index') }}" class="card mt-5 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-44">
            <label class="label" for="status">স্ট্যাটাস</label>
            <select name="status" id="status" class="input">
                <option value="">সব স্ট্যাটাস</option>
                @foreach (\App\Enums\ReviewStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">ফিল্টার করুন</button>
        <a href="{{ route('admin.ratings.index') }}" class="btn-ghost">রিসেট</a>
    </form>

    <div class="mt-5 space-y-4">
        @forelse ($ratings as $rating)
            <article class="card p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900">
                            {{ $rating->donor?->user?->name ?? 'ডোনার' }}
                            <span class="font-normal text-slate-400">কে</span>
                            {{ $rating->user?->name ?? 'ইউজার' }}
                            <span class="font-normal text-slate-400">রেটিং দিয়েছেন</span>
                        </p>
                        <p class="mt-0.5 text-xs text-slate-500">
                            রক্তের গ্রুপ {{ $rating->donor?->blood_group ?? '—' }} ·
                            {{ $rating->created_at->format('d M Y, h:i A') }} ·
                            {{ $rating->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg text-amber-500">{{ str_repeat('★', (int) $rating->rating) }}<span class="text-slate-200">{{ str_repeat('★', 5 - (int) $rating->rating) }}</span></span>
                        <span class="badge {{ $rating->status === \App\Enums\ReviewStatus::Published ? 'bg-emerald-50 text-emerald-700' : ($rating->status === \App\Enums\ReviewStatus::Pending ? 'bg-amber-50 text-amber-700' : 'bg-slate-200 text-slate-600') }}">
                            {{ $rating->status->label() }}
                        </span>
                    </div>
                </div>

                @if ($rating->comment)
                    <p class="mt-3 rounded-xl bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-700">{{ $rating->comment }}</p>
                @else
                    <p class="mt-3 text-sm italic text-slate-400">কোনো মন্তব্য নেই।</p>
                @endif

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">
                    <form method="POST" action="{{ route('admin.ratings.update', $rating) }}" class="flex flex-wrap items-end gap-2">
                        @csrf
                        @method('PUT')
                        <div class="min-w-36">
                            <label class="label text-xs" for="rating-status-{{ $rating->id }}">স্ট্যাটাস বদলান</label>
                            <select name="status" id="rating-status-{{ $rating->id }}" class="input py-1.5 text-xs">
                                @foreach (\App\Enums\ReviewStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($rating->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary btn-sm">আপডেট</button>
                    </form>

                    <div class="flex items-center gap-2">
                        @if ($rating->donor?->user)
                            <a href="{{ route('admin.donors.show', $rating->donor) }}" class="btn-secondary btn-sm">ডোনার দেখুন</a>
                        @endif
                        <form method="POST" action="{{ route('admin.ratings.destroy', $rating) }}"
                              onsubmit="return confirm('এই রিভিউ স্থায়ীভাবে মুছে ফেলতে চান?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost btn-sm text-rose-600 hover:bg-rose-50">মুছুন</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="card p-10 text-center">
                <p class="text-4xl">⭐</p>
                <p class="mt-3 text-sm font-semibold text-slate-700">কোনো রিভিউ পাওয়া যায়নি</p>
                <p class="mt-1 text-xs text-slate-500">ফিল্টার পরিবর্তন করে আবার দেখুন।</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $ratings->links() }}</div>
@endsection
