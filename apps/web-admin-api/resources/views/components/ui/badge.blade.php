@props([
    'variant' => 'neutral',
])

@php
    $variants = [
        'success' => 'border-green-500/30 bg-green-500/10 text-green-100',
        'warning' => 'border-amber-500/30 bg-amber-500/10 text-amber-100',
        'danger' => 'border-red-500/30 bg-red-500/10 text-red-100',
        'info' => 'border-blue-500/30 bg-blue-500/10 text-blue-100',
        'primary' => 'border-[var(--mms-color-border-gold)] bg-[var(--mms-color-primary-soft)] text-[var(--mms-color-primary-hover)]',
        'neutral' => 'border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text-muted)]',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-semibold', $variants[$variant] ?? $variants['neutral']]) }}>
    {{ $slot }}
</span>
