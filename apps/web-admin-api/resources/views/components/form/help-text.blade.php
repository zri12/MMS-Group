@props([
    'id' => null,
])

<p @if($id) id="{{ $id }}" @endif {{ $attributes->class(['text-sm leading-6 text-[var(--mms-color-text-muted)]']) }}>
    {{ $slot }}
</p>
