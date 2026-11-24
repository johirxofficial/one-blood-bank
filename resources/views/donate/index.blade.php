@extends('layouts.app')

@section('title', 'সাইটে সহযোগিতা করুন — '.\App\Models\Setting::get('site.name', 'One Blood Bank'))
@section('meta_description', 'সাইটের সার্ভার ও রক্ষণাবেক্ষণ খরচে স্বেচ্ছায় সহযোগিতা করুন। বিকাশ, নগদ, রকেট বা ব্যাংক ট্রান্সফার।')

@section('content')
    @php
        $progress = $goal > 0 ? min(100, round($raised / $goal * 100)) : 0;
    @endphp

    <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-600 to-rose-500 px-6 py-10 text-white sm:px-10 sm:py-14">
        <span class="badge bg-white/15 text-white">💝 স্বেচ্ছা সহযোগিতা</span>
        <h1 class="mt-4 max-w-3xl text-3xl font-extrabold tracking-tight sm:text-4xl">সাইট চালাতে আমাদের পাশে দাঁড়ান</h1>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/90 sm:text-base">
            One Blood Bank সম্পূর্ণ ফ্রি সেবা। সার্ভার, ডোমেইন ও রক্ষণাবেক্ষণ খরচ চালাতে আপনার ছোট সহযোগিতাও বড় ভূমিকা রাখে।
        </p>

        <div class="mt-6 grid max-w-2xl gap-3 sm:grid-cols-3">
            <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                <p class="text-2xl font-extrabold">{{ number_format($raised, 0) }} {{ $currency }}</p>
                <p class="text-xs text-white/75">মোট সংগৃহীত</p>
            </div>
            <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                <p class="text-2xl font-extrabold">{{ number_format($donorsCount) }}</p>
                <p class="text-xs text-white/75">জন সহযোগী</p>
            </div>
            <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                <p class="text-2xl font-extrabold">{{ $goal > 0 ? number_format($goal, 0).' '.$currency : 'উন্মুক্ত' }}</p>
                <p class="text-xs text-white/75">লক্ষ্য</p>
            </div>
        </div>

        @if ($showGoal && $goal > 0)
            <div class="mt-5 max-w-2xl">
                <div class="flex items-center justify-between text-xs text-white/80">
                    <span>অগ্রগতি</span>
                    <span>{{ $progress }}%</span>
                </div>
                <div class="mt-1.5 h-2.5 overflow-hidden rounded-full bg-white/20">
                    <div class="h-full rounded-full bg-white" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        @endif
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-6">
            <div class="card p-5 sm:p-6">
                <h2 class="text-lg font-bold text-slate-900">টাকা পাঠানোর মাধ্যম</h2>
                <p class="mt-1 text-sm text-slate-600">নিচের যেকোনো মাধ্যমে টাকা পাঠিয়ে ট্রানজেকশন আইডি দিয়ে ফর্মটি পূরণ করুন।</p>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @forelse ($methods as $method => $number)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-bold text-slate-900">{{ $methodLabels[$method] ?? ucfirst($method) }}</p>
                            @if ($method === 'bank')
                                <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-slate-600">{{ $bankDetails }}</p>
                            @else
                                <p class="mt-1 font-mono text-base font-bold text-brand-700" data-copy-value="{{ $number }}">{{ $number }}</p>
                                <button type="button" data-copy-target="{{ $number }}" class="mt-2 text-xs font-semibold text-brand-700 hover:underline">কপি করুন</button>
                            @endif
                        </div>
                    @empty
                        <p class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800">পেমেন্ট নম্বর এখনো সেট করা হয়নি। অ্যাডমিন সেটিংস থেকে যোগ করুন।</p>
                    @endforelse
                </div>

                @if ($instructions)
                    <div class="mt-4 rounded-xl border border-sky-100 bg-sky-50 p-4 text-sm leading-relaxed text-sky-900">
                        <p class="font-semibold">নির্দেশনা</p>
                        <p class="mt-1 whitespace-pre-line">{{ $instructions }}</p>
                    </div>
                @endif
            </div>

            <div class="card p-5 sm:p-6">
                <h2 class="text-lg font-bold text-slate-900">সহযোগিতার তথ্য জমা দিন</h2>
                <p class="mt-1 text-sm text-slate-600">অ্যাডমিন যাচাই করার পর আপনার নাম ধন্যবাদ তালিকায় যুক্ত হবে।</p>

                <form method="POST" action="{{ route('donate.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div>
                        <label class="label" for="name">আপনার নাম <span class="text-brand-600">*</span></label>
                        <input type="text" name="name" id="name" class="input" value="{{ old('name', $user?->name) }}" required maxlength="120">
                    </div>
                    <div>
                        <label class="label" for="phone">মোবাইল</label>
                        <input type="tel" name="phone" id="phone" class="input" value="{{ old('phone', $user?->phone) }}" maxlength="20" placeholder="+8801712345678">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="email">ইমেইল</label>
                        <input type="email" name="email" id="email" class="input" value="{{ old('email', $user?->email) }}" maxlength="180">
                    </div>
                    <div>
                        <label class="label" for="amount">পরিমাণ ({{ $currency }}) <span class="text-brand-600">*</span></label>
                        <input type="number" name="amount" id="amount" class="input" value="{{ old('amount', $minimum) }}" min="{{ $minimum }}" max="10000000" step="1" required>
                        <div class="mt-2 flex flex-wrap gap-1.5" data-amount-chips>
                            @foreach ([$minimum, $minimum * 2, $minimum * 5, $minimum * 10] as $amount)
                                <button type="button" data-amount="{{ $amount }}" class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-200">{{ number_format($amount) }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="label" for="method">মাধ্যম <span class="text-brand-600">*</span></label>
                        <select name="method" id="method" class="input" required>
                            @foreach ($methodLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('method') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="transaction_id">ট্রানজেকশন আইডি <span class="text-brand-600">*</span></label>
                        <input type="text" name="transaction_id" id="transaction_id" class="input" value="{{ old('transaction_id') }}" required maxlength="120" placeholder="যেমন: TRX12345678">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label" for="message">বার্তা (ঐচ্ছিক)</label>
                        <textarea name="message" id="message" rows="3" class="input" maxlength="1000" placeholder="শুভেচ্ছা বা মন্তব্য">{{ old('message') }}</textarea>
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <button type="submit" class="btn-primary">সহযোগিতা জমা দিন</button>
                    </div>
                </form>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="card p-5">
                <h2 class="text-base font-bold text-slate-900">ধন্যবাদ তালিকা</h2>
                <ul class="mt-4 space-y-3">
                    @forelse ($recentDonors as $donor)
                        <li class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $donor->name }}</p>
                                @if ($donor->message)
                                    <p class="mt-0.5 line-clamp-2 text-xs text-slate-500">{{ $donor->message }}</p>
                                @endif
                            </div>
                            <span class="badge bg-emerald-50 text-emerald-700">{{ number_format($donor->amount, 0) }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">এখনো কোনো যাচাইকৃত সহযোগিতা নেই। আপনিই প্রথম হতে পারেন!</li>
                    @endforelse
                </ul>
            </div>

            <div class="card p-5 text-sm leading-relaxed text-slate-600">
                <p class="font-semibold text-slate-800">আপনার তথ্য নিরাপদ</p>
                <p class="mt-1">আপনার ট্রানজেকশন আইডি শুধুমাত্র যাচাইয়ের জন্য ব্যবহৃত হবে। কোনো কার্ড বা পিন কখনো চাওয়া হবে না।</p>
            </div>

            <x-ad placement="sidebar" />
        </aside>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-amount]').forEach((button) => {
            button.addEventListener('click', () => {
                const amount = document.getElementById('amount');
                if (amount) amount.value = button.dataset.amount;
            });
        });

        document.querySelectorAll('[data-copy-target]').forEach((button) => {
            button.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(button.dataset.copyTarget);
                    const old = button.textContent;
                    button.textContent = 'কপি হয়েছে ✓';
                    setTimeout(() => (button.textContent = old), 1800);
                } catch (error) {
                    /* clipboard unavailable */
                }
            });
        });
    </script>
@endpush
