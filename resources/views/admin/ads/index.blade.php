@extends('layouts.admin')

@section('title', 'বিজ্ঞাপন')
@section('heading', 'বিজ্ঞাপন')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">বিজ্ঞাপন ম্যানেজমেন্ট</h2>
            <p class="mt-1 text-sm text-slate-600">ব্যানার, পপআন্ডার ও স্ক্রিপ্ট বিজ্ঞাপন যোগ করুন। মোট {{ number_format($ads->total()) }} টি।</p>
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
                                    <img src="{{ asset('storage/'.$ad->image_path) }}" alt="{{ $ad->title }}" class="h-10 w-14 rounded-lg object-cover ring-1 ring-slate-200">
                                @else
                                    <span class="grid h-10 w-14 place-items-center rounded-lg bg-slate-100 text-slate-400">📢</span>
                                @endif
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900">{{ $ad->title }}</p>
                                    <p class="truncate text-xs text-slate-500">ক্রম {{ $ad->sort_order }}</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-violet-50 text-violet-700">{{ $ad->type->label() }}</span></td>
                        <td><span class="badge bg-slate-100 text-slate-600">{{ $ad->placement->label() }}</span></td>
                        <td class="text-xs text-slate-500">
                            <p>শুরু: {{ $ad->starts_at?->format('d M Y') ?? 'সবসময়' }}</p>
                            <p>শেষ: {{ $ad->ends_at?->format('d M Y') ?? 'সীমাহীন' }}</p>
                        </td>
                        <td class="text-xs text-slate-500">
                            <p>ইম্প্রেশন: {{ number_format($ad->impressions) }}</p>
                            <p>ক্লিক: {{ number_format($ad->clicks) }}</p>
                        </td>
                        <td>
                            @if ($ad->is_active)
                                <span class="badge bg-emerald-50 text-emerald-700">চালু</span>
                            @else
                                <span class="badge bg-slate-200 text-slate-600">বন্ধ</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.ads.edit', $ad) }}" class="btn-secondary btn-sm">এডিট</a>
                                <form method="POST" action="{{ route('admin.ads.destroy', $ad) }}"
                                      onsubmit="return confirm('এই বিজ্ঞাপন মুছে ফেলতে চান?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-rose-600 hover:bg-rose-50">মুছুন</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center">
                            <p class="text-sm font-semibold text-slate-700">এখনো কোনো বিজ্ঞাপন নেই</p>
                            <a href="{{ route('admin.ads.create') }}" class="btn-primary btn-sm mt-3">প্রথম বিজ্ঞাপন যোগ করুন</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $ads->links() }}</div>
@endsection
