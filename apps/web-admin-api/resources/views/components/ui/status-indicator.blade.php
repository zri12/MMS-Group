@props([
    'variant' => 'neutral',
    'label' => null,
])

@php
    $variants = [
        'active' => 'bg-[var(--mms-color-success)]',
        'approved' => 'bg-[var(--mms-color-success)]',
        'completed' => 'bg-[var(--mms-color-success)]',
        'online' => 'bg-[var(--mms-color-online)]',
        'pending' => 'bg-[var(--mms-color-warning)]',
        'in-progress' => 'bg-[var(--mms-color-info)]',
        'inactive' => 'bg-[var(--mms-color-offline)]',
        'offline' => 'bg-[var(--mms-color-offline)]',
        'rejected' => 'bg-[var(--mms-color-danger)]',
        'neutral' => 'bg-[var(--mms-color-text-muted)]',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-2 text-sm text-[var(--mms-color-text-muted)]']) }}>
    <span class="size-2.5 rounded-full {{ $variants[$variant] ?? $variants['neutral'] }}" aria-hidden="true"></span>
    <span>{{ $label ?? $slot }}</span>
</span>
