@props([
    'type' => 'button',
    'label',
    'variant' => 'ghost',
    'size' => 'md',
    'disabled' => false,
])

@php
    $variants = [
        'ghost' => 'border-transparent bg-transparent text-[var(--mms-color-text-muted)] hover:bg-[var(--mms-color-surface-muted)] hover:text-[var(--mms-color-text)]',
        'outline' => 'border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] text-[var(--mms-color-text-muted)] hover:border-[var(--mms-color-primary)] hover:text-[var(--mms-color-primary-hover)]',
        'primary' => 'border-transparent bg-[var(--mms-color-primary)] text-[var(--mms-color-primary-foreground)] hover:bg-[var(--mms-color-primary-hover)]',
        'danger' => 'border-transparent bg-[var(--mms-color-danger)] text-white hover:bg-red-500',
    ];

    $sizes = [
        'sm' => 'size-10',
        'md' => 'size-11',
        'lg' => 'size-12',
    ];
@endphp

<button
    type="{{ $type }}"
    aria-label="{{ $label }}"
    @disabled(filter_var($disabled, FILTER_VALIDATE_BOOLEAN))
    {{ $attributes->class([
        'inline-grid shrink-0 place-items-center rounded-[var(--mms-radius-md)] border transition duration-200 ease-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] focus-visible:ring-offset-2 focus-visible:ring-offset-[var(--mms-color-background)] disabled:cursor-not-allowed disabled:opacity-60',
        $variants[$variant] ?? $variants['ghost'],
        $sizes[$size] ?? $sizes['md'],
    ]) }}
>
    <span aria-hidden="true">{{ $slot }}</span>
</button>
