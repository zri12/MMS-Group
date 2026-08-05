@props([
    'href',
    'variant' => 'primary',
    'size' => 'md',
    'fullWidth' => false,
])

@php
    $variants = [
        'primary' => 'border-transparent bg-[var(--mms-color-primary)] text-[var(--mms-color-primary-foreground)] hover:bg-[var(--mms-color-primary-hover)]',
        'secondary' => 'border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text)] hover:bg-[var(--mms-color-surface-elevated)]',
        'outline' => 'border-[var(--mms-color-border-strong)] bg-transparent text-[var(--mms-color-text)] hover:border-[var(--mms-color-primary)] hover:text-[var(--mms-color-primary-hover)]',
        'ghost' => 'border-transparent bg-transparent text-[var(--mms-color-text-muted)] hover:bg-[var(--mms-color-surface-muted)] hover:text-[var(--mms-color-text)]',
        'danger' => 'border-transparent bg-[var(--mms-color-danger)] text-white hover:bg-red-500',
    ];

    $sizes = [
        'sm' => 'min-h-10 px-3 text-xs',
        'md' => 'min-h-11 px-4 text-sm',
        'lg' => 'min-h-12 px-5 text-sm',
    ];
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2 rounded-[var(--mms-radius-md)] border font-semibold transition duration-200 ease-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] focus-visible:ring-offset-2 focus-visible:ring-offset-[var(--mms-color-background)]',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        'w-full' => filter_var($fullWidth, FILTER_VALIDATE_BOOLEAN),
    ]) }}
>
    {{ $slot }}
</a>
