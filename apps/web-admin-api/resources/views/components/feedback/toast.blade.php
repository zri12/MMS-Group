@props([
    'message' => null,
    'variant' => 'info',
])

@if ($message)
    <div {{ $attributes->class(['fixed right-4 top-4 z-[1200] w-[min(24rem,calc(100vw-2rem))]']) }}>
        <x-ui.alert :variant="$variant" dismissible="true">{{ $message }}</x-ui.alert>
    </div>
@endif
