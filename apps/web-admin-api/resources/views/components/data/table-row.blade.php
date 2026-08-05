@props([
    'selected' => false,
])

<tr {{ $attributes->class(['transition hover:bg-[var(--mms-color-surface-muted)]', 'bg-[var(--mms-color-primary-soft)]' => filter_var($selected, FILTER_VALIDATE_BOOLEAN)]) }}>
    {{ $slot }}
</tr>
