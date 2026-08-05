@props([
    'items' => [],
])

@if (count($items) > 0)
    <nav {{ $attributes->class(['text-sm text-[var(--mms-color-text-muted)]']) }} aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            @foreach ($items as $item)
                <li class="flex items-center gap-2">
                    @if (! $loop->first)
                        <span aria-hidden="true">/</span>
                    @endif
                    @if (($item['href'] ?? null) && ! $loop->last)
                        <a class="hover:text-[var(--mms-color-primary-hover)]" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                    @else
                        <span @if($loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
