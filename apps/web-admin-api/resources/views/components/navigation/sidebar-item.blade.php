@props([
    'href',
    'label',
    'active' => false,
    'disabled' => false,
])

@php
    $isActive = filter_var($active, FILTER_VALIDATE_BOOLEAN);
    $isDisabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
@endphp

@if ($isDisabled)
    <span {{ $attributes->class(['flex min-h-11 items-center gap-3 rounded-[var(--mms-radius-md)] px-3 text-sm font-semibold text-[var(--mms-color-text-subtle)] opacity-60']) }}>
        <span class="grid size-5 place-items-center" aria-hidden="true">{{ $slot }}</span>
        <span class="truncate">{{ $label }}</span>
    </span>
@else
    <a
        href="{{ $href }}"
        @if($isActive) aria-current="page" @endif
        {{ $attributes->class([
            'relative flex min-h-11 items-center gap-3 rounded-[var(--mms-radius-md)] px-3 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]',
            $isActive ? 'bg-[var(--mms-color-primary-soft)] text-[var(--mms-color-primary-hover)]' : 'text-[var(--mms-color-text-muted)] hover:bg-[var(--mms-color-surface-muted)] hover:text-[var(--mms-color-text)]',
        ]) }}
    >
        @if ($isActive)
            <span class="absolute left-0 top-2 h-7 w-0.5 rounded-full bg-[var(--mms-color-primary)]" aria-hidden="true"></span>
        @endif
        <span class="grid size-5 place-items-center" aria-hidden="true">{{ $slot }}</span>
        <span class="truncate">{{ $label }}</span>
    </a>
@endif
