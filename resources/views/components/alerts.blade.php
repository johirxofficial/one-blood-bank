@php
    $types = [
        'success' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-800', 'icon' => '✓'],
        'warning' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-900', 'icon' => '!'],
        'error' => ['bg' => 'bg-rose-50 border-rose-200 text-rose-800', 'icon' => '×'],
        'status' => ['bg' => 'bg-sky-50 border-sky-200 text-sky-800', 'icon' => 'i'],
    ];
@endphp

@foreach ($types as $key => $style)
    @if (session($key))
        <div data-flash class="mb-4 flex items-start gap-3 rounded-xl border px-4 py-3 text-sm {{ $style['bg'] }}">
            <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-white/70 text-xs font-bold">{{ $style['icon'] }}</span>
            <p class="flex-1 leading-relaxed">{{ session($key) }}</p>
        </div>
    @endif
@endforeach

@if (isset($errors) && $errors->any())
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        <p class="font-semibold">একটু দেখে নিন:</p>
        <ul class="mt-1 list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
