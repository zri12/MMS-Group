@props([
    'label',
])

<div class="min-w-0">
    <dt class="text-xs font-semibold uppercase tracking-wide text-[var(--mms-color-text-subtle)]">{{ $label }}</dt>
    <dd class="mt-1 break-words text-sm font-semibold text-[var(--mms-color-text)]">{{ $slot }}</dd>
</div>
