@extends('layouts.admin')

@section('title', 'স্টাফ ম্যানেজমেন্ট')
@section('heading', 'স্টাফ')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">স্টাফ ম্যানেজমেন্ট</h2>
            <p class="mt-1 text-sm text-ink-soft">অ্যাডমিন, ম্যানেজার ও স্টাফ অ্যাকাউন্ট তৈরি ও পরিচালনা করুন। মোট {{ number_format($members->total()) }} জন।</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn-primary">+ নতুন স্টাফ</a>
    </div>

    <div class="table-wrap mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th>স্টাফ</th>
                    <th>যোগাযোগ</th>
                    <th>রোল</th>
                    <th>স্ট্যাটাস</th>
                    <th>শেষ লগইন</th>
                    <th>যোগদান</th>
                    <th class="text-right">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $member)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $member->avatarUrl() }}" alt="{{ $member->name }}" class="h-9 w-9 rounded-lg object-cover">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-ink">
                                        {{ $member->name }}
                                        @if ($member->is(auth()->user()))
                                            <span class="badge bg-sky-50 text-sky-700">আপনি</span>
                                        @endif
                                    </p>
                                    <p class="truncate text-xs text-ink-muted">@ {{ $member->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-xs">
                            <p class="break-all text-ink-soft">{{ $member->email }}</p>
                            <p class="text-ink-muted">{{ $member->phone }}</p>
                        </td>
                        <td>
                            <span class="badge {{ $member->role === \App\Enums\UserRole::Admin ? 'bg-brand-50 text-brand-700' : ($member->role === \App\Enums\UserRole::Manager ? 'bg-violet-50 text-violet-700' : 'bg-raised text-ink-soft') }}">
                                {{ $member->role->label() }}
                            </span>
                        </td>
                        <td>
                            @if ($member->is_active)
                                <span class="badge bg-[#edf5ee] text-[#15803d]">সক্রিয়</span>
                            @else
                                <span class="badge bg-brand-50 text-brand-700">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-xs text-ink-muted">{{ $member->last_login_at?->format('d M Y, h:i A') ?? '—' }}</td>
                        <td class="whitespace-nowrap text-xs text-ink-muted">{{ $member->created_at?->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.staff.edit', $member) }}" class="btn-secondary btn-sm">এডিট</a>
                                @unless ($member->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.staff.destroy', $member) }}"
                                          onsubmit="return confirm('এই স্টাফ অ্যাকাউন্ট মুছে ফেলতে চান?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost btn-sm text-brand-700 hover:bg-brand-50">মুছুন</button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-10 text-center text-sm text-ink-muted">কোনো স্টাফ নেই।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $members->links() }}</div>
@endsection
