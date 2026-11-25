@extends('layouts.admin')

@php
    $isEdit = $ad->exists;
@endphp

@section('title', $isEdit ? 'বিজ্ঞাপন এডিট' : 'নতুন বিজ্ঞাপন')
@section('heading', $isEdit ? 'বিজ্ঞাপন এডিট' : 'নতুন বিজ্ঞাপন')

@section('content')
    <nav class="flex flex-wrap items-center gap-1.5 text-xs text-ink-muted">
        <a href="{{ route('admin.ads.index') }}" class="hover:text-brand-700">বিজ্ঞাপন</a>
        <span>/</span>
        <span class="text-ink-soft">{{ $isEdit ? $ad->title : 'নতুন' }}</span>
    </nav>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $isEdit ? route('admin.ads.update', $ad) : route('admin.ads.store') }}"
          class="card mt-4 max-w-4xl p-5 sm:p-6">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label" for="title">শিরোনাম <span class="text-brand-600">*</span></label>
                <input type="text" name="title" id="title" class="input" value="{{ old('title', $ad->title) }}" required maxlength="120">
            </div>

            <div>
                <label class="label" for="type">বিজ্ঞাপনের ধরন <span class="text-brand-600">*</span></label>
                <select name="type" id="type" class="input" required>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected(old('type', $ad->type?->value) === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
                <p class="help">ব্যানার = ছবি/লিংক, পপআন্ডার = পপআপ কোড, স্ক্রিপ্ট = কাস্টম HTML/JS।</p>
            </div>

            <div>
                <label class="label" for="placement">স্থান <span class="text-brand-600">*</span></label>
                <select name="placement" id="placement" class="input" required>
                    @foreach ($placements as $placement)
                        <option value="{{ $placement->value }}" @selected(old('placement', $ad->placement?->value) === $placement->value)>{{ $placement->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="label" for="target_url">টার্গেট লিংক</label>
                <input type="url" name="target_url" id="target_url" class="input" value="{{ old('target_url', $ad->target_url) }}"
                       placeholder="https://example.com" maxlength="255">
            </div>

            <div class="sm:col-span-2">
                <label class="label" for="image">ব্যানার ছবি</label>
                @if ($ad->image_path)
                    <img src="{{ asset('storage/'.$ad->image_path) }}" alt="{{ $ad->title }}" class="mb-2 h-24 rounded-xl ring-1 ring-line">
                @endif
                <input type="file" name="image" id="image" class="input file:mr-3 file:rounded-lg file:border-0 file:bg-raised file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-ink-soft"
                       accept=".jpg,.jpeg,.png,.webp,.gif">
                <p class="help">JPG, PNG, WebP বা GIF — সর্বোচ্চ ২ মেগাবাইট।</p>
            </div>

            <div class="sm:col-span-2">
                <label class="label" for="code">কোড (স্ক্রিপ্ট / পপআন্ডার / HTML)</label>
                <textarea name="code" id="code" rows="6" class="input font-mono text-xs" spellcheck="false"
                          placeholder="&lt;script&gt;...&lt;/script&gt;">{{ old('code', $ad->code) }}</textarea>
                <p class="help">শুধুমাত্র বিশ্বস্ত বিজ্ঞাপন নেটওয়ার্কের কোড ব্যবহার করুন।</p>
            </div>

            <div>
                <label class="label" for="starts_at">শুরুর সময়</label>
                <input type="datetime-local" name="starts_at" id="starts_at" class="input"
                       value="{{ old('starts_at', $ad->starts_at?->format('Y-m-d\TH:i')) }}">
            </div>

            <div>
                <label class="label" for="ends_at">শেষের সময়</label>
                <input type="datetime-local" name="ends_at" id="ends_at" class="input"
                       value="{{ old('ends_at', $ad->ends_at?->format('Y-m-d\TH:i')) }}">
            </div>

            <div>
                <label class="label" for="sort_order">ক্রম</label>
                <input type="number" name="sort_order" id="sort_order" class="input" min="0" max="999"
                       value="{{ old('sort_order', $ad->sort_order ?? 0) }}">
            </div>

            <div class="flex items-end">
                <label class="flex w-full cursor-pointer items-center gap-3 rounded-xl border border-line px-4 py-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-line text-brand-600 focus:ring-brand-500"
                           @checked((string) old('is_active', $ad->is_active ? '1' : '0') === '1')>
                    <span class="text-sm font-medium text-ink">বিজ্ঞাপন সক্রিয়</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-2 border-t border-line pt-4">
            <a href="{{ route('admin.ads.index') }}" class="btn-ghost">বাতিল</a>
            <button type="submit" class="btn-primary">{{ $isEdit ? 'পরিবর্তন সংরক্ষণ করুন' : 'বিজ্ঞাপন যোগ করুন' }}</button>
        </div>
    </form>
@endsection
