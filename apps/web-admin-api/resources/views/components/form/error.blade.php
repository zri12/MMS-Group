@props([
    'message' => null,
    'id' => null,
])

@if ($message)
    <p @if($id) id="{{ $id }}" @endif role="alert" {{ $attributes->class(['text-sm text-red-200']) }}>
        {{ $message }}
    </p>
@endif
