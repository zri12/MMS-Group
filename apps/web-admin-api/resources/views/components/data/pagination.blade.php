@props([
    'paginator' => null,
])

@if ($paginator)
    <div {{ $attributes }}>
        {{ $paginator->links('vendor.pagination.mms') }}
    </div>
@else
    <nav {{ $attributes->class(['flex flex-wrap items-center justify-between gap-3 text-sm text-[var(--mms-color-text-muted)]']) }} aria-label="Navigasi halaman">
        <span>Halaman 1</span>
        <div class="flex gap-2">
            <x-ui.button type="button" variant="outline" size="sm" disabled="true">Sebelumnya</x-ui.button>
            <x-ui.button type="button" variant="outline" size="sm" disabled="true">Berikutnya</x-ui.button>
        </div>
    </nav>
@endif
