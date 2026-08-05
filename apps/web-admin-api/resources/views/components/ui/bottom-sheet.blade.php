@props([
    'name'         => 'sheet',
    'title'        => '',
    'description'  => '',
    'triggerLabel' => 'Buka',
])

{{--
  Reusable Mobile Bottom Sheet Component
  Dipanggil dari luar dengan:
    - x-data yang punya variabel boolean (misalnya: showFilter)
    - x-bind:class atau x-show berdasarkan variabel tersebut

  Penggunaan:
    <x-ui.bottom-sheet name="filter" title="Filter" :description="...">
        <x-slot:trigger>...tombol pemicu...</x-slot:trigger>
        ...konten sheet...
    </x-ui.bottom-sheet>
--}}

@php $sheetId = 'sheet-'.$name; @endphp

<div
    x-data="{ {{ $name }}Open: false }"
    x-on:keydown.escape.window="{{ $name }}Open = false"
    x-effect="document.body.classList.toggle('overflow-hidden', {{ $name }}Open)"
>
    {{-- Trigger slot --}}
    @if ($slot->isEmpty())
        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-4 py-2.5 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
            x-on:click="{{ $name }}Open = true"
            aria-expanded="{{ "{{ ".$name."Open }}" }}"
            aria-controls="{{ $sheetId }}"
        >
            {{ $triggerLabel }}
        </button>
    @else
        <div x-on:click="{{ $name }}Open = true">
            {{ $slot }}
        </div>
    @endif

    {{-- Overlay --}}
    <div
        x-cloak
        x-show="{{ $name }}Open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-160"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="mms-sheet-backdrop"
        aria-hidden="true"
        x-on:click="{{ $name }}Open = false"
    ></div>

    {{-- Sheet panel --}}
    <div
        id="{{ $sheetId }}"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $sheetId }}-title"
        x-cloak
        x-show="{{ $name }}Open"
        x-transition:enter="transition ease-out duration-220"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-160"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="mms-sheet-panel mms-sheet-panel-tall"
        style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom))"
        x-trap="{{ $name }}Open"
    >
        {{-- Handle bar --}}
        <div class="mx-auto mt-3 h-1 w-12 rounded-full bg-white/20" aria-hidden="true"></div>

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-[var(--mms-color-border)] px-4 pb-3 pt-4">
            <div>
                @if ($title)
                    <h2 id="{{ $sheetId }}-title" class="text-[16px] font-semibold text-[var(--mms-color-text)]">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-0.5 text-[12px] text-[var(--mms-color-text-subtle)]">{{ $description }}</p>
                @endif
            </div>
            <button
                type="button"
                class="grid size-9 place-items-center rounded-full border border-[var(--mms-color-border)] text-[var(--mms-color-text-muted)] transition hover:text-[var(--mms-color-text)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                aria-label="Tutup {{ $title }}"
                x-on:click="{{ $name }}Open = false"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
                </svg>
            </button>
        </div>

        {{-- Content --}}
        <div class="mms-scrollbar overflow-y-auto px-4 pt-4 pb-2" style="max-height: calc(90dvh - 6rem)">
            {{ $content ?? '' }}
        </div>
    </div>
</div>
