@props([
    'variant' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $variants = [
        'success' => ['class' => 'border-green-500/30 bg-green-500/10 text-green-100', 'icon' => 'M9 12.75 11.25 15 15 9.75', 'role' => 'status'],
        'warning' => ['class' => 'border-amber-500/30 bg-amber-500/10 text-amber-100', 'icon' => 'M12 8v4m0 4h.01', 'role' => 'alert'],
        'danger' => ['class' => 'border-red-500/30 bg-red-500/10 text-red-100', 'icon' => 'M12 9v4m0 4h.01', 'role' => 'alert'],
        'info' => ['class' => 'border-blue-500/30 bg-blue-500/10 text-blue-100', 'icon' => 'M12 11v5m0-8h.01', 'role' => 'status'],
        'neutral' => ['class' => 'border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text)]', 'icon' => 'M12 11v5m0-8h.01', 'role' => 'status'],
    ];

    $config = $variants[$variant] ?? $variants['info'];
@endphp

<div
    x-data="{ visible: true }"
    x-show="visible"
    role="{{ $config['role'] }}"
    {{ $attributes->class(['rounded-[var(--mms-radius-md)] border p-4 text-sm', $config['class']]) }}
>
    <div class="flex gap-3">
        <svg class="mt-0.5 size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $config['icon'] }}"></path>
            <circle cx="12" cy="12" r="9"></circle>
        </svg>
        <div class="min-w-0 flex-1">
            @if ($title)
                <p class="font-semibold">{{ $title }}</p>
            @endif
            <div @class(['mt-1' => $title])>{{ $slot }}</div>
        </div>
        @if (filter_var($dismissible, FILTER_VALIDATE_BOOLEAN))
            <button type="button" class="rounded-md p-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-current" x-on:click="visible = false" aria-label="Tutup pesan">
                <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18"></path>
                </svg>
            </button>
        @endif
    </div>
</div>
