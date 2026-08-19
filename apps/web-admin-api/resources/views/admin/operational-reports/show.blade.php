@extends('layouts.admin')

@section('title', 'Detail Laporan Operasional - '.config('mms.name'))
@section('page_title', 'Detail Laporan Operasional')

@php
    $rupiah = fn (int $value): string => 'Rp '.number_format($value, 0, ',', '.');
@endphp

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="{{ $report->marketingProfile->code }} - {{ $report->resort }}"
            description="{{ $report->report_date->format('d/m/Y') }} {{ $report->report_time }}"
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.home')],
                ['label' => 'Laporan Operasional', 'href' => route('admin.operational-reports.index')],
                ['label' => $report->marketingProfile->code],
            ]"
        >
            <x-slot:actions>
                <x-ui.link-button :href="route('admin.marketing.show', $report->marketingProfile)" variant="outline">Lihat Marketing</x-ui.link-button>
            </x-slot:actions>
        </x-navigation.page-header>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.stat-card label="Storting" :value="$rupiah($report->storting)" />
            <x-ui.stat-card label="Drop" :value="$rupiah($report->drop_amount)" />
            <x-ui.stat-card label="Target" :value="$rupiah($report->total_target_amount)" :helper="$report->total_target_people.' orang'" />
            <x-ui.stat-card label="Tabungan Keluar" :value="$rupiah($report->withdrawal_saving)" />
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <x-ui.card title="Informasi" class="lg:col-span-2">
                <x-data.description-list class="grid-cols-1 sm:grid-cols-2">
                    <x-data.description-item label="Marketing">{{ $report->marketingProfile->code }} - {{ $report->marketingProfile->user->name }}</x-data.description-item>
                    <x-data.description-item label="Resort">{{ $report->resort }}</x-data.description-item>
                    <x-data.description-item label="Hari">{{ $report->day_name->label() }}</x-data.description-item>
                    <x-data.description-item label="Waktu">{{ $report->report_date->format('d/m/Y') }} {{ $report->report_time }}</x-data.description-item>
                    <x-data.description-item label="Sinkronisasi">{{ $report->sync_status->label() }}</x-data.description-item>
                    <x-data.description-item label="Catatan">{{ $report->notes ?? '-' }}</x-data.description-item>
                </x-data.description-list>
            </x-ui.card>

            <x-ui.card title="Nominal">
                <x-data.description-list>
                    <x-data.description-item label="Asuransi">{{ $rupiah($report->insurance_amount) }}</x-data.description-item>
                    <x-data.description-item label="Drop Baru">{{ $rupiah($report->new_drop) }}</x-data.description-item>
                    <x-data.description-item label="Drop Lanjut">{{ $rupiah($report->continued_drop) }}</x-data.description-item>
                </x-data.description-list>
            </x-ui.card>
        </section>

        <x-ui.card title="Target">
            <x-data.description-list class="grid-cols-1 sm:grid-cols-3">
                <x-data.description-item label="Target Sebelumnya">{{ $rupiah($report->previous_target_amount) }} / {{ $report->previous_target_people }} org</x-data.description-item>
                <x-data.description-item label="Target Masuk">{{ $rupiah($report->incoming_target_amount) }} / {{ $report->incoming_target_people }} org</x-data.description-item>
                <x-data.description-item label="Target Keluar">{{ $rupiah($report->outgoing_target_amount) }} / {{ $report->outgoing_target_people }} org</x-data.description-item>
            </x-data.description-list>
        </x-ui.card>

        <section aria-labelledby="attachment-heading">
            <h2 id="attachment-heading" class="mb-3 text-[17px] font-semibold text-[var(--mms-color-text)]">Dokumen Operasional</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($attachmentPanels as $panel)
                    <article class="overflow-hidden rounded-[var(--mms-radius-lg)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]">
                        <div class="border-b border-[var(--mms-color-border)] px-4 py-3">
                            <h3 class="text-[14px] font-semibold text-[var(--mms-color-text)]">{{ $panel['label'] }}</h3>
                        </div>
                        @if ($panel['url'])
                            <img src="{{ $panel['url'] }}" alt="{{ $panel['label'] }}" class="aspect-[4/3] w-full object-cover" loading="lazy">
                            <p class="px-4 py-3 text-[13px] text-[var(--mms-color-text-subtle)]">{{ $panel['caption'] ?: 'Tanpa keterangan' }}</p>
                        @else
                            <p class="p-4 text-[13px] text-[var(--mms-color-text-subtle)]">Belum ada {{ strtolower($panel['label']) }}.</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    </div>
@endsection
