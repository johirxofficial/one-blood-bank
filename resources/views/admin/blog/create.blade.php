@extends('layouts.admin')

@section('title', 'নতুন ব্লগ পোস্ট')
@section('heading', 'নতুন ব্লগ পোস্ট')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">নতুন পোস্ট</h2>
                <p class="mt-1 text-sm text-slate-600">শিরোনাম, ক্যাটাগরি ও কনটেন্ট লিখে প্রকাশ করুন।</p>
            </div>
            <a href="{{ route('admin.blog.index') }}" class="btn-secondary">← সব পোস্ট</a>
        </div>

        @include('admin.blog.form', ['post' => $post])
    </div>
@endsection
