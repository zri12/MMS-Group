@props([
    'text',
])

<span {{ $attributes->class(['group relative inline-flex']) }}>
    {{ $slot }}
    <span class="pointer-events-none absolute bottom-full left-1/2 z-[950] mb-2 hidden -translate-x-1/2 whitespace-nowrap rounded-md bg-[var(--mms-color-surface-elevated)] px-2 py-1 text-xs text-[var(--mms-color-text)] shadow-[var(--mms-shadow-sm)] group-focus-within:block group-hover:block">
        {{ $text }}
    </span>
</span>
