@extends('layouts.admin')

@section('title', 'Detail Prospek - '.config('mms.name'))
@section('page_title', 'Detail Prospek')

@section('content')
    <div class="space-y-6">
        <x-navigation.page-header
            title="{{ $prospect->name }}"
            description="{{ $prospect->business }} - {{ $prospect->resort }}"
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.home')],
                ['label' => 'Prospek', 'href' => route('admin.prospects.index')],
                ['label' => $prospect->name],
            ]"
        />

        <section class="grid gap-4 lg:grid-cols-3">
            <x-ui.card title="Identitas" class="lg:col-span-2">
                <x-data.description-list class="grid-cols-1 sm:grid-cols-2">
                    <x-data.description-item label="Nama">{{ $prospect->name }}</x-data.description-item>
                    <x-data.description-item label="Nomor HP">{{ $prospect->phone }}</x-data.description-item>
                    <x-data.description-item label="Usaha">{{ $prospect->business }}</x-data.description-item>
                    <x-data.description-item label="Status"><x-ui.badge variant="primary">{{ $prospect->status->label() }}</x-ui.badge></x-data.description-item>
                    <x-data.description-item label="Resort">{{ $prospect->resort }}</x-data.description-item>
                    <x-data.description-item label="Marketing">{{ $prospect->marketingProfile->code }} - {{ $prospect->marketingProfile->user->name }}</x-data.description-item>
                    <x-data.description-item label="Tanggal Input">{{ $prospect->input_date->format('d/m/Y') }} {{ $prospect->input_time }}</x-data.description-item>
                    <x-data.description-item label="Sinkronisasi">{{ $prospect->sync_status->label() }}</x-data.description-item>
                </x-data.description-list>
            </x-ui.card>

            <x-ui.card title="Lokasi">
                <p class="text-sm leading-6 text-[var(--mms-color-text-muted)]">{{ $prospect->address }}</p>
                <x-ui.divider />
                <p class="text-xs text-[var(--mms-color-text-muted)]">Koordinat</p>
                <p class="mt-1 text-sm text-[var(--mms-color-text)]">{{ $prospect->latitude ?? '-' }}, {{ $prospect->longitude ?? '-' }}</p>
                <p class="mt-3 text-xs text-[var(--mms-color-text-muted)]">{{ $prospect->location_address ?? '-' }}</p>
            </x-ui.card>
        </section>

        <x-ui.card title="Catatan dan Hasil Awal">
            <x-data.description-list>
                <x-data.description-item label="Hasil Awal">{{ $prospect->initial_visit_result }}</x-data.description-item>
                <x-data.description-item label="Catatan">{{ $prospect->notes ?? '-' }}</x-data.description-item>
                <x-data.description-item label="Anggota Terkait">{{ $prospect->member?->member_number ?? '-' }}</x-data.description-item>
            </x-data.description-list>
        </x-ui.card>

        <x-ui.card title="Riwayat Kunjungan">
            @if ($prospect->visitReports->isEmpty())
                <x-ui.empty-state title="Belum ada kunjungan" description="Riwayat kunjungan untuk prospek ini belum tersedia." />
            @else
                <div class="space-y-3">
                    @foreach ($prospect->visitReports as $visit)
                        <div class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-[var(--mms-color-text)]">{{ $visit->visit_purpose }}</p>
                                    <p class="mt-1 text-xs text-[var(--mms-color-text-muted)]">{{ $visit->visit_date->format('d/m/Y') }} {{ $visit->visit_time }}</p>
                                </div>
                                <x-ui.badge variant="primary">{{ $visit->visit_result->label() }}</x-ui.badge>
                            </div>
                            <p class="mt-3 text-sm text-[var(--mms-color-text-muted)]">{{ $visit->notes ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection
