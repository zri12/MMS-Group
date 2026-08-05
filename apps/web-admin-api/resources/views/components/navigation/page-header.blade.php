@props([
    'title',
    'description' => null,
    'breadcrumbs' => [],
])

<header {{ $attributes->class(['flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div class="min-w-0">
        <x-navigation.breadcrumb :items="$breadcrumbs" />
        <h1 class="mt-2 text-2xl font-semibold leading-tight text-[var(--mms-color-text)] sm:text-3xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-2 max-w-3xl text-sm leading-6 text-[var(--mms-color-text-muted)]">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap gap-2">{{ $actions }}</div>
    @endisset
</header>
