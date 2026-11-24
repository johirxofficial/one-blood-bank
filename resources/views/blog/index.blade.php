@extends('layouts.app')

@section('title', 'ব্লগ — '.\App\Models\Setting::get('site.name', 'One Blood Bank'))
@section('meta_description', 'রক্তদান, ডোনার স্বাস্থ্য ও জীবন বাঁচানোর গল্প নিয়ে '.(\App\Models\Setting::get('site.name', 'One Blood Bank')).' ব্লগ।')

@section('content')
    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-600 to-rose-500 px-6 py-10 text-white sm:px-10 sm:py-14">
        <span class="badge bg-white/15 text-white">📝 ব্লগ</span>
        <h1 class="mt-4 max-w-2xl text-3xl font-extrabold tracking-tight sm:text-4xl">রক্তদান নিয়ে জানুন, শিখুন ও অনুপ্রাণিত হোন</h1>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/90 sm:text-base">
            ডোনার হওয়ার নিয়ম, রক্তদানের উপকারিতা, স্বাস্থ্য টিপস এবং বাস্তব জীবনের গল্প — সব এক জায়গায়।
        </p>
    </section>

    @if ($categories->isNotEmpty())
        <div class="mt-6 flex flex-wrap items-center gap-2">
            <a href="{{ route('blog.index') }}" class="badge {{ $category === '' ? 'bg-brand-600 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}">সব পোস্ট</a>
            @foreach ($categories as $item)
                <a href="{{ route('blog.index', ['category' => $item]) }}" class="badge {{ $category === $item ? 'bg-brand-600 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}">{{ $item }}</a>
            @endforeach
        </div>
    @endif

    @if ($posts->isEmpty())
        <div class="card mt-8 p-10 text-center">
            <p class="text-4xl">📰</p>
            <h2 class="mt-3 text-lg font-bold text-slate-900">এখনো কোনো পোস্ট প্রকাশিত হয়নি</h2>
            <p class="mt-1 text-sm text-slate-600">শীঘ্রই নতুন লেখা আসছে।</p>
            <a href="{{ route('home') }}" class="btn-secondary mt-4">হোমপেজে ফিরুন</a>
        </div>
    @else
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <article class="card card-hover flex flex-col overflow-hidden">
                    <a href="{{ $post->url() }}" class="block aspect-[16/9] bg-slate-100">
                        @if ($post->coverUrl())
                            <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="grid h-full place-items-center bg-gradient-to-br from-brand-50 to-rose-100 text-5xl">🩸</div>
                        @endif
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold text-slate-500">
                            @if ($post->category)
                                <span class="badge bg-brand-50 text-brand-700">{{ $post->category }}</span>
                            @endif
                            <span>{{ ($post->published_at ?? $post->created_at)?->format('d M Y') }}</span>
                            <span>·</span>
                            <span>{{ $post->readingMinutes() }} মিনিট পড়া</span>
                        </div>
                        <h2 class="mt-3 text-lg font-bold leading-snug text-slate-900">
                            <a href="{{ $post->url() }}" class="hover:text-brand-700">{{ $post->title }}</a>
                        </h2>
                        @if ($post->excerpt)
                            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $post->excerpt }}</p>
                        @endif
                        <a href="{{ $post->url() }}" class="mt-auto pt-4 text-sm font-semibold text-brand-700 hover:underline">বিস্তারিত পড়ুন →</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $posts->links() }}</div>
    @endif
@endsection
