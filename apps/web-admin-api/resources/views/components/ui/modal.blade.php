@props([
    'name',
    'title',
    'closeOnOverlay' => true,
    'closeOnEscape' => true,
    'descriptionId' => null,
])

<div
    x-data="{
        open: false,
        previous: null,
        previousOverflow: '',
        focusable() {
            return Array.from(this.$refs.panel?.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), details summary, [tabindex]:not([tabindex=\'-1\'])') ?? [])
                .filter((element) => ! element.hasAttribute('disabled') && element.getAttribute('aria-hidden') !== 'true');
        },
        openDialog(trigger = null) {
            this.previous = trigger || document.activeElement;
            this.previousOverflow = document.body.style.overflow;
            this.open = true;
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => {
                const first = this.focusable()[0];
                (first || this.$refs.panel)?.focus?.();
            });
        },
        closeDialog() {
            this.open = false;
            document.body.style.overflow = this.previousOverflow || '';
            this.$nextTick(() => this.previous?.focus?.());
        },
        trapTab(event) {
            const items = this.focusable();
            if (items.length === 0) {
                event.preventDefault();
                this.$refs.panel?.focus?.();
                return;
            }

            const first = items[0];
            const last = items[items.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
                return;
            }

            if (! event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        },
    }"
    x-on:open-modal.window="if ($event.detail === @js($name)) openDialog()"
    x-on:keydown.escape.window="if (open && {{ filter_var($closeOnEscape, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false' }}) closeDialog()"
>
    @isset($trigger)
        <span x-ref="trigger" x-on:click="openDialog($refs.trigger)" class="contents">
            {{ $trigger }}
        </span>
    @endisset

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[1100] grid place-items-center overflow-y-auto bg-black/70 p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $name }}-title"
        @if($descriptionId) aria-describedby="{{ $descriptionId }}" @endif
    >
        <div class="absolute inset-0" @if(filter_var($closeOnOverlay, FILTER_VALIDATE_BOOLEAN)) x-on:click="closeDialog()" @endif></div>
        <section
            x-ref="panel"
            tabindex="-1"
            x-on:keydown.tab="trapTab($event)"
            {{ $attributes->class(['relative w-full max-w-lg rounded-[var(--mms-radius-xl)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-5 shadow-[var(--mms-shadow-lg)] outline-none']) }}
        >
            <div class="flex items-start justify-between gap-4">
                <h2 id="{{ $name }}-title" class="text-lg font-semibold text-[var(--mms-color-text)]">{{ $title }}</h2>
                <button type="button" class="grid size-9 place-items-center rounded-[var(--mms-radius-sm)] text-[var(--mms-color-text-muted)] hover:bg-[var(--mms-color-surface-muted)] hover:text-[var(--mms-color-text)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" x-on:click="closeDialog()" aria-label="Tutup dialog">
                    <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-4">{{ $slot }}</div>
        </section>
        </div>
</div>
