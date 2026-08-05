@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'leadingIcon' => null,
    'trailingIcon' => null,
])

@php
    $variants = [
        'primary' => 'border-transparent bg-[var(--mms-color-primary)] text-[var(--mms-color-primary-foreground)] hover:bg-[var(--mms-color-primary-hover)] focus-visible:ring-[var(--mms-color-focus)]',
        'secondary' => 'border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text)] hover:bg-[var(--mms-color-surface-elevated)] focus-visible:ring-[var(--mms-color-focus)]',
        'outline' => 'border-[var(--mms-color-border-strong)] bg-transparent text-[var(--mms-color-text)] hover:border-[var(--mms-color-primary)] hover:text-[var(--mms-color-primary-hover)] focus-visible:ring-[var(--mms-color-focus)]',
        'ghost' => 'border-transparent bg-transparent text-[var(--mms-color-text-muted)] hover:bg-[var(--mms-color-surface-muted)] hover:text-[var(--mms-color-text)] focus-visible:ring-[var(--mms-color-focus)]',
        'danger' => 'border-transparent bg-[var(--mms-color-danger)] text-white hover:bg-red-500 focus-visible:ring-red-300',
    ];

    $sizes = [
        'sm' => 'min-h-10 px-3 text-xs',
        'md' => 'min-h-11 px-4 text-sm',
        'lg' => 'min-h-12 px-5 text-sm',
    ];

    $isLoading = filter_var($loading, FILTER_VALIDATE_BOOLEAN);
    $isDisabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN) || $isLoading;
@endphp

<button
    type="{{ $type }}"
    @disabled($isDisabled)
    aria-disabled="{{ $isDisabled ? 'true' : 'false' }}"
    @if($isLoading) aria-busy="true" @endif
    {{ $attributes->class([
        'inline-flex items-center justify-center gap-2 rounded-[var(--mms-radius-md)] border font-semibold transition duration-200 ease-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-[var(--mms-color-background)] disabled:cursor-not-allowed disabled:opacity-60',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        'w-full' => filter_var($fullWidth, FILTER_VALIDATE_BOOLEAN),
    ]) }}
>
    @if ($isLoading)
        <x-ui.spinner size="sm" />
    @elseif ($leadingIcon)
        <span aria-hidden="true">{{ $leadingIcon }}</span>
    @endif

    <span>{{ $slot }}</span>

    @if (! $isLoading && $trailingIcon)
        <span aria-hidden="true">{{ $trailingIcon }}</span>
    @endif
</button>
