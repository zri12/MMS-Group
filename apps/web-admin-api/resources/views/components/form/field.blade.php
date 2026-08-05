@props([
    'label' => null,
    'for' => null,
    'required' => false,
    'error' => null,
    'help' => null,
])

@php
    $helpId = $for && $help ? $for.'-help' : null;
    $errorId = $for && $error ? $for.'-error' : null;
@endphp

<div {{ $attributes->class(['space-y-2']) }}>
    @if ($label)
        <x-form.label :for="$for" :required="$required">{{ $label }}</x-form.label>
    @endif

    {{ $slot }}

    @if ($help)
        <x-form.help-text :id="$helpId">{{ $help }}</x-form.help-text>
    @endif

    <x-form.error :message="$error" :id="$errorId" />
</div>
