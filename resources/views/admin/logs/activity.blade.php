@extends('layouts.admin')

@section('title', 'অ্যাক্টিভিটি লগ')
@section('heading', 'অ্যাক্টিভিটি লগ')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">অ্যাক্টিভিটি লগ</h2>
            <p class="mt-1 text-sm text-ink-soft">সিস্টেমের সব গুরুত্বপূর্ণ কার্যক্রমের রেকর্ড। মোট {{ number_format($logs->total()) }} টি।</p>
        </div>
        <a href="{{ route('admin.logs.mail') }}" class="btn-secondary">মেইল লগ দেখুন</a>
    </div>

    <div class="table-wrap mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th>সময়</th>
                    <th>ইউজার</th>
                    <th>অ্যাকশন</th>
                    <th>বিবরণ</th>
                    <th>আইপি</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap text-xs text-ink-muted">
                            <p class="font-medium text-ink-soft">{{ $log->created_at?->format('d M Y') }}</p>
                            <p>{{ $log->created_at?->format('h:i:s A') }}</p>
                        </td>
                        <td>
                            @if ($log->user)
                                <p class="text-sm font-semibold text-ink">{{ $log->user->name }}</p>
                                <p class="text-xs text-ink-muted">@ {{ $log->user->username }}</p>
                            @else
                                <span class="text-xs text-ink-muted">সিস্টেম</span>
                            @endif
                        </td>
                        <td><span class="badge bg-raised font-mono text-[11px] text-ink-soft">{{ $log->action }}</span></td>
                        <td class="max-w-md text-sm text-ink-soft">{{ $log->description ?: '—' }}</td>
                        <td class="whitespace-nowrap font-mono text-xs text-ink-muted">{{ $log->ip ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-sm text-ink-muted">কোনো অ্যাক্টিভিটি লগ নেই।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
