@props([
    'href',
    'label',
    'active' => false,
])

@php
    $isActive = filter_var($active, FILTER_VALIDATE_BOOLEAN);
@endphp

<a
    href="{{ $href }}"
    @if($isActive) aria-current="page" @endif
    {{ $attributes->class(['flex h-[58px] min-w-0 flex-col items-center justify-center gap-0.5 rounded-[var(--mms-radius-md)] px-0.5 text-[10px] font-semibold leading-tight transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] sm:h-[60px] sm:gap-1 sm:px-1 sm:text-xs']) }}
>
    <span @class([
        'grid size-8 place-items-center rounded-[var(--mms-radius-md)]',
        $isActive ? 'bg-[var(--mms-color-primary)] text-[var(--mms-color-primary-foreground)]' : 'text-[var(--mms-color-text-muted)]',
    ]) aria-hidden="true">
        {{ $slot }}
    </span>
    <span @class(['max-w-full truncate', $isActive ? 'text-[var(--mms-color-primary-hover)]' : 'text-[var(--mms-color-text-muted)]'])>{{ $label }}</span>
</a>
