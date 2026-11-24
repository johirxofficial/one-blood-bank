@extends('layouts.admin')

@section('title', 'এডিট পোস্ট: '.$post->title)
@section('heading', 'পোস্ট এডিট')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">পোস্ট এডিট</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $post->title }}</p>
            </div>
            <div class="flex gap-2">
                @if ($post->is_published)
                    <a href="{{ $post->url() }}" target="_blank" rel="noopener" class="btn-secondary">দেখুন ↗</a>
                @endif
                <a href="{{ route('admin.blog.index') }}" class="btn-secondary">← সব পোস্ট</a>
            </div>
        </div>

        @include('admin.blog.form', ['post' => $post])
    </div>
@endsection
