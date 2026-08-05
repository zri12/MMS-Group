@props([
    'name',
    'id' => null,
    'value' => '1',
    'label' => null,
    'checked' => false,
    'invalid' => false,
    'describedBy' => null,
    'wrapperClass' => '',
    'labelClass' => '',
])

@php
    $fieldId = $id ?: $name;
    $oldValue = old($name);
    $isChecked = $oldValue === null
        ? filter_var($checked, FILTER_VALIDATE_BOOLEAN)
        : (string) $oldValue === (string) $value;
    $isInvalid = filter_var($invalid, FILTER_VALIDATE_BOOLEAN);
@endphp

<label @class(['inline-flex items-center gap-3 text-sm text-[var(--mms-color-text)]', $wrapperClass])>
    <input
        id="{{ $fieldId }}"
        name="{{ $name }}"
        type="checkbox"
        value="{{ $value }}"
        role="switch"
        aria-checked="{{ $isChecked ? 'true' : 'false' }}"
        aria-invalid="{{ $isInvalid ? 'true' : 'false' }}"
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @checked($isChecked)
        x-on:change="$el.setAttribute('aria-checked', $el.checked ? 'true' : 'false')"
        {{ $attributes->class(['peer sr-only']) }}
    >
    <span class="relative h-6 w-11 rounded-full border border-[var(--mms-color-border-strong)] bg-[var(--mms-color-surface-muted)] transition after:absolute after:left-0.5 after:top-0.5 after:size-5 after:rounded-full after:bg-[var(--mms-color-text)] after:transition after:content-[''] peer-checked:border-[var(--mms-color-primary)] peer-checked:bg-[var(--mms-color-primary)] peer-checked:after:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--mms-color-focus)]"></span>
    <span @class([$labelClass])>{{ $label ?? $slot }}</span>
</label>
