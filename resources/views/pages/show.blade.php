@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title.' — One Blood Bank')
@section('meta_description', $page->meta_description)

@section('content')
    <div class="mx-auto max-w-3xl">
        <nav class="flex items-center gap-1.5 text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-brand-700">হোম</a>
            <span>/</span>
            <span class="text-slate-700">{{ $page->title }}</span>
        </nav>

        <article class="card mt-4 p-6 sm:p-9">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $page->title }}</h1>
            <p class="mt-1 text-xs text-slate-400">সর্বশেষ আপডেট: {{ $page->updated_at->format('d M Y') }}</p>

            <div class="rich-text mt-6">
                {!! $page->content !!}
            </div>
        </article>
    </div>
@endsection
