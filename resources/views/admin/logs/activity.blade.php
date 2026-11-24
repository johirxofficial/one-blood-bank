@extends('layouts.admin')

@section('title', 'অ্যাক্টিভিটি লগ')
@section('heading', 'অ্যাক্টিভিটি লগ')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">অ্যাক্টিভিটি লগ</h2>
            <p class="mt-1 text-sm text-slate-600">সিস্টেমের সব গুরুত্বপূর্ণ কার্যক্রমের রেকর্ড। মোট {{ number_format($logs->total()) }} টি।</p>
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
                        <td class="whitespace-nowrap text-xs text-slate-500">
                            <p class="font-medium text-slate-700">{{ $log->created_at?->format('d M Y') }}</p>
                            <p>{{ $log->created_at?->format('h:i:s A') }}</p>
                        </td>
                        <td>
                            @if ($log->user)
                                <p class="text-sm font-semibold text-slate-800">{{ $log->user->name }}</p>
                                <p class="text-xs text-slate-500">@ {{ $log->user->username }}</p>
                            @else
                                <span class="text-xs text-slate-400">সিস্টেম</span>
                            @endif
                        </td>
                        <td><span class="badge bg-slate-100 font-mono text-[11px] text-slate-700">{{ $log->action }}</span></td>
                        <td class="max-w-md text-sm text-slate-600">{{ $log->description ?: '—' }}</td>
                        <td class="whitespace-nowrap font-mono text-xs text-slate-500">{{ $log->ip ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-sm text-slate-500">কোনো অ্যাক্টিভিটি লগ নেই।</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
