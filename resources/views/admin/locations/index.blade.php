@extends('layouts.admin')

@section('title', 'লোকেশন ইমপোর্ট')
@section('heading', 'লোকেশন ইমপোর্ট')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">দেশ অনুযায়ী বিভাগ, জেলা, উপজেলা ও ইউনিয়ন</h2>
            <p class="mt-1 max-w-3xl text-sm leading-relaxed text-ink-soft">
                বাংলাদেশ ছাড়া অন্য দেশের জন্যও নিজের লোকেশন হায়ারার্কি ইমপোর্ট করুন। CSV ফাইলে অবশ্যই
                <code class="rounded bg-raised px-1.5 py-0.5 text-xs">level</code> এবং
                <code class="rounded bg-raised px-1.5 py-0.5 text-xs">name</code> কলাম থাকতে হবে।
                জেলা, উপজেলা ও ইউনিয়নের জন্য <code class="rounded bg-raised px-1.5 py-0.5 text-xs">parent</code> কলামে অভিভাবকের নাম বা কোড দিন।
            </p>
        </div>
        <a href="{{ route('admin.locations.sample') }}" class="btn-secondary">নমুনা CSV ডাউনলোড</a>
    </div>

    @if (session('success'))
        <div class="alert-success mt-5">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert-danger mt-5">{{ session('error') }}</div>
    @endif

    @if (session('import_output'))
        <pre class="mt-4 overflow-x-auto rounded-[14px] border border-line bg-ink p-4 text-xs text-paper/80">{{ session('import_output') }}</pre>
    @endif

    <div class="mt-5 grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,.85fr)]">
        <div class="card p-5 sm:p-6">
            <h3 class="text-base font-semibold text-ink">CSV আপলোড করুন</h3>

            <form method="POST" action="{{ route('admin.locations.import') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf

                <div class="grid gap-4 sm:grid-cols-[10rem_minmax(0,1fr)]">
                    <div>
                        <label class="label" for="country">দেশের কোড</label>
                        <input type="text" name="country" id="country" class="input uppercase" maxlength="2"
                               placeholder="BD" value="{{ old('country', 'BD') }}" required>
                        <p class="help">ISO দুই অক্ষরের কোড।</p>
                        @error('country') <p class="mt-1 text-xs font-medium text-brand-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="file">CSV ফাইল</label>
                        <input type="file" name="file" id="file" class="input" accept=".csv,text/csv" required>
                        @error('file') <p class="mt-1 text-xs font-medium text-brand-700">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="rounded-xl border border-line bg-raised/60 p-3 text-xs leading-relaxed text-ink-soft">
                    <p class="font-semibold text-ink-soft">কলামের বিবরণ</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        <li><code>level</code> — division, district, upazila অথবা union</li>
                        <li><code>name</code> — ইংরেজি নাম; <code>name_bn</code> — স্থানীয়/বাংলা নাম (ঐচ্ছিক)</li>
                        <li><code>parent</code> — অভিভাবকের নাম বা কোড (বিভাগের জন্য খালি)</li>
                        <li><code>code</code> — ঐচ্ছিক সরকারি কোড; পরে parent হিসেবে ব্যবহার করা যায়</li>
                        <li><code>latitude</code>, <code>longitude</code> — ঐচ্ছিক</li>
                    </ul>
                </div>

                <button type="submit" class="btn-primary">ইমপোর্ট শুরু করুন</button>
            </form>
        </div>

        <div class="space-y-6">
            <div class="card p-5 sm:p-6">
                <h3 class="text-base font-semibold text-ink">বর্তমান ডাটা</h3>
                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-xl bg-raised/60 p-3">
                        <dt class="text-xs font-medium text-ink-muted">বিভাগ</dt>
                        <dd class="mt-1 text-xl font-semibold text-ink">{{ number_format($counts['division']) }}</dd>
                    </div>
                    <div class="rounded-xl bg-raised/60 p-3">
                        <dt class="text-xs font-medium text-ink-muted">জেলা</dt>
                        <dd class="mt-1 text-xl font-semibold text-ink">{{ number_format($counts['district']) }}</dd>
                    </div>
                    <div class="rounded-xl bg-raised/60 p-3">
                        <dt class="text-xs font-medium text-ink-muted">উপজেলা</dt>
                        <dd class="mt-1 text-xl font-semibold text-ink">{{ number_format($counts['upazila']) }}</dd>
                    </div>
                    <div class="rounded-xl bg-raised/60 p-3">
                        <dt class="text-xs font-medium text-ink-muted">ইউনিয়ন</dt>
                        <dd class="mt-1 text-xl font-semibold text-ink">{{ number_format($counts['union']) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card p-5 sm:p-6">
                <h3 class="text-base font-semibold text-ink">ইমপোর্ট করা দেশ</h3>
                @if ($countries->isEmpty())
                    <p class="mt-3 text-sm text-ink-soft">এখনও কোনো লোকেশন ইমপোর্ট করা হয়নি।</p>
                @else
                    <div class="mt-4 space-y-2">
                        @foreach ($countries as $country)
                            <div class="flex items-center justify-between rounded-xl border border-line px-3 py-2 text-sm">
                                <span class="font-semibold text-ink">{{ $country->country_code }}</span>
                                <span class="text-ink-muted">{{ number_format($country->divisions) }} বিভাগ</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                <p class="mt-4 text-xs leading-relaxed text-ink-muted">
                    সাইটে কোন দেশের লোকেশন দেখাবে তা নির্ধারণ করুন
                    <a href="{{ route('admin.settings.edit', ['tab' => 'others']) }}" class="font-semibold text-brand-700 hover:underline">সেটিংস → অন্যান্য → লোকেশন দেশের কোড</a>
                    থেকে।
                </p>
            </div>
        </div>
    </div>
@endsection
