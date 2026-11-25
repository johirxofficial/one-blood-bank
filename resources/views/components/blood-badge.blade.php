@props(['group', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'h-8 w-9 text-sm',
        'md' => 'h-10 w-11 text-sm',
        'lg' => 'h-14 w-14 text-xl',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'tnum inline-grid place-items-center rounded-lg border border-brand-200 bg-brand-50 font-medium text-brand-700 '.($sizes[$size] ?? $sizes['md'])]) }}>
    {{ $group }}
</span>
