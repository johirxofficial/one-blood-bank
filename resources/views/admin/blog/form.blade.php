@php
    $editing = $post->exists;
    $action = $editing ? route('admin.blog.update', $post) : route('admin.blog.store');
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mt-5 space-y-5">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="card p-5 sm:p-6">
        <div class="space-y-4">
            <div>
                <label class="label" for="title">শিরোনাম <span class="text-brand-600">*</span></label>
                <input type="text" name="title" id="title" class="input" value="{{ old('title', $post->title) }}" required maxlength="180">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="slug">স্লাগ (URL)</label>
                    <input type="text" name="slug" id="slug" class="input font-mono text-xs" value="{{ old('slug', $post->slug) }}" maxlength="200" placeholder="খালি রাখলে শিরোনাম থেকে তৈরি হবে">
                </div>
                <div>
                    <label class="label" for="category">ক্যাটাগরি</label>
                    <input type="text" name="category" id="category" class="input" value="{{ old('category', $post->category) }}" maxlength="80" placeholder="যেমন: ডোনার টিপস">
                </div>
            </div>

            <div>
                <label class="label" for="excerpt">সংক্ষিপ্ত বর্ণনা</label>
                <textarea name="excerpt" id="excerpt" rows="2" class="input" maxlength="500">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            <div>
                <label class="label" for="body">মূল লেখা <span class="text-brand-600">*</span></label>
                <textarea name="body" id="body" rows="14" class="input font-mono text-sm" required>{{ old('body', $post->body) }}</textarea>
                <p class="help">সাধারণ টেক্সট লিখুন। লাইন ব্রেক সংরক্ষিত থাকবে।</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="cover">কভার ছবি</label>
                    @if ($post->coverUrl())
                        <img src="{{ $post->coverUrl() }}" alt="" class="mb-2 h-24 w-full max-w-xs rounded-xl object-cover ring-1 ring-slate-200">
                    @endif
                    <input type="file" name="cover" id="cover" accept="image/jpeg,image/png,image/webp" class="input py-2">
                    <p class="help">JPG, PNG, WebP — সর্বোচ্চ ৩ MB।</p>
                </div>
                <div>
                    <label class="label" for="published_at">প্রকাশের তারিখ</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="input"
                           value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                    <p class="help">খালি রাখলে প্রকাশের সময় বর্তমান সময় হবে।</p>
                </div>
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-4 py-3">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600" @checked(old('is_published', $post->is_published))>
                <span>
                    <span class="block text-sm font-medium text-slate-800">এখনই প্রকাশ করুন</span>
                    <span class="block text-xs text-slate-500">বন্ধ থাকলে পোস্টটি খসড়া হিসেবে সংরক্ষিত হবে।</span>
                </span>
            </label>
        </div>
    </div>

    <div class="card p-5 sm:p-6">
        <h3 class="text-base font-bold text-slate-900">SEO</h3>
        <div class="mt-4 space-y-4">
            <div>
                <label class="label" for="meta_title">মেটা টাইটেল</label>
                <input type="text" name="meta_title" id="meta_title" class="input" value="{{ old('meta_title', $post->meta_title) }}" maxlength="200">
            </div>
            <div>
                <label class="label" for="meta_description">মেটা ডেসক্রিপশন</label>
                <textarea name="meta_description" id="meta_description" rows="2" class="input" maxlength="300">{{ old('meta_description', $post->meta_description) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-2">
        <a href="{{ route('admin.blog.index') }}" class="btn-secondary">বাতিল</a>
        <button type="submit" class="btn-primary">{{ $editing ? 'পোস্ট আপডেট করুন' : 'পোস্ট তৈরি করুন' }}</button>
    </div>
</form>
