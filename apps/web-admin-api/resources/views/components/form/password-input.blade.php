@props([
    'name',
    'id' => null,
    'invalid' => false,
    'describedBy' => null,
    'showLabel' => 'Tampilkan password',
    'hideLabel' => 'Sembunyikan password',
])

@aware([
    'for' => null,
    'error' => null,
    'help' => null,
])

@php
    $fieldId = $id ?: $name;
    $hasFieldContext = $for === $fieldId;
    $isInvalid = filter_var($invalid, FILTER_VALIDATE_BOOLEAN) || ($hasFieldContext && filled($error));
    $describedByIds = collect(explode(' ', (string) $describedBy))
        ->filter()
        ->when($hasFieldContext && $help, fn ($ids) => $ids->push($fieldId.'-help'))
        ->when($hasFieldContext && $error, fn ($ids) => $ids->push($fieldId.'-error'))
        ->unique()
        ->implode(' ');
@endphp

<span class="relative block" x-data="{ show: false }">
    <input
        id="{{ $fieldId }}"
        name="{{ $name }}"
        x-bind:type="show ? 'text' : 'password'"
        aria-invalid="{{ $isInvalid ? 'true' : 'false' }}"
        @if($describedByIds) aria-describedby="{{ $describedByIds }}" @endif
        {{ $attributes->class([
            'min-h-11 w-full rounded-[var(--mms-radius-md)] border bg-[var(--mms-color-surface-muted)] px-4 pr-12 text-sm text-[var(--mms-color-text)] outline-none transition duration-200 ease-out placeholder:text-[var(--mms-color-text-subtle)] focus:border-[var(--mms-color-primary)] focus:ring-2 focus:ring-[var(--mms-color-primary-soft)] disabled:cursor-not-allowed disabled:opacity-60',
            $isInvalid ? 'border-red-400/70' : 'border-[var(--mms-color-border-strong)]',
        ]) }}
    >
    <button
        type="button"
        x-on:click="show = ! show"
        x-bind:aria-label="show ? '{{ $hideLabel }}' : '{{ $showLabel }}'"
        x-bind:aria-pressed="show"
        class="absolute right-2 top-1/2 grid size-8 -translate-y-1/2 place-items-center rounded-[var(--mms-radius-sm)] text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-elevated)] hover:text-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
    >
        <svg x-show="! show" class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
        <svg x-cloak x-show="show" class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.6 10.6A3 3 0 0 0 13.4 13.4M9.9 4.6A10.3 10.3 0 0 1 12 4c6 0 9.5 8 9.5 8a17.6 17.6 0 0 1-2.2 3.2M6.3 6.3C3.8 8 2.5 12 2.5 12s3.5 8 9.5 8a10 10 0 0 0 4.4-1"></path>
        </svg>
    </button>
</span>
