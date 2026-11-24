@extends('layouts.admin')

@section('title', 'মেইল লগ')
@section('heading', 'মেইল লগ')

@section('content')
    @php
        $statusStyles = [
            'sent' => 'bg-emerald-50 text-emerald-700',
            'queued' => 'bg-amber-50 text-amber-700',
            'failed' => 'bg-rose-50 text-rose-700',
        ];
    @endphp

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">মেইল লগ</h2>
            <p class="mt-1 text-sm text-slate-600">সিস্টেম ও অ্যাডমিনের পাঠানো সব ইমেইলের রেকর্ড। মোট {{ number_format($logs->total()) }} টি।</p>
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
                        <td class="whitespace-nowrap text-xs text-slate-500">
                            <p class="font-medium text-slate-700">{{ $log->created_at?->format('d M Y') }}</p>
                            <p>{{ $log->created_at?->format('h:i:s A') }}</p>
                        </td>
                        <td class="text-sm">
                            <p class="break-all font-medium text-slate-800">{{ $log->to_email }}</p>
                            @if ($log->user)
                                <p class="text-xs text-slate-500">@ {{ $log->user->username }}</p>
                            @endif
                        </td>
                        <td class="max-w-xs text-sm text-slate-700">{{ $log->subject }}</td>
                        <td class="text-xs text-slate-500">{{ $log->template ?: '—' }}</td>
                        <td>
                            <span class="badge {{ $statusStyles[$log->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $log->status }}</span>
                        </td>
                        <td class="max-w-xs text-xs text-rose-600">{{ $log->error ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-10 text-center text-sm text-slate-500">এখনো কোনো মেইল পাঠানো হয়নি।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
