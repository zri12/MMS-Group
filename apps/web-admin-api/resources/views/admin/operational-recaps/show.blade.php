@extends('layouts.admin')

@section('title', 'Detail Rekap - '.config('mms.name'))
@section('page_title', config('mms.navigation.labels.operational_recaps'))

@php
    $rupiah = fn (?int $value): string => $value === null ? '-' : number_format($value, 0, ',', '.');
@endphp

@section('content')
    <div class="space-y-6" x-data="{ fullscreen: false }">
        <x-navigation.page-header
            title="{{ $recap->report_number }}"
            description="{{ $recap->day_name->label() }} - {{ $recap->recap_date->format('d/m/Y') }}"
            :breadcrumbs="[['label' => 'Admin', 'href' => route('admin.home')], ['label' => config('mms.navigation.labels.operational_recaps'), 'href' => route('admin.operational-recaps.index')], ['label' => $recap->report_number]]"
        >
            <x-slot:actions>
                <x-ui.button type="button" variant="outline" x-on:click="fullscreen = true">Perbesar Tabel</x-ui.button>
            </x-slot:actions>
        </x-navigation.page-header>

        <div x-bind:class="fullscreen ? 'fixed inset-0 z-[1200] overflow-auto bg-[var(--mms-color-background)] p-4' : ''">
            <div class="mb-4 flex items-center justify-between gap-3" x-show="fullscreen" x-cloak>
                <h2 class="text-lg font-semibold text-[var(--mms-color-text)]">Rekap Operasional</h2>
                <x-ui.button type="button" variant="outline" x-on:click="fullscreen = false">Tutup</x-ui.button>
            </div>
            <div class="operation-table-scroll w-full overflow-x-auto rounded-[var(--mms-radius-lg)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]">
                <table class="mms-table-sticky min-w-[1680px] border-collapse text-left text-xs">
                    <caption class="sr-only">Tabel rekap operasional</caption>
                    <thead class="bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text)]">
                        <tr>
                            <th rowspan="2" class="border border-[var(--mms-color-border)] px-3 py-3">No</th>
                            <th rowspan="2" class="border border-[var(--mms-color-border)] px-3 py-3">Hari</th>
                            <th rowspan="2" class="border border-[var(--mms-color-border)] px-3 py-3">Tanggal</th>
                            <th rowspan="2" class="border border-[var(--mms-color-border)] px-3 py-3">MG</th>
                            <th colspan="4" class="border border-[var(--mms-color-border)] px-3 py-3 text-center">Anggota</th>
                            <th colspan="4" class="border border-[var(--mms-color-border)] px-3 py-3 text-center">Target</th>
                            <th colspan="3" class="border border-[var(--mms-color-border)] px-3 py-3 text-center">Drop</th>
                            <th colspan="3" class="border border-[var(--mms-color-border)] px-3 py-3 text-center">Storting</th>
                            <th rowspan="2" class="border border-[var(--mms-color-border)] px-3 py-3">%</th>
                            <th colspan="2" class="border border-[var(--mms-color-border)] px-3 py-3 text-center">Sirkulasi</th>
                            <th rowspan="2" class="border border-[var(--mms-color-border)] px-3 py-3">Diikuti Oleh</th>
                            <th rowspan="2" class="border border-[var(--mms-color-border)] px-3 py-3">Kas Pagi</th>
                        </tr>
                        <tr>
                            @foreach (['L', 'M', 'K', 'S', 'Lalu', 'MSK', 'KLR', 'S', 'Lalu', 'Kini', 'Total', 'Lalu', 'Kini', 'Total', 'Lalu', 'Sekarang'] as $heading)
                                <th class="border border-[var(--mms-color-border)] px-3 py-2">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-[var(--mms-color-text-muted)]">
                        @forelse ($recap->rows->sortBy('mg') as $row)
                            <tr class="hover:bg-[var(--mms-color-surface-muted)]">
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $recap->day_name->label() }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $recap->recap_date->format('d/m/Y') }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2 font-semibold text-[var(--mms-color-text)]">{{ $row->mg }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $row->members_l }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $row->members_m }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $row->members_k }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $row->members_s }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->target_previous) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->target_incoming) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->target_outgoing) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->target_s) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->drop_previous) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->drop_current) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->drop_total) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->storting_previous) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->storting_current) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->storting_total) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $row->percentage === null ? '-' : $row->percentage.'%' }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->previous_circulation) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->current_circulation) }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $row->followed_by }}</td>
                                <td class="border border-[var(--mms-color-border)] px-3 py-2">{{ $rupiah($row->morning_cash) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="23" class="px-4 py-8 text-center text-[var(--mms-color-text-muted)]">Belum ada baris rekap.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
