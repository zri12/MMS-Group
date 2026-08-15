@props([
    'compact' => false,
])

@php
    $logoUrl = asset('storage/LOGO-KSP.jpeg');
@endphp

<div {{ $attributes->class(['flex min-w-0 items-center gap-3']) }}>
    <span class="grid size-11 shrink-0 place-items-center overflow-hidden rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border-gold)] bg-white p-1">
        <img
            src="{{ $logoUrl }}"
            alt="Logo KSP Manunggal Makmur Sejahtera"
            class="size-full object-contain"
            loading="eager"
        >
    </span>
    @unless (filter_var($compact, FILTER_VALIDATE_BOOLEAN))
        <span class="min-w-0">
            <span class="block truncate text-sm font-semibold text-[var(--mms-color-text)]">KSP MMS</span>
            <span class="mt-0.5 block truncate text-xs text-[var(--mms-color-text-muted)]">PDL Monitoring</span>
        </span>
    @endunless
</div>
