@props([
    'tabs' => [],
    'default' => null,
    'panels' => [],
])

@php
    $initial = $default ?: (array_key_first($tabs) ?: 'tab');
    $componentId = $attributes->get('id') ?: 'tabs-'.\Illuminate\Support\Str::random(8);
    $tabKeys = array_keys($tabs);
@endphp

<div
    {{ $attributes }}
    x-data="{
        active: @js($initial),
        tabs: @js($tabKeys),
        focusActive() {
            this.$nextTick(() => this.$refs['tab_' + this.active]?.focus?.());
        },
        move(delta) {
            const index = this.tabs.indexOf(this.active);
            this.active = this.tabs[(index + delta + this.tabs.length) % this.tabs.length];
            this.focusActive();
        },
        first() {
            this.active = this.tabs[0];
            this.focusActive();
        },
        last() {
            this.active = this.tabs[this.tabs.length - 1];
            this.focusActive();
        },
    }"
>
    <div
        class="flex flex-wrap gap-2"
        role="tablist"
        aria-label="Pilihan tampilan"
        x-on:keydown.arrow-right.prevent="move(1)"
        x-on:keydown.arrow-left.prevent="move(-1)"
        x-on:keydown.home.prevent="first()"
        x-on:keydown.end.prevent="last()"
    >
        @foreach ($tabs as $key => $label)
            @php
                $safeKey = \Illuminate\Support\Str::slug((string) $key);
                $tabId = $componentId.'-'.$safeKey.'-tab';
                $panelId = $componentId.'-'.$safeKey.'-panel';
            @endphp
            <button
                id="{{ $tabId }}"
                type="button"
                role="tab"
                x-ref="tab_{{ $key }}"
                x-bind:aria-selected="active === '{{ $key }}'"
                aria-controls="{{ $panelId }}"
                x-bind:tabindex="active === '{{ $key }}' ? 0 : -1"
                x-on:click="active = '{{ $key }}'"
                @class([
                    'rounded-[var(--mms-radius-md)] border px-3 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]',
                ])
                x-bind:class="active === '{{ $key }}' ? 'border-[var(--mms-color-border-gold)] bg-[var(--mms-color-primary-soft)] text-[var(--mms-color-primary-hover)]' : 'border-[var(--mms-color-border)] text-[var(--mms-color-text-muted)] hover:bg-[var(--mms-color-surface-muted)]'"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="mt-4">
        @forelse ($tabs as $key => $label)
            @php
                $safeKey = \Illuminate\Support\Str::slug((string) $key);
                $tabId = $componentId.'-'.$safeKey.'-tab';
                $panelId = $componentId.'-'.$safeKey.'-panel';
            @endphp
            <section
                id="{{ $panelId }}"
                role="tabpanel"
                tabindex="0"
                aria-labelledby="{{ $tabId }}"
                x-show="active === '{{ $key }}'"
                @if($key !== $initial) x-cloak @endif
                class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4 text-sm leading-6 text-[var(--mms-color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
            >
                {{ $panels[$key] ?? $slot }}
            </section>
        @empty
            <section role="tabpanel" class="text-sm text-[var(--mms-color-text-muted)]">
                {{ $slot }}
            </section>
        @endforelse
    </div>
</div>
