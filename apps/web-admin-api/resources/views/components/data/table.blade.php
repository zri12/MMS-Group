@props([
    'caption' => null,
])

<div {{ $attributes->class(['operation-table-scroll w-full overflow-x-auto rounded-[var(--mms-radius-lg)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]']) }}>
    <table class="w-full min-w-[640px] border-collapse text-left text-sm">
        @if ($caption)
            <caption class="sr-only">{{ $caption }}</caption>
        @endif
        {{ $slot }}
    </table>
</div>
