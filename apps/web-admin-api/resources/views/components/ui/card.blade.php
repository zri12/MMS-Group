@props([
    'title' => null,
    'description' => null,
    'padding' => 'md',
])

@php
    $paddingClasses = [
        'none' => '',
        'sm' => 'p-4',
        'md' => 'p-5 sm:p-6',
        'lg' => 'p-6 sm:p-8',
    ];
@endphp

<section {{ $attributes->class(['rounded-[var(--mms-radius-lg)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]', $paddingClasses[$padding] ?? $paddingClasses['md']]) }}>
    @if ($title || $description || isset($actions))
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-base font-semibold text-[var(--mms-color-text)]">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-1 text-sm leading-6 text-[var(--mms-color-text-muted)]">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex flex-wrap gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    {{ $slot }}
</section>
