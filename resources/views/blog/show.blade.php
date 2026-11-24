@extends('layouts.app')

@section('title', $post->meta_title ?: $post->title.' — '.\App\Models\Setting::get('site.name', 'One Blood Bank'))
@section('meta_description', $post->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->body), 155))

@section('content')
    <nav class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-brand-700">হোম</a>
        <span>/</span>
        <a href="{{ route('blog.index') }}" class="hover:text-brand-700">ব্লগ</a>
        @if ($post->category)
            <span>/</span>
            <a href="{{ route('blog.index', ['category' => $post->category]) }}" class="hover:text-brand-700">{{ $post->category }}</a>
        @endif
    </nav>

    <div class="mt-4 grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <article class="min-w-0">
            <header class="card overflow-hidden">
                @if ($post->coverUrl())
                    <div class="aspect-[16/8] bg-slate-100">
                        <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                    </div>
                @endif
                <div class="p-5 sm:p-8">
                    @if ($post->category)
                        <span class="badge bg-brand-50 text-brand-700">{{ $post->category }}</span>
                    @endif
                    <h1 class="mt-3 text-2xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-4xl">{{ $post->title }}</h1>

                    <div class="mt-4 flex flex-wrap items-center gap-3 border-b border-slate-100 pb-5 text-xs text-slate-500">
                        <span class="flex items-center gap-2">
                            @if ($post->author)
                                <img src="{{ $post->author->avatarUrl() }}" alt="" class="h-7 w-7 rounded-full object-cover">
                                <span class="font-semibold text-slate-700">{{ $post->author->name }}</span>
                            @else
                                <span class="font-semibold text-slate-700">{{ \App\Models\Setting::get('site.name', 'One Blood Bank') }}</span>
                            @endif
                        </span>
                        <span>·</span>
                        <span>{{ ($post->published_at ?? $post->created_at)?->format('d M Y') }}</span>
                        <span>·</span>
                        <span>{{ $post->readingMinutes() }} মিনিট পড়া</span>
                        <span>·</span>
                        <span>{{ number_format($post->views) }} বার পড়া হয়েছে</span>
                    </div>

                    <div class="article-body mt-6 text-[15px] leading-8 text-slate-700">
                        {!! nl2br(e($post->body)) !!}
                    </div>
                </div>
            </header>

            <div class="card mt-6 flex flex-wrap items-center justify-between gap-3 p-5">
                <p class="text-sm font-semibold text-slate-800">এই লেখাটি কাজে লাগলে শেয়ার করুন</p>
                <div class="flex flex-wrap gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($post->url()) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm">Facebook</a>
                    <a href="https://wa.me/?text={{ urlencode($post->title.' — '.$post->url()) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm">WhatsApp</a>
                    <a href="{{ route('donor.apply') }}" class="btn-primary btn-sm">ডোনার হোন</a>
                </div>
            </div>
        </article>

        <aside class="space-y-6">
            <div class="card p-5">
                <h2 class="text-base font-bold text-slate-900">আরও পড়ুন</h2>
                <div class="mt-4 space-y-4">
                    @forelse ($related as $item)
                        <a href="{{ $item->url() }}" class="block group">
                            <p class="text-sm font-semibold leading-snug text-slate-800 group-hover:text-brand-700">{{ $item->title }}</p>
                            <p class="mt-1 text-[11px] text-slate-400">{{ ($item->published_at ?? $item->created_at)?->format('d M Y') }} · {{ $item->readingMinutes() }} মিনিট</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">আরও পোস্ট শীঘ্রই আসছে।</p>
                    @endforelse
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-brand-700 to-rose-600 p-5 text-white">
                <p class="text-lg font-extrabold">রক্ত দিন, জীবন বাঁচান</p>
                <p class="mt-2 text-sm leading-relaxed text-white/85">আপনার এক ব্যাগ রক্ত একজন রোগীর জীবন বাঁচাতে পারে। আজই ডোনার হিসেবে যোগ দিন।</p>
                <a href="{{ route('donor.apply') }}" class="mt-4 inline-flex rounded-xl bg-white px-4 py-2 text-sm font-bold text-brand-700 hover:bg-brand-50">ডোনার আবেদন</a>
            </div>

            <x-ad placement="sidebar" />
        </aside>
    </div>
@endsection
