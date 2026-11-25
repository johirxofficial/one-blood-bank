@extends('layouts.admin')

@section('title', 'ডোনার ম্যানেজমেন্ট')
@section('heading', 'ডোনার')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">ডোনার ম্যানেজমেন্ট</h2>
            <p class="mt-1 text-sm text-ink-soft">আবেদন যাচাই, অনুমোদন, বাতিল ও স্থগিত করুন। মোট {{ number_format($donors->total()) }} জন।</p>
        </div>
        <a href="{{ route('admin.donors.index', ['status' => 'pending']) }}" class="btn-primary">অপেক্ষমাণ আবেদন</a>
    </div>

    <form method="GET" action="{{ route('admin.donors.index') }}" class="card mt-5 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-52 flex-1">
            <label class="label" for="q">নাম / ইউজারনেম / মোবাইল / এলাকা</label>
            <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}" class="input" placeholder="যেমন: রাকিব / 01712... / কুমিল্লা">
        </div>
        <div class="min-w-40">
            <label class="label" for="status">স্ট্যাটাস</label>
            <select name="status" id="status" class="input">
                <option value="">সব স্ট্যাটাস</option>
                @foreach (\App\Enums\DonorStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">ফিল্টার করুন</button>
        <a href="{{ route('admin.donors.index') }}" class="btn-ghost">রিসেট</a>
    </form>

    <div class="table-wrap mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th>ডোনার</th>
                    <th>রক্তের গ্রুপ</th>
                    <th>এলাকা</th>
                    <th>স্ট্যাটাস</th>
                    <th>উপলব্ধতা</th>
                    <th>ভেরিফাইড</th>
                    <th class="text-right">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($donors as $donor)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $donor->user->avatarUrl() }}" alt="{{ $donor->user->name }}" class="h-9 w-9 rounded-lg object-cover">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-ink">{{ $donor->user->name }}</p>
                                    <p class="truncate text-xs text-ink-muted">@ {{ $donor->user->username }} · {{ $donor->user->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-brand-50 text-brand-700">{{ $donor->blood_group }}</span></td>
                        <td class="text-xs">
                            <p class="font-medium text-ink-soft">{{ $donor->locationLabel() ?: '—' }}</p>
                            <p class="text-ink-muted">{{ $donor->country ?: ($donor->district?->localizedName() ?? '—') }}</p>
                        </td>
                        <td><span class="badge {{ $donor->status->badgeClasses() }}">{{ $donor->status->label() }}</span></td>
                        <td>
                            @if ($donor->is_available)
                                <span class="badge bg-[#edf5ee] text-[#15803d]">উপলব্ধ</span>
                            @else
                                <span class="badge bg-brand-50 text-brand-700" title="{{ $donor->available_from?->format('d M Y') }}">অনুপলব্ধ</span>
                            @endif
                        </td>
                        <td class="text-xs text-ink-muted">
                            {{ $donor->verified_at?->format('d M Y') ?? '—' }}
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.donors.show', $donor) }}" class="btn-secondary btn-sm">বিস্তারিত</a>
                                @if ($donor->status === \App\Enums\DonorStatus::Pending)
                                    <form method="POST" action="{{ route('admin.donors.approve', $donor) }}">
                                        @csrf
                                        <button type="submit" class="btn-primary btn-sm">অনুমোদন</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-sm text-ink-muted">কোনো ডোনার পাওয়া যায়নি।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $donors->links() }}</div>
@endsection
