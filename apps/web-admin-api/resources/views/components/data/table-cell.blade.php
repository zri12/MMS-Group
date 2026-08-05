@props([
    'heading' => false,
])

@if (filter_var($heading, FILTER_VALIDATE_BOOLEAN))
    <th scope="col" {{ $attributes->class(['px-4 py-3 font-semibold']) }}>{{ $slot }}</th>
@else
    <td {{ $attributes->class(['px-4 py-3 text-[var(--mms-color-text-muted)]']) }}>{{ $slot }}</td>
@endif
