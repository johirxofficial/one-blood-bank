@extends('layouts.admin')

@section('title', 'বিজ্ঞাপন')
@section('heading', 'বিজ্ঞাপন')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">বিজ্ঞাপন ম্যানেজমেন্ট</h2>
            <p class="mt-1 text-sm text-ink-soft">ব্যানার, পপআন্ডার ও স্ক্রিপ্ট বিজ্ঞাপন যোগ করুন। মোট {{ number_format($ads->total()) }} টি।</p>
        </div>
        <a href="{{ route('admin.ads.create') }}" class="btn-primary">+ নতুন বিজ্ঞাপন</a>
    </div>

    <div class="table-wrap mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th>বিজ্ঞাপন</th>
                    <th>ধরন</th>
                    <th>স্থান</th>
                    <th>শিডিউল</th>
                    <th>পরিসংখ্যান</th>
                    <th>স্ট্যাটাস</th>
                    <th class="text-right">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ads as $ad)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if ($ad->image_path)
                                    <img src="{{ asset('storage/'.$ad->image_path) }}" alt="{{ $ad->title }}" class="h-10 w-14 rounded-lg object-cover ring-1 ring-line">
                                @else
                                    <span class="grid h-10 w-14 place-items-center rounded-lg bg-raised text-ink-muted">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 9.5v5h3l8 4V5.5l-8 4H4Z" stroke-linejoin="round"/><path d="M18 9.5a4 4 0 0 1 0 5" stroke-linecap="round"/></svg>
                                    </span>
                                @endif
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-ink">{{ $ad->title }}</p>
                                    <p class="truncate text-xs text-ink-muted">ক্রম {{ $ad->sort_order }}</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-violet-50 text-violet-700">{{ $ad->type->label() }}</span></td>
                        <td><span class="badge bg-raised text-ink-soft">{{ $ad->placement->label() }}</span></td>
                        <td class="text-xs text-ink-muted">
                            <p>শুরু: {{ $ad->starts_at?->format('d M Y') ?? 'সবসময়' }}</p>
                            <p>শেষ: {{ $ad->ends_at?->format('d M Y') ?? 'সীমাহীন' }}</p>
                        </td>
                        <td class="text-xs text-ink-muted">
                            <p>ইম্প্রেশন: {{ number_format($ad->impressions) }}</p>
                            <p>ক্লিক: {{ number_format($ad->clicks) }}</p>
                        </td>
                        <td>
                            @if ($ad->is_active)
                                <span class="badge bg-[#edf5ee] text-[#15803d]">চালু</span>
                            @else
                                <span class="badge bg-raised text-ink-soft">বন্ধ</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.ads.edit', $ad) }}" class="btn-secondary btn-sm">এডিট</a>
                                <form method="POST" action="{{ route('admin.ads.destroy', $ad) }}"
                                      onsubmit="return confirm('এই বিজ্ঞাপন মুছে ফেলতে চান?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-brand-700 hover:bg-brand-50">মুছুন</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center">
                            <p class="text-sm font-semibold text-ink-soft">এখনো কোনো বিজ্ঞাপন নেই</p>
                            <a href="{{ route('admin.ads.create') }}" class="btn-primary btn-sm mt-3">প্রথম বিজ্ঞাপন যোগ করুন</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $ads->links() }}</div>
@endsection
