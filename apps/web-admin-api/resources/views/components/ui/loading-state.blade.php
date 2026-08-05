@props([
    'label' => 'Memuat...',
])

<div {{ $attributes->class(['flex items-center justify-center gap-3 rounded-[var(--mms-radius-lg)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-6 text-sm text-[var(--mms-color-text-muted)]']) }} aria-busy="true" role="status">
    <x-ui.spinner />
    <span>{{ $label }}</span>
</div>
