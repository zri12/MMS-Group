@extends('layouts.admin')

@section('title', 'Dashboard - '.config('mms.name'))
@section('page_title', 'Dashboard')

@section('content')
    @php
        $rupiah = fn (?int $value): string => $value === null ? 'Belum tersedia' : 'Rp '.number_format($value, 0, ',', '.');
        $icons = [
            'arrow-down' => 'M12 3v14m0 0 5-5m-5 5-5-5M5 21h14',
            'wallet' => 'M4 7h15a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a3 3 0 0 1-3-3V6a3 3 0 0 1 3-3h13v4M16 14h.01',
            'refresh' => 'M20 11a8 8 0 0 0-15-3M4 4v4h4m-4 5a8 8 0 0 0 15 3m1 4v-4h-4',
            'arrow-in' => 'M12 3v13m0 0 5-5m-5 5-5-5M5 21h14',
            'arrow-out' => 'M12 21V8m0 0 5 5m-5-5-5 5M5 3h14',
            'target' => 'M12 3a9 9 0 1 0 9 9M12 7a5 5 0 1 0 5 5m-5-2 .01 0',
            'member-in' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0M19 8v6m3-3h-6',
            'member-out' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0M19 11h3',
            'members' => 'M16 20v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m6-9a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm9 9v-1a4 4 0 0 0-3-3.87',
        ];
    @endphp

    <div class="mms-dashboard space-y-8">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[.12em] text-[var(--mms-color-primary)]">Ringkasan Operasional</p>
                <h1 class="mt-1 text-[22px] font-semibold text-[var(--mms-color-text)]">Dashboard Admin</h1>
                <p class="mt-1 text-[13px] text-[var(--mms-color-text-subtle)]">{{ $selectedDay->label() }}, {{ $selectedDate->translatedFormat('d F Y') }}</p>
            </div>
            <a href="{{ route('admin.operational-recaps.index') }}" class="mms-text-link">Buka Rekap Operasional</a>
        </header>

        <section aria-label="Metrik operasional">
            <div class="mms-dashboard-metrics">
                @foreach ($metricCards as $card)
                    <article class="mms-dashboard-card {{ $card['value'] === null ? 'mms-dashboard-card-unavailable' : '' }}">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-[12px] font-medium text-[var(--mms-color-text-muted)]">{{ $card['label'] }}</p>
                            <span class="grid size-8 shrink-0 place-items-center rounded-[var(--mms-radius-sm)] bg-[var(--mms-color-primary-soft)] text-[var(--mms-color-primary)]" aria-hidden="true">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$card['icon']] }}" /></svg>
                            </span>
                        </div>
                        <p class="mms-dashboard-metric-value" title="{{ $card['value'] === null ? 'Belum tersedia' : ($card['type'] === 'currency' ? $rupiah($card['value']) : $card['value'].' orang') }}">{{ $card['value'] === null ? 'Belum tersedia' : ($card['type'] === 'currency' ? $compactCurrency($card['value']) : $card['value']) }}</p>
                        <p class="mms-dashboard-metric-helper">{{ $card['value'] === null ? 'Menunggu definisi bisnis.' : ($card['type'] === 'count' ? 'orang' : 'Akumulasi periode terpilih.') }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section aria-labelledby="attachment-heading">
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h2 id="attachment-heading" class="text-[17px] font-semibold text-[var(--mms-color-text)]">Dokumen Operasional</h2>
                    <p class="mt-1 text-[13px] text-[var(--mms-color-text-subtle)]">Foto terbaru yang terkait laporan operasional.</p>
                </div>
                <a href="{{ route('admin.operational-reports.index') }}" class="mms-text-link shrink-0">Lihat Semua</a>
            </div>
            <div class="mms-photo-panels">
                @foreach (['disbursement' => 'Foto Pencairan', 'transfer_proof' => 'Foto Bukti Transfer'] as $type => $title)
                    <article class="mms-photo-panel">
                        <h3 class="text-[15px] font-semibold text-[var(--mms-color-text)]">{{ $title }}</h3>
                        @if ($attachmentPanels[$type]->isEmpty())
                            <div class="flex min-h-40 items-center justify-center rounded-[var(--mms-radius-md)] border border-dashed border-[var(--mms-color-border-strong)] bg-[var(--mms-color-background-subtle)] p-5 text-center">
                                <p class="text-[13px] text-[var(--mms-color-text-subtle)]">Belum ada {{ strtolower($title) }}.</p>
                            </div>
                        @else
                            <div class="mt-4 grid grid-cols-3 gap-2">
                                @foreach ($attachmentPanels[$type] as $attachment)
                                    <a href="{{ $attachment['report_url'] }}" class="group min-w-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">
                                        <img src="{{ $attachment['url'] }}" alt="{{ $title }} {{ $attachment['pdl_code'] }}" class="aspect-[4/3] w-full rounded-[var(--mms-radius-sm)] object-cover ring-1 ring-[var(--mms-color-border)]" loading="lazy">
                                        <p class="mt-2 truncate text-[11px] font-semibold text-[var(--mms-color-text)]">{{ $attachment['pdl_code'] }} · {{ $attachment['pdl_name'] }}</p>
                                        <p class="truncate text-[10px] text-[var(--mms-color-text-subtle)]">{{ $attachment['caption'] ?: 'Tanpa keterangan' }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section aria-labelledby="pdl-heading">
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h2 id="pdl-heading" class="text-[17px] font-semibold text-[var(--mms-color-text)]">PDL</h2>
                    <p class="mt-1 text-[13px] text-[var(--mms-color-text-subtle)]">Ringkasan akun PDL yang aktif.</p>
                </div>
                <a href="{{ route('admin.marketing.index') }}" class="mms-text-link shrink-0">Pengaturan PDL</a>
            </div>
            @if ($pdlProfiles->isEmpty())
                <x-ui.empty-state title="Belum ada PDL" description="PDL akan tampil setelah akun dibuat dari Pengaturan PDL." />
            @else
                <div class="mms-pdl-grid">
                    @foreach ($pdlProfiles as $pdl)
                        <a href="{{ $pdl['detail_url'] }}" class="mms-pdl-card">
                            <div class="flex items-center gap-3">
                                @if ($pdl['photo_url'])
                                    <img src="{{ $pdl['photo_url'] }}" alt="Foto {{ $pdl['name'] }}" class="size-10 rounded-full object-cover" loading="lazy">
                                @else
                                    <span class="grid size-10 place-items-center rounded-full bg-[var(--mms-color-primary-soft)] text-sm font-bold text-[var(--mms-color-primary)]">{{ mb_strtoupper(mb_substr($pdl['name'], 0, 1)) }}</span>
                                @endif
                                <span class="min-w-0"><span class="block text-[11px] font-bold text-[var(--mms-color-primary)]">{{ $pdl['code'] }}</span><span class="block truncate text-[14px] font-semibold text-[var(--mms-color-text)]">{{ $pdl['name'] }}</span><span class="block truncate text-[11px] text-[var(--mms-color-text-subtle)]">{{ $pdl['area'] }}</span></span>
                            </div>
                            <dl class="mt-4 grid grid-cols-3 gap-2 border-t border-[var(--mms-color-border)] pt-3 text-[10px] text-[var(--mms-color-text-subtle)]"><div><dt>Target</dt><dd class="mt-1 font-semibold text-[var(--mms-color-text)]">{{ $rupiah($pdl['target_total']) }}</dd></div><div><dt>Drop</dt><dd class="mt-1 font-semibold text-[var(--mms-color-text)]">{{ $rupiah($pdl['drop_total']) }}</dd></div><div><dt>Storting</dt><dd class="mt-1 font-semibold text-[var(--mms-color-text)]">{{ $rupiah($pdl['storting_total']) }}</dd></div></dl>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="mms-tracking-overview" aria-labelledby="tracking-heading">
            <div>
                <p class="text-[12px] font-semibold uppercase tracking-[.12em] text-[var(--mms-color-primary)]">Tracking PDL</p>
                <h2 id="tracking-heading" class="mt-1 text-[18px] font-semibold text-[var(--mms-color-text)]">Lokasi dan perjalanan PDL</h2>
                <p class="mt-1 text-[13px] text-[var(--mms-color-text-subtle)]">{{ $activeTrackingCount }} PDL aktif pada periode terpilih.</p>
            </div>
            <a href="{{ route('admin.tracking.index') }}" class="mms-primary-link">Buka Tracking PDL</a>
        </section>
    </div>
@endsection
