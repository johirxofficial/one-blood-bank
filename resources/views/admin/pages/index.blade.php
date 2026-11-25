@extends('layouts.admin')

@section('title', 'স্ট্যাটিক পেজ')
@section('heading', 'স্ট্যাটিক পেজ')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">স্ট্যাটিক পেজ</h2>
            <p class="mt-1 text-sm text-ink-soft">আমাদের সম্পর্কে, শর্তাবলী, গোপনীয়তা নীতি ইত্যাদি পেজ পরিচালনা করুন। মোট {{ number_format($pages->total()) }} টি।</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="btn-primary">+ নতুন পেজ</a>
    </div>

    <div class="table-wrap mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th>শিরোনাম</th>
                    <th>স্লাগ</th>
                    <th>স্ট্যাটাস</th>
                    <th>সর্বশেষ আপডেট</th>
                    <th class="text-right">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pages as $page)
                    <tr>
                        <td>
                            <p class="font-semibold text-ink">{{ $page->title }}</p>
                            @if ($page->meta_title)
                                <p class="truncate text-xs text-ink-muted">{{ $page->meta_title }}</p>
                            @endif
                        </td>
                        <td class="font-mono text-xs text-ink-soft">/pages/{{ $page->slug }}</td>
                        <td>
                            @if ($page->is_published)
                                <span class="badge bg-[#edf5ee] text-[#15803d]">প্রকাশিত</span>
                            @else
                                <span class="badge bg-[#f8efd9] text-[#8a5a12]">খসড়া</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap text-xs text-ink-muted">{{ $page->updated_at?->format('d M Y, h:i A') }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('pages.show', $page->slug) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm">দেখুন ↗</a>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn-secondary btn-sm">এডিট</a>
                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                                      onsubmit="return confirm('এই পেজ মুছে ফেলতে চান?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-brand-700 hover:bg-brand-50">মুছুন</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center">
                            <p class="text-sm font-semibold text-ink-soft">কোনো পেজ নেই</p>
                            <a href="{{ route('admin.pages.create') }}" class="btn-primary btn-sm mt-3">প্রথম পেজ তৈরি করুন</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $pages->links() }}</div>
@endsection
