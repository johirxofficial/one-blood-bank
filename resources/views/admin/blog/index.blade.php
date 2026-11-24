@extends('layouts.admin')

@section('title', 'ব্লগ')
@section('heading', 'ব্লগ')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">ব্লগ পোস্ট</h2>
            <p class="mt-1 text-sm text-slate-600">মোট {{ number_format($posts->total()) }} টি পোস্ট। প্রকাশিত পোস্ট পাবলিক ব্লগে দেখা যাবে।</p>
        </div>
        <a href="{{ route('admin.blog.create') }}" class="btn-primary">+ নতুন পোস্ট</a>
    </div>

    <form method="GET" action="{{ route('admin.blog.index') }}" class="card mt-5 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-52 flex-1">
            <label class="label" for="q">শিরোনাম / ক্যাটাগরি</label>
            <input type="text" name="q" id="q" value="{{ $term }}" class="input" placeholder="খুঁজুন...">
        </div>
        <button class="btn-primary" type="submit">খুঁজুন</button>
        <a href="{{ route('admin.blog.index') }}" class="btn-ghost">রিসেট</a>
    </form>

    <div class="table-wrap mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th>পোস্ট</th>
                    <th>ক্যাটাগরি</th>
                    <th>স্ট্যাটাস</th>
                    <th>প্রকাশ</th>
                    <th>ভিউ</th>
                    <th class="text-right">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if ($post->coverUrl())
                                    <img src="{{ $post->coverUrl() }}" alt="" class="h-10 w-14 rounded-lg object-cover">
                                @else
                                    <span class="grid h-10 w-14 place-items-center rounded-lg bg-slate-100 text-lg">📝</span>
                                @endif
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900">{{ $post->title }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $post->author?->name ?? 'অ্যাডমিন' }} · {{ $post->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-slate-100 text-slate-700">{{ $post->category ?: '—' }}</span></td>
                        <td>
                            @if ($post->is_published)
                                <span class="badge bg-emerald-50 text-emerald-700">প্রকাশিত</span>
                            @else
                                <span class="badge bg-amber-50 text-amber-700">খসড়া</span>
                            @endif
                        </td>
                        <td class="text-xs text-slate-500">{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
                        <td class="text-xs text-slate-500">{{ number_format($post->views) }}</td>
                        <td>
                            <div class="flex flex-wrap items-center justify-end gap-1.5">
                                @if ($post->is_published)
                                    <a href="{{ $post->url() }}" target="_blank" rel="noopener" class="btn-secondary btn-sm">দেখুন ↗</a>
                                @endif
                                <a href="{{ route('admin.blog.edit', $post) }}" class="btn-secondary btn-sm">এডিট</a>
                                <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" onsubmit="return confirm('পোস্টটি মুছে ফেলতে চান?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-ghost btn-sm text-rose-600 hover:bg-rose-50">মুছুন</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-10 text-center text-sm text-slate-500">কোনো পোস্ট নেই। প্রথম পোস্ট তৈরি করুন।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $posts->links() }}</div>
@endsection
