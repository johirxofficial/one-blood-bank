@extends('layouts.app')

@section('title', 'ব্লগ — '.\App\Models\Setting::get('site.name', 'One Blood Bank'))
@section('meta_description', 'রক্তদান, ডোনার স্বাস্থ্য ও জীবন বাঁচানোর গল্প নিয়ে '.(\App\Models\Setting::get('site.name', 'One Blood Bank')).' ব্লগ।')

@section('content')
    <section class="rounded-[16px] border border-line bg-raised px-6 py-10 sm:px-10 sm:py-12">
        <p class="eyebrow">ব্লগ</p>
        <h1 class="display-title mt-4 max-w-2xl text-3xl sm:text-4xl">রক্তদান নিয়ে জানুন, শিখুন ও অনুপ্রাণিত হোন</h1>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-ink-soft sm:text-base">
            ডোনার হওয়ার নিয়ম, রক্তদানের উপকারিতা, স্বাস্থ্য টিপস এবং বাস্তব জীবনের গল্প — সব এক জায়গায়।
        </p>
    </section>

    @if ($categories->isNotEmpty())
        <div class="mt-6 flex flex-wrap items-center gap-2">
            <a href="{{ route('blog.index') }}" class="badge {{ $category === '' ? 'bg-ink text-paper' : 'border border-line bg-surface text-ink-soft hover:bg-raised' }}">সব পোস্ট</a>
            @foreach ($categories as $item)
                <a href="{{ route('blog.index', ['category' => $item]) }}" class="badge {{ $category === $item ? 'bg-ink text-paper' : 'border border-line bg-surface text-ink-soft hover:bg-raised' }}">{{ $item }}</a>
            @endforeach
        </div>
    @endif

    @if ($posts->isEmpty())
        <div class="card mt-8 p-10 text-center">
            <svg class="mx-auto h-8 w-8 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path d="M5 4.5h11.5a2 2 0 0 1 2 2V20H7a2 2 0 0 1-2-2V4.5Z" stroke-linejoin="round"/>
                <path d="M5 17.5h13.5M8.5 8h6M8.5 11.5h6" stroke-linecap="round"/>
            </svg>
            <h2 class="mt-3 text-lg font-medium text-ink">এখনো কোনো পোস্ট প্রকাশিত হয়নি</h2>
            <p class="mt-1 text-sm text-ink-soft">শীঘ্রই নতুন লেখা আসছে।</p>
            <a href="{{ route('home') }}" class="btn-secondary mt-4">হোমপেজে ফিরুন</a>
        </div>
    @else
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <article class="card card-hover flex flex-col overflow-hidden">
                    <a href="{{ $post->url() }}" class="block aspect-[16/9] bg-raised">
                        @if ($post->coverUrl())
                            <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full flex-col justify-between bg-raised p-5">
                                <span class="text-[11px] font-medium text-ink-muted">One Blood Bank</span>
                                <div>
                                    <p class="font-display text-2xl leading-tight text-ink">{{ $post->category ?: 'ব্লগ' }}</p>
                                    <span class="mt-3 block h-px w-8 bg-brand-700"></span>
                                </div>
                            </div>
                        @endif
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex flex-wrap items-center gap-2 text-[11px] text-ink-muted">
                            @if ($post->category && $post->coverUrl())
                                <span class="badge bg-brand-50 text-brand-700">{{ $post->category }}</span>
                            @endif
                            <span>{{ ($post->published_at ?? $post->created_at)?->format('d M Y') }}</span>
                            <span>·</span>
                            <span>{{ $post->readingMinutes() }} মিনিট পড়া</span>
                        </div>
                        <h2 class="mt-3 text-lg font-medium leading-snug text-ink">
                            <a href="{{ $post->url() }}" class="hover:text-brand-700">{{ $post->title }}</a>
                        </h2>
                        @if ($post->excerpt)
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-ink-soft">{{ $post->excerpt }}</p>
                        @endif
                        <a href="{{ $post->url() }}" class="mt-auto pt-4 text-sm font-medium text-brand-700 hover:underline">বিস্তারিত পড়ুন →</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $posts->links() }}</div>
    @endif
@endsection
