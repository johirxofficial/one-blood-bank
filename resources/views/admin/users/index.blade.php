@extends('layouts.admin')

@section('title', 'ইউজার ম্যানেজমেন্ট')
@section('heading', 'ইউজার')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">ইউজার ম্যানেজমেন্ট</h2>
            <p class="mt-1 text-sm text-slate-600">মোট {{ number_format($users->total()) }} জন রেজিস্টার্ড ইউজার।</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="card mt-5 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-52 flex-1">
            <label class="label" for="q">নাম / ইউজারনেম / ইমেইল / মোবাইল</label>
            <input type="text" name="q" id="q" value="{{ $filters['q'] ?? '' }}" class="input" placeholder="যেমন: রাকিব / donor@example.com">
        </div>
        <div class="min-w-40">
            <label class="label" for="role">রোল</label>
            <select name="role" id="role" class="input">
                <option value="">সব রোল</option>
                @foreach (\App\Enums\UserRole::cases() as $role)
                    <option value="{{ $role->value }}" @selected(($filters['role'] ?? null) === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">ফিল্টার করুন</button>
        <a href="{{ route('admin.users.index') }}" class="btn-ghost">রিসেট</a>
    </form>

    <div class="table-wrap mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th>ইউজার</th>
                    <th>যোগাযোগ</th>
                    <th>রোল</th>
                    <th>ডোনার</th>
                    <th>ভেরিফিকেশন</th>
                    <th>স্ট্যাটাস</th>
                    <th>যোগদান</th>
                    <th class="text-right">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-9 w-9 rounded-lg object-cover">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900">{{ $user->name }}</p>
                                    <p class="truncate text-xs text-slate-500">@ {{ $user->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-xs">
                            <p class="break-all text-slate-700">{{ $user->email }}</p>
                            <p class="text-slate-500">{{ $user->phone }}</p>
                        </td>
                        <td>
                            <span class="badge {{ $user->isStaff() ? 'bg-violet-50 text-violet-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="text-xs">
                            @if ($user->donor)
                                <span class="badge {{ $user->donor->status->badgeClasses() }}">{{ $user->donor->status->label() }}</span>
                            @else
                                <span class="text-slate-400">নন-ডোনার</span>
                            @endif
                        </td>
                        <td class="text-xs">
                            <p class="{{ $user->phone_verified_at ? 'text-emerald-600' : 'text-rose-500' }}">মোবাইল {{ $user->phone_verified_at ? '✓' : '✗' }}</p>
                            <p class="{{ $user->email_verified_at ? 'text-emerald-600' : 'text-rose-500' }}">ইমেইল {{ $user->email_verified_at ? '✓' : '✗' }}</p>
                        </td>
                        <td>
                            @if ($user->is_active)
                                <span class="badge bg-emerald-50 text-emerald-700">সক্রিয়</span>
                            @else
                                <span class="badge bg-rose-50 text-rose-700">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap text-xs text-slate-500">{{ $user->created_at?->format('d M Y') }}</td>
                        <td>
                            <div class="flex justify-end">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary btn-sm">এডিট</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-10 text-center text-sm text-slate-500">কোনো ইউজার পাওয়া যায়নি।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
