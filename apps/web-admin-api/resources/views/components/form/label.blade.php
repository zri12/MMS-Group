@props([
    'for' => null,
    'required' => false,
])

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->class(['block text-sm font-semibold text-[var(--mms-color-primary-hover)]']) }}
>
    {{ $slot }}
    @if (filter_var($required, FILTER_VALIDATE_BOOLEAN))
        <span class="text-red-200" aria-label="wajib">*</span>
    @endif
</label>
