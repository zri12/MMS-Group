@extends('layouts.admin')

@section('title', 'Detail Laporan Kunjungan - '.config('mms.name'))
@section('page_title', 'Detail Laporan Kunjungan')

@php($photoUrl = $report->photo_path ? asset('storage/'.$report->photo_path) : null)

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="{{ $report->prospect->name }}"
            description="{{ $report->visit_purpose }} - {{ $report->resort }}"
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.home')],
                ['label' => 'Laporan Kunjungan', 'href' => route('admin.visit-reports.index')],
                ['label' => $report->prospect->name],
            ]"
        >
            <x-slot:actions>
                <x-ui.link-button :href="route('admin.prospects.show', $report->prospect)" variant="outline">Lihat Prospek</x-ui.link-button>
                <x-ui.link-button :href="route('admin.marketing.show', $report->marketingProfile)" variant="outline">Lihat Marketing</x-ui.link-button>
            </x-slot:actions>
        </x-navigation.page-header>

        <section class="grid gap-4 lg:grid-cols-3">
            <x-ui.card title="Kunjungan" class="lg:col-span-2">
                <x-data.description-list class="grid-cols-1 sm:grid-cols-2">
                    <x-data.description-item label="Prospek">{{ $report->prospect->name }}</x-data.description-item>
                    <x-data.description-item label="Marketing">{{ $report->marketingProfile->code }} - {{ $report->marketingProfile->user->name }}</x-data.description-item>
                    <x-data.description-item label="Tujuan">{{ $report->visit_purpose }}</x-data.description-item>
                    <x-data.description-item label="Hasil"><x-ui.badge variant="primary">{{ $report->visit_result->label() }}</x-ui.badge></x-data.description-item>
                    <x-data.description-item label="Status Prospek"><x-ui.badge variant="neutral">{{ $report->prospect_status->label() }}</x-ui.badge></x-data.description-item>
                    <x-data.description-item label="Follow Up">{{ $report->follow_up_date?->format('d/m/Y') ?? '-' }}</x-data.description-item>
                    <x-data.description-item label="Waktu">{{ $report->visit_date->format('d/m/Y') }} {{ $report->visit_time }}</x-data.description-item>
                    <x-data.description-item label="Sinkronisasi">{{ $report->sync_status->label() }}</x-data.description-item>
                </x-data.description-list>
            </x-ui.card>

            <x-ui.card title="Foto">
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="Foto kunjungan {{ $report->prospect->name }}" class="aspect-[4/3] w-full rounded-[var(--mms-radius-md)] object-cover">
                    <p class="mt-3 text-sm text-[var(--mms-color-text-muted)]">{{ $report->photo_caption ?? '-' }}</p>
                    <div class="mt-4">
                        <x-ui.modal name="visit-photo-{{ $report->id }}" title="Foto Kunjungan" class="max-w-3xl">
                            <x-slot:trigger>
                                <x-ui.button type="button" variant="outline">Perbesar Foto</x-ui.button>
                            </x-slot:trigger>
                            <img src="{{ $photoUrl }}" alt="Foto kunjungan {{ $report->prospect->name }}" class="max-h-[75vh] w-full rounded-[var(--mms-radius-md)] object-contain">
                        </x-ui.modal>
                    </div>
                @else
                    <x-ui.empty-state title="Foto belum tersedia" />
                @endif
            </x-ui.card>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <x-ui.card title="Catatan">
                <p class="text-sm leading-6 text-[var(--mms-color-text-muted)]">{{ $report->notes ?? '-' }}</p>
            </x-ui.card>

            <x-ui.card title="Lokasi">
                <p class="text-xs text-[var(--mms-color-text-muted)]">Koordinat</p>
                <p class="mt-1 text-sm text-[var(--mms-color-text)]">{{ $report->latitude ?? '-' }}, {{ $report->longitude ?? '-' }}</p>
                <p class="mt-3 text-sm leading-6 text-[var(--mms-color-text-muted)]">{{ $report->location_address ?? '-' }}</p>
                @if ($report->latitude && $report->longitude)
                    <div class="mt-4">
                        <x-ui.link-button href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank" rel="noopener" variant="outline">Lihat Lokasi</x-ui.link-button>
                    </div>
                @endif
            </x-ui.card>
        </section>
    </div>
@endsection
