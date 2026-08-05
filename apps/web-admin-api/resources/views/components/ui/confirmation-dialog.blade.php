@props([
    'name',
    'title' => 'Konfirmasi tindakan',
    'message' => 'Lanjutkan tindakan ini?',
    'cancelLabel' => 'Batal',
])

@php
    $descriptionId = $name.'-description';
@endphp

<x-ui.modal :name="$name" :title="$title" :description-id="$descriptionId">
    @isset($trigger)
        <x-slot:trigger>
            {{ $trigger }}
        </x-slot:trigger>
    @endisset

    <div id="{{ $descriptionId }}" class="text-sm leading-6 text-[var(--mms-color-text-muted)]">
        @if ($slot->isEmpty())
            {{ $message }}
        @else
            {{ $slot }}
        @endif
    </div>

    <div class="mt-5 flex flex-wrap justify-end gap-3">
        @isset($cancel)
            {{ $cancel }}
        @else
            <x-ui.button type="button" variant="ghost" x-on:click="closeDialog()">
                {{ $cancelLabel }}
            </x-ui.button>
        @endisset

        @isset($confirm)
            {{ $confirm }}
        @endisset
    </div>
</x-ui.modal>
