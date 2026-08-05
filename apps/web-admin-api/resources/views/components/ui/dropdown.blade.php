@props([
    'label' => 'Buka menu',
    'align' => 'right',
    'compactMobile' => false,
])

@php
    $id = 'dropdown-'.\Illuminate\Support\Str::random(8);
    $alignment = $align === 'left' ? 'left-0' : 'right-0';
@endphp

<div {{ $attributes->class(['relative inline-block text-left']) }} x-data="{ open: false }" x-on:keydown.escape.window="open = false" x-on:click.outside="open = false">
    <button
        type="button"
        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-sm font-semibold text-[var(--mms-color-text)] hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] {{ $compactMobile ? 'max-lg:size-10 max-lg:min-h-10 max-lg:px-0' : '' }}"
        x-on:click="open = ! open"
        x-bind:aria-expanded="open"
        aria-controls="{{ $id }}"
    >
        @if ($compactMobile)
            <svg class="hidden size-4 max-lg:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 9a7 7 0 0 0-14 0"></path>
            </svg>
        @endif
        <span class="{{ $compactMobile ? 'max-lg:sr-only' : '' }}">{{ $label }}</span>
        <svg class="size-4 {{ $compactMobile ? 'max-lg:hidden' : '' }}" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"></path>
        </svg>
    </button>

    <div
        id="{{ $id }}"
        x-show="open"
        x-cloak
        class="absolute {{ $alignment }} z-[900] mt-2 min-w-48 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-elevated)] p-2 shadow-[var(--mms-shadow-md)]"
    >
        {{ $slot }}
    </div>
</div>
