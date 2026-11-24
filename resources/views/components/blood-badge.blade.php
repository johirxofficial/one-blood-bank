@props(['group', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'h-8 w-9 text-sm',
        'md' => 'h-11 w-12 text-base',
        'lg' => 'h-16 w-16 text-2xl',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-grid place-items-center rounded-xl bg-brand-50 font-extrabold text-brand-700 ring-1 ring-inset ring-brand-100 '.($sizes[$size] ?? $sizes['md'])]) }}>
    {{ $group }}
</span>
