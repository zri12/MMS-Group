@props([
    'title' => 'Belum ada data.',
    'description' => null,
])

<div {{ $attributes->class(['rounded-[var(--mms-radius-lg)] border border-dashed border-[var(--mms-color-border-strong)] bg-[var(--mms-color-surface)] p-6 text-center']) }}>
    <div class="mx-auto grid size-11 place-items-center rounded-full bg-[var(--mms-color-primary-soft)] text-[var(--mms-color-primary-hover)]">
        <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M7 7v11h10V7M9 11h6"></path>
        </svg>
    </div>
    <h2 class="mt-4 text-base font-semibold text-[var(--mms-color-text)]">{{ $title }}</h2>
    @if ($description)
        <p class="mt-2 text-sm leading-6 text-[var(--mms-color-text-muted)]">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
