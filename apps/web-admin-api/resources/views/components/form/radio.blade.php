@props([
    'name',
    'id' => null,
    'value',
    'label' => null,
    'checked' => false,
    'invalid' => false,
    'describedBy' => null,
    'wrapperClass' => '',
    'labelClass' => '',
])

@php
    $fieldId = $id ?: $name.'-'.$value;
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
        type="radio"
        value="{{ $value }}"
        @checked($isChecked)
        aria-invalid="{{ $isInvalid ? 'true' : 'false' }}"
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->class([
            'size-4 border-[var(--mms-color-border-strong)] bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-primary)] focus:ring-[var(--mms-color-focus)] disabled:cursor-not-allowed disabled:opacity-60',
            $isInvalid ? 'border-red-400/70' : '',
        ]) }}
    >
    <span @class([$labelClass])>{{ $label ?? $slot }}</span>
</label>
