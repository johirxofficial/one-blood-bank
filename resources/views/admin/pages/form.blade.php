@extends('layouts.admin')

@php
    $isEdit = $page->exists;
@endphp

@section('title', $isEdit ? 'পেজ এডিট' : 'নতুন পেজ')
@section('heading', $isEdit ? 'পেজ এডিট' : 'নতুন পেজ')

@section('content')
    <nav class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
        <a href="{{ route('admin.pages.index') }}" class="hover:text-brand-700">স্ট্যাটিক পেজ</a>
        <span>/</span>
        <span class="text-slate-700">{{ $isEdit ? $page->title : 'নতুন' }}</span>
    </nav>

    <form method="POST" action="{{ $isEdit ? route('admin.pages.update', $page) : route('admin.pages.store') }}"
          class="card mt-4 max-w-4xl p-5 sm:p-6">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="title">শিরোনাম <span class="text-brand-600">*</span></label>
                <input type="text" name="title" id="title" class="input" value="{{ old('title', $page->title) }}" required maxlength="160">
            </div>
            <div>
                <label class="label" for="slug">স্লাগ <span class="text-brand-600">*</span></label>
                <input type="text" name="slug" id="slug" class="input font-mono" value="{{ old('slug', $page->slug) }}" required maxlength="100"
                       pattern="[a-zA-Z0-9\-_]+" placeholder="about">
                <p class="help">URL হবে: /pages/<span id="slug-preview">{{ $page->slug ?: 'about' }}</span></p>
            </div>

            <div class="sm:col-span-2">
                <label class="label" for="content">কনটেন্ট (HTML)</label>
                <textarea name="content" id="content" rows="16" class="input font-mono text-xs" spellcheck="false" required>{{ old('content', $page->content) }}</textarea>
                <p class="help">HTML ট্যাগ ব্যবহার করতে পারবেন — যেমন &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;a&gt;।</p>
            </div>

            <div class="sm:col-span-2">
                <label class="label" for="meta_title">SEO টাইটেল</label>
                <input type="text" name="meta_title" id="meta_title" class="input" value="{{ old('meta_title', $page->meta_title) }}" maxlength="180">
            </div>

            <div class="sm:col-span-2">
                <label class="label" for="meta_description">SEO ডেসক্রিপশন</label>
                <textarea name="meta_description" id="meta_description" rows="2" class="input" maxlength="300">{{ old('meta_description', $page->meta_description) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                           @checked((string) old('is_published', $page->is_published ? '1' : '0') === '1')>
                    <span>
                        <span class="block text-sm font-medium text-slate-800">পেজ প্রকাশ করুন</span>
                        <span class="block text-xs text-slate-500">আনচেক থাকলে পেজটি পাবলিকভাবে দেখা যাবে না।</span>
                    </span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-4">
            <a href="{{ route('admin.pages.index') }}" class="btn-ghost">বাতিল</a>
            <button type="submit" class="btn-primary">{{ $isEdit ? 'পরিবর্তন সংরক্ষণ করুন' : 'পেজ তৈরি করুন' }}</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const slug = document.getElementById('slug');
            const preview = document.getElementById('slug-preview');
            if (!slug || !preview) return;
            slug.addEventListener('input', () => {
                preview.textContent = slug.value || 'about';
            });
        })();
    </script>
@endpush
