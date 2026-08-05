@props([
    'label',
    'value',
    'helper' => null,
    'trend' => null,
])

<x-ui.card padding="sm" {{ $attributes }}>
    <p class="text-xs font-semibold uppercase tracking-wide text-[var(--mms-color-text-subtle)]">{{ $label }}</p>
    <p class="mt-2 break-words text-xl font-semibold text-[var(--mms-color-text)]">{{ $value }}</p>
    @if ($helper || $trend)
        <p class="mt-2 text-xs text-[var(--mms-color-text-muted)]">{{ $trend ?? $helper }}</p>
    @endif
</x-ui.card>
