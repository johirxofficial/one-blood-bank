@extends('layouts.admin')

@section('title', 'মেইল লগ')
@section('heading', 'মেইল লগ')

@section('content')
    @php
        $statusStyles = [
            'sent' => 'bg-[#edf5ee] text-[#15803d]',
            'queued' => 'bg-[#f8efd9] text-[#8a5a12]',
            'failed' => 'bg-brand-50 text-brand-700',
        ];
    @endphp

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">মেইল লগ</h2>
            <p class="mt-1 text-sm text-ink-soft">সিস্টেম ও অ্যাডমিনের পাঠানো সব ইমেইলের রেকর্ড। মোট {{ number_format($logs->total()) }} টি।</p>
        </div>
        <a href="{{ route('admin.logs.activity') }}" class="btn-secondary">অ্যাক্টিভিটি লগ</a>
    </div>

    <div class="table-wrap mt-5">
        <table class="table">
            <thead>
                <tr>
                    <th>সময়</th>
                    <th>প্রাপক</th>
                    <th>বিষয়</th>
                    <th>টেমপ্লেট</th>
                    <th>স্ট্যাটাস</th>
                    <th>ত্রুটি</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap text-xs text-ink-muted">
                            <p class="font-medium text-ink-soft">{{ $log->created_at?->format('d M Y') }}</p>
                            <p>{{ $log->created_at?->format('h:i:s A') }}</p>
                        </td>
                        <td class="text-sm">
                            <p class="break-all font-medium text-ink">{{ $log->to_email }}</p>
                            @if ($log->user)
                                <p class="text-xs text-ink-muted">@ {{ $log->user->username }}</p>
                            @endif
                        </td>
                        <td class="max-w-xs text-sm text-ink-soft">{{ $log->subject }}</td>
                        <td class="text-xs text-ink-muted">{{ $log->template ?: '—' }}</td>
                        <td>
                            <span class="badge {{ $statusStyles[$log->status] ?? 'bg-raised text-ink-soft' }}">{{ $log->status }}</span>
                        </td>
                        <td class="max-w-xs text-xs text-brand-700">{{ $log->error ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-10 text-center text-sm text-ink-muted">এখনো কোনো মেইল পাঠানো হয়নি।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
