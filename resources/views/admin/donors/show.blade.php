@extends('layouts.admin')

@section('title', 'ডোনার: '.$donor->user->name)
@section('heading', 'ডোনার প্রোফাইল')

@section('content')
    <nav class="flex flex-wrap items-center gap-1.5 text-xs text-ink-muted">
        <a href="{{ route('admin.donors.index') }}" class="hover:text-brand-700">ডোনার</a>
        <span>/</span>
        <span class="text-ink-soft">{{ $donor->user->name }}</span>
    </nav>

    <div class="mt-4 grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-6">
            {{-- Header --}}
            <div class="card overflow-hidden">
                <div class="h-16 border-b border-line bg-raised"></div>
                <div class="px-5 pb-5 sm:px-6">
                    <div class="-mt-9 flex flex-wrap items-end justify-between gap-4">
                        <div class="flex items-end gap-4">
                            <img src="{{ $donor->user->avatarUrl() }}" alt="{{ $donor->user->name }}"
                                 class="h-20 w-20 rounded-[14px] border-4 border-surface object-cover">
                            <div class="pb-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-xl font-semibold text-ink">{{ $donor->user->name }}</h2>
                                    <span class="badge {{ $donor->status->badgeClasses() }}">{{ $donor->status->label() }}</span>
                                </div>
                                <p class="text-sm text-ink-muted">@ {{ $donor->user->username }} · আইডি #{{ $donor->id }}</p>
                            </div>
                        </div>
                        <x-blood-badge :group="$donor->blood_group" size="lg" />
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-xl bg-raised/60 p-3 text-center">
                            <p class="text-lg font-semibold text-ink">{{ number_format((float) $donor->rating_avg, 1) }}</p>
                            <p class="text-[11px] text-ink-muted">রেটিং ({{ $donor->rating_count }})</p>
                        </div>
                        <div class="rounded-xl bg-raised/60 p-3 text-center">
                            <p class="text-lg font-semibold text-ink">{{ $donor->total_donations }}</p>
                            <p class="text-[11px] text-ink-muted">মোট রক্তদান</p>
                        </div>
                        <div class="rounded-xl bg-raised/60 p-3 text-center">
                            <p class="text-lg font-semibold text-ink">{{ $donor->last_donation_date?->format('d M Y') ?? '—' }}</p>
                            <p class="text-[11px] text-ink-muted">সর্বশেষ রক্তদান</p>
                        </div>
                        <div class="rounded-xl bg-raised/60 p-3 text-center">
                            <p class="text-lg font-semibold {{ $donor->is_available ? 'text-[#15803d]' : 'text-brand-700' }}">
                                {{ $donor->is_available ? 'উপলব্ধ' : 'অনুপলব্ধ' }}
                            </p>
                            <p class="text-[11px] text-ink-muted">{{ $donor->is_available ? 'এখন রক্ত দিতে পারবেন' : ($donor->available_from?->format('d M Y') ?? 'তারিখ নেই') }}</p>
                        </div>
                    </div>

                    @if ($donor->rejection_reason)
                        <div class="mt-4 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
                            <span class="font-semibold">বাতিলের কারণ:</span> {{ $donor->rejection_reason }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Donor details --}}
            <div class="card p-5 sm:p-6">
                <h3 class="text-base font-semibold text-ink">ডোনারের তথ্য</h3>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">রক্তের গ্রুপ</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-brand-700">{{ $donor->blood_group }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">জন্ম তারিখ</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">লিঙ্গ</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->gender ? ucfirst($donor->gender) : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">ওজন</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->weight_kg ? $donor->weight_kg.' কেজি' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">দেশ</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->country ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">রাজ্য / প্রদেশ</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->state ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">শহর / জেলা</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->city ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">বিভাগ</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->division?->localizedName() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">জেলা</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->district?->localizedName() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">উপজেলা</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->upazila?->localizedName() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">ইউনিয়ন</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->union?->localizedName() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">গ্রাম / এলাকা</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->village ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <dt class="text-[11px] font-medium text-ink-muted">বিস্তারিত ঠিকানা</dt>
                        <dd class="mt-0.5 text-sm leading-relaxed text-ink">{{ $donor->address_details ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Account details --}}
            <div class="card p-5 sm:p-6">
                <h3 class="text-base font-semibold text-ink">অ্যাকাউন্ট তথ্য</h3>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">ইমেইল</dt>
                        <dd class="mt-0.5 break-all text-sm text-ink">{{ $donor->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">মোবাইল (প্রধান)</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->user->phone }}</dd>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <dt class="text-[11px] font-medium text-ink-muted">অতিরিক্ত যোগাযোগ নম্বর</dt>
                        <dd class="mt-1 flex flex-wrap gap-2">
                            @forelse ($donor->contacts as $contact)
                                <span class="badge bg-raised text-ink-soft">
                                    {{ $contact->displayLabel() }}: {{ $contact->phone }}
                                </span>
                            @empty
                                <span class="text-sm text-ink-muted">কোনো অতিরিক্ত নম্বর নেই।</span>
                            @endforelse
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">রোল</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->user->role->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">মোবাইল ভেরিফাইড</dt>
                        <dd class="mt-0.5 text-sm {{ $donor->user->phone_verified_at ? 'text-[#15803d]' : 'text-brand-700' }}">
                            {{ $donor->user->phone_verified_at ? '✓ হ্যাঁ' : '✗ না' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">ইমেইল ভেরিফাইড</dt>
                        <dd class="mt-0.5 text-sm {{ $donor->user->email_verified_at ? 'text-[#15803d]' : 'text-brand-700' }}">
                            {{ $donor->user->email_verified_at ? '✓ হ্যাঁ' : '✗ না' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium text-ink-muted">যোগদান</dt>
                        <dd class="mt-0.5 text-sm text-ink">{{ $donor->user->created_at?->format('d M Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Donation history --}}
            <div class="card p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-ink">রক্তদানের ইতিহাস</h3>
                    <span class="badge bg-raised text-ink-soft">{{ $donor->donations->count() }} টি</span>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>তারিখ</th>
                                <th>হাসপাতাল</th>
                                <th>নোট</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($donor->donations as $donation)
                                <tr>
                                    <td class="whitespace-nowrap font-medium text-ink">{{ $donation->donated_at->format('d M Y') }}</td>
                                    <td>{{ $donation->hospital ?: '—' }}</td>
                                    <td class="text-xs text-ink-muted">{{ $donation->note ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-center text-sm text-ink-muted">কোনো রক্তদানের তথ্য নেই।</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Reviews --}}
            <div class="card p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-ink">রিভিউ</h3>
                    <x-star-rating :rating="$donor->rating_avg" :count="$donor->rating_count" />
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($donor->ratings as $rating)
                        <div class="rounded-xl border border-line bg-raised/60 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-ink">{{ $rating->user?->name ?? 'ইউজার' }}</p>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-brand-700">{{ str_repeat('★', (int) $rating->rating) }}</span>
                                    <span class="badge {{ $rating->status === \App\Enums\ReviewStatus::Published ? 'bg-[#edf5ee] text-[#15803d]' : ($rating->status === \App\Enums\ReviewStatus::Pending ? 'bg-[#f8efd9] text-[#8a5a12]' : 'bg-raised text-ink-soft') }}">
                                        {{ $rating->status->label() }}
                                    </span>
                                </div>
                            </div>
                            @if ($rating->comment)
                                <p class="mt-1.5 text-sm leading-relaxed text-ink-soft">{{ $rating->comment }}</p>
                            @endif
                            <p class="mt-1.5 text-[11px] text-ink-muted">{{ $rating->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="rounded-xl bg-raised/60 p-4 text-sm text-ink-muted">এখনো কোনো রিভিউ নেই।</p>
                    @endforelse
                </div>
            </div>

            {{-- Phone reveals --}}
            <div class="card p-5 sm:p-6">
                <h3 class="text-base font-semibold text-ink">সাম্প্রতিক নম্বর দেখার লগ</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ইউজার</th>
                                <th>আইপি</th>
                                <th>সময়</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($donor->phoneReveals as $reveal)
                                <tr>
                                    <td class="font-medium text-ink">{{ $reveal->user?->name ?? 'ইউজার' }}</td>
                                    <td class="text-xs text-ink-muted">{{ $reveal->ip ?: '—' }}</td>
                                    <td class="text-xs text-ink-muted">{{ $reveal->created_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-center text-sm text-ink-muted">কেউ এখনো নম্বর দেখেনি।</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar actions --}}
        <aside class="space-y-6">
            <div class="card p-5">
                <h3 class="text-base font-semibold text-ink">অ্যাকশন</h3>
                <div class="mt-2 space-y-1 text-xs text-ink-muted">
                    <p>স্ট্যাটাস: <span class="font-semibold text-ink-soft">{{ $donor->status->label() }}</span></p>
                    <p>যাচাই করেছেন: <span class="font-semibold text-ink-soft">{{ $donor->verifiedBy?->name ?? '—' }}</span></p>
                    <p>যাচাইয়ের সময়: <span class="font-semibold text-ink-soft">{{ $donor->verified_at?->format('d M Y, h:i A') ?? '—' }}</span></p>
                </div>

                @if ($donor->status !== \App\Enums\DonorStatus::Approved)
                    <form method="POST" action="{{ route('admin.donors.approve', $donor) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="btn-primary w-full">✓ অনুমোদন করুন</button>
                    </form>
                @endif

                @if ($donor->isApproved())
                    <form method="POST" action="{{ route('admin.donors.suspend', $donor) }}" class="mt-2" onsubmit="return confirm('ডোনারকে স্থগিত করতে চান?')">
                        @csrf
                        <button type="submit" class="btn-secondary w-full">স্থগিত করুন</button>
                    </form>
                @endif

                <div class="mt-4 border-t border-line pt-4">
                    <p class="text-sm font-semibold text-ink">আবেদন বাতিল</p>
                    <form method="POST" action="{{ route('admin.donors.reject', $donor) }}" class="mt-2 space-y-2">
                        @csrf
                        <textarea name="reason" rows="3" class="input" required maxlength="500"
                                  placeholder="বাতিলের কারণ লিখুন — ইউজারকে ইমেইলে জানানো হবে">{{ old('reason', $donor->rejection_reason) }}</textarea>
                        <button type="submit" class="btn-danger w-full">কারণসহ বাতিল করুন</button>
                    </form>
                </div>

                <div class="mt-4 border-t border-line pt-4">
                    <form method="POST" action="{{ route('admin.donors.destroy', $donor) }}"
                          onsubmit="return confirm('এই ডোনার প্রোফাইল স্থায়ীভাবে মুছে ফেলতে চান?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost w-full text-brand-700 hover:bg-brand-50">স্থায়ীভাবে মুছুন</button>
                    </form>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="text-base font-semibold text-ink">দ্রুত তথ্য</h3>
                <ul class="mt-3 space-y-2 text-sm text-ink-soft">
                    <li class="flex items-center justify-between gap-2">
                        <span>পাবলিক প্রোফাইল</span>
                        <span class="font-semibold {{ $donor->is_public ? 'text-[#15803d]' : 'text-brand-700' }}">{{ $donor->is_public ? 'চালু' : 'বন্ধ' }}</span>
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <span>রেটিং</span>
                        <span class="font-semibold text-ink">{{ number_format((float) $donor->rating_avg, 1) }} / ৫</span>
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <span>রিভিউ সংখ্যা</span>
                        <span class="font-semibold text-ink">{{ $donor->rating_count }}</span>
                    </li>
                    <li class="flex items-center justify-between gap-2">
                        <span>নম্বর দেখা হয়েছে</span>
                        <span class="font-semibold text-ink">{{ $donor->phoneReveals->count() }} বার</span>
                    </li>
                </ul>
                @if ($donor->isApproved())
                    <a href="{{ $donor->user->profileUrl() }}" target="_blank" rel="noopener" class="btn-secondary mt-4 w-full">পাবলিক প্রোফাইল দেখুন ↗</a>
                @endif
            </div>
        </aside>
    </div>
@endsection
