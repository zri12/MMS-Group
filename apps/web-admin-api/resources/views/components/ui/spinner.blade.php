@props([
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'size-4',
        'md' => 'size-5',
        'lg' => 'size-6',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<svg
    {{ $attributes->class([$sizeClass, 'animate-spin text-current']) }}
    aria-hidden="true"
    viewBox="0 0 24 24"
    fill="none"
>
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
</svg>
