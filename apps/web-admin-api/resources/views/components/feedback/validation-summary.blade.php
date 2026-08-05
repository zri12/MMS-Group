@props([
    'bag' => $errors,
])

@if ($bag->any())
    <x-ui.alert variant="danger" {{ $attributes }}>
        {{ $bag->first() }}
    </x-ui.alert>
@endif
