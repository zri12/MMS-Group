@props([
    'title' => 'Akses tidak tersedia.',
    'description' => null,
])

<div {{ $attributes->class(['rounded-[var(--mms-radius-lg)] border border-red-500/25 bg-red-500/10 p-6 text-center text-red-100']) }}>
    <div class="mx-auto grid size-11 place-items-center rounded-full bg-red-500/15">
        <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 4h.01"></path>
            <circle cx="12" cy="12" r="9"></circle>
        </svg>
    </div>
    <h1 class="mt-4 text-xl font-semibold">{{ $title }}</h1>
    @if ($description)
        <p class="mt-2 text-sm leading-6 text-red-100/80">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-5 flex flex-wrap justify-center gap-3">{{ $slot }}</div>
    @endif
</div>
