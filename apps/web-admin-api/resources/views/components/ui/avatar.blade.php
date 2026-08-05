@props([
    'name' => 'Admin',
    'src' => null,
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'size-8 text-xs',
        'md' => 'size-10 text-sm',
        'lg' => 'size-12 text-base',
    ];

    $initials = collect(explode(' ', trim($name)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
@endphp

<span {{ $attributes->class(['inline-grid shrink-0 place-items-center overflow-hidden rounded-full border border-[var(--mms-color-border-gold)] bg-[var(--mms-color-primary-soft)] font-semibold text-[var(--mms-color-primary-hover)]', $sizes[$size] ?? $sizes['md']]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name }}" class="size-full object-cover">
    @else
        {{ $initials ?: 'A' }}
    @endif
</span>
