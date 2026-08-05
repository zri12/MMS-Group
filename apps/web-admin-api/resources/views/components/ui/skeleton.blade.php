@props([
    'shape' => 'block',
])

@php
    $shapeClasses = [
        'line' => 'h-3 rounded-full',
        'block' => 'h-20 rounded-[var(--mms-radius-md)]',
        'circle' => 'size-10 rounded-full',
    ];
@endphp

<span {{ $attributes->class(['block animate-pulse bg-[var(--mms-color-surface-muted)]', $shapeClasses[$shape] ?? $shapeClasses['block']]) }} aria-hidden="true"></span>
