@props([
    'name',
    'id' => null,
    'invalid' => false,
    'describedBy' => null,
    'placeholder' => null,
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

<select
    id="{{ $fieldId }}"
    name="{{ $name }}"
    aria-invalid="{{ $isInvalid ? 'true' : 'false' }}"
    @if($describedByIds) aria-describedby="{{ $describedByIds }}" @endif
    {{ $attributes->class([
        'min-h-11 w-full rounded-[var(--mms-radius-md)] border bg-[var(--mms-color-surface-muted)] px-4 text-sm text-[var(--mms-color-text)] outline-none transition duration-200 ease-out focus:border-[var(--mms-color-primary)] focus:ring-2 focus:ring-[var(--mms-color-primary-soft)] disabled:cursor-not-allowed disabled:opacity-60',
        $isInvalid ? 'border-red-400/70' : 'border-[var(--mms-color-border-strong)]',
    ]) }}
>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</select>
