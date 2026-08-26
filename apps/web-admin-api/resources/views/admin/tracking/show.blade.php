@extends('layouts.admin')

@section('title', 'Detail Tracking - '.config('mms.name'))
@section('page_title', 'Detail Tracking')

@php
    $statusVariant = fn (string $status): string => match ($status) {
        'Aktif' => 'success',
        'Offline' => 'neutral',
        'GPS Tidak Aktif' => 'warning',
        default => 'primary',
    };
@endphp

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="{{ $session->marketingProfile->code }} - {{ $session->marketingProfile->user->name }}"
            description="{{ $session->session_date->format('d/m/Y') }} {{ $session->started_at->format('H:i') }}"
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.home')],
                ['label' => 'Tracking Lokasi', 'href' => route('admin.tracking.index')],
                ['label' => $session->marketingProfile->code],
            ]"
        >
            <x-slot:actions>
                <x-ui.link-button :href="route('admin.tracking.index')" variant="outline">Kembali</x-ui.link-button>
                <x-ui.button type="button" variant="outline" data-tracking-map-fit="tracking-map-detail">Fit Marker</x-ui.button>
                <x-ui.button type="button" variant="outline" data-tracking-map-fullscreen="tracking-map-detail-shell">Fullscreen</x-ui.button>
            </x-slot:actions>
        </x-navigation.page-header>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.stat-card label="Status" :value="$session->status->label()" />
            <x-ui.stat-card label="Jarak" :value="$session->distance_meters === null ? 'Belum tersedia' : number_format($session->distance_meters, 0, ',', '.').' m'" />
            <x-ui.stat-card label="Kunjungan" :value="$session->visit_count" />
            <x-ui.stat-card label="Titik" :value="$session->points_count" />
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">
            <div id="tracking-map-detail-shell" class="overflow-hidden rounded-[var(--mms-radius-lg)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]">
                <div
                    id="tracking-map-detail"
                    class="mms-map-detail"
                    data-tracking-map
                    data-markers="{{ \Illuminate\Support\Js::encode($markers) }}"
                    data-path="{{ \Illuminate\Support\Js::encode($path) }}"
                    aria-label="Peta rute tracking"
                ></div>
            </div>

            <x-ui.card title="Informasi Sesi">
                <x-data.description-list>
                    <x-data.description-item label="Marketing">{{ $session->marketingProfile->code }} - {{ $session->marketingProfile->user->name }}</x-data.description-item>
                    <x-data.description-item label="Hari">{{ $session->day_name->label() }}</x-data.description-item>
                    <x-data.description-item label="Mulai">{{ $session->started_at->format('d/m/Y H:i') }}</x-data.description-item>
                    <x-data.description-item label="Selesai">{{ $session->ended_at?->format('d/m/Y H:i') ?? '-' }}</x-data.description-item>
                    <x-data.description-item label="Status"><x-ui.badge :variant="$statusVariant($session->status->value)">{{ $session->status->label() }}</x-ui.badge></x-data.description-item>
                    <x-data.description-item label="Jadwal">{{ $session->schedule?->agenda ?? '-' }}</x-data.description-item>
                </x-data.description-list>
            </x-ui.card>
        </section>

        <x-ui.card title="Titik Tracking">
            @if ($points->isEmpty())
                <x-ui.empty-state title="Belum ada titik" description="Titik tracking untuk sesi ini belum tersedia." />
            @else
                <x-data.table caption="Titik tracking">
                    <x-data.table-head>
                        <tr>
                            <x-data.table-cell heading="true">Waktu</x-data.table-cell>
                            <x-data.table-cell heading="true">Tipe</x-data.table-cell>
                            <x-data.table-cell heading="true">Koordinat</x-data.table-cell>
                            <x-data.table-cell heading="true">Akurasi</x-data.table-cell>
                            <x-data.table-cell heading="true">Kecepatan</x-data.table-cell>
                            <x-data.table-cell heading="true">Lokasi</x-data.table-cell>
                        </tr>
                    </x-data.table-head>
                    <x-data.table-body>
                        @foreach ($points as $point)
                            <x-data.table-row>
                                <x-data.table-cell>{{ $point->recorded_at->format('d/m/Y H:i:s') }}</x-data.table-cell>
                                <x-data.table-cell><x-ui.badge variant="neutral">{{ $point->point_type->label() }}</x-ui.badge></x-data.table-cell>
                                <x-data.table-cell>{{ $point->latitude }}, {{ $point->longitude }}</x-data.table-cell>
                                <x-data.table-cell>{{ $point->accuracy_meters ?? '-' }}</x-data.table-cell>
                                <x-data.table-cell>{{ $point->speed_mps ?? '-' }}</x-data.table-cell>
                                <x-data.table-cell>{{ $point->address ?? '-' }}</x-data.table-cell>
                            </x-data.table-row>
                        @endforeach
                    </x-data.table-body>
                </x-data.table>
            @endif
        </x-ui.card>
    </div>
@endsection
