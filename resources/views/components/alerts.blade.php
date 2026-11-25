@php
    $types = [
        'success' => 'border-l-[#15803d] bg-[#edf5ee] text-[#166534]',
        'warning' => 'border-l-[#b45309] bg-[#f8efd9] text-[#6b4e16]',
        'error' => 'border-l-brand-700 bg-brand-50 text-brand-800',
        'status' => 'border-l-ink-muted bg-raised text-ink-soft',
    ];
@endphp

@foreach ($types as $key => $style)
    @if (session($key))
        <div data-flash class="mb-4 rounded-lg border border-line border-l-2 px-4 py-3 text-sm {{ $style }}">
            <p class="leading-relaxed">{{ session($key) }}</p>
        </div>
    @endif
@endforeach

@if (isset($errors) && $errors->any())
    <div class="mb-4 rounded-lg border border-line border-l-2 border-l-brand-700 bg-brand-50 px-4 py-3 text-sm text-brand-800">
        <p class="font-medium">একটু দেখে নিন:</p>
        <ul class="mt-1 list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
