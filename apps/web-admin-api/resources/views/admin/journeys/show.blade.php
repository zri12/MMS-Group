@extends('layouts.admin')

@section('title', 'Detail Perjalanan - '.config('mms.name'))
@section('page_title', 'Detail Perjalanan')

@php
    $dateLabel = fn ($d): string => $d ? $d->format('d/m/Y') : '-';
    $distanceKm = $session->distance_meters !== null
        ? number_format($session->distance_meters / 1000, 2, ',', '.').' km'
        : 'Belum tersedia';
    $statusBg = match ($session->status->value) {
        'Aktif'           => 'bg-[rgba(34,197,94,0.1)] text-[var(--mms-color-success)]',
        'Offline'         => 'bg-[rgba(239,68,68,0.1)] text-[var(--mms-color-danger)]',
        'GPS Tidak Aktif' => 'bg-[rgba(245,158,11,0.1)] text-[var(--mms-color-warning)]',
        default           => 'bg-[rgba(167,175,186,0.1)] text-[var(--mms-color-text-muted)]',
    };
@endphp

@section('content')
    <div class="space-y-6">

        {{-- ── Compact Back Header ── --}}
        <div class="flex flex-wrap items-center gap-3">
            <a
                href="{{ route('admin.journeys.index') }}"
                class="inline-flex min-h-[40px] items-center gap-2 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] px-3 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                aria-label="Kembali ke Riwayat Perjalanan"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                </svg>
                <span class="hidden sm:inline">Riwayat Perjalanan</span>
            </a>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-[12px] text-[var(--mms-color-text-subtle)]">
                <a href="{{ route('admin.home') }}" class="hover:text-[var(--mms-color-text)]">Admin</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('admin.journeys.index') }}" class="hover:text-[var(--mms-color-text)]">Riwayat</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-[var(--mms-color-text)]">{{ $session->marketingProfile->code }}</span>
            </nav>
            <div class="ml-auto flex gap-2">
                <button type="button" class="inline-flex items-center gap-2 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] px-3 py-2 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" data-tracking-map-fit="journey-map">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                    Fit
                </button>
                <button type="button" class="inline-flex items-center gap-2 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] px-3 py-2 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" data-tracking-map-fullscreen="journey-map-shell">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9m11.25-5.25h-4.5m4.5 0v4.5m0-4.5L15 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15m11.25 5.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                    Fullscreen
                </button>
            </div>
        </div>

        {{-- ── Profil Marketing + Status ── --}}
        <section class="overflow-hidden rounded-[16px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-4 sm:p-5">
            <div class="flex items-start gap-4">
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="Foto {{ $session->marketingProfile->user->name }}" loading="lazy" class="size-16 shrink-0 rounded-[12px] object-cover ring-1 ring-white/10 sm:size-20">
                @else
                    <div class="grid size-16 shrink-0 place-items-center rounded-[12px] bg-[var(--mms-color-surface-muted)] text-[22px] font-bold text-[var(--mms-color-primary)] ring-1 ring-white/10 sm:size-20">{{ mb_strtoupper(mb_substr($session->marketingProfile->user->name, 0, 1)) }}</div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] font-bold text-[var(--mms-color-primary)]">{{ $session->marketingProfile->code }}</p>
                    <h1 class="mt-0.5 text-[18px] font-semibold text-[var(--mms-color-text)] sm:text-[22px]">{{ $session->marketingProfile->user->name }}</h1>
                    <p class="mt-1 text-[13px] text-[var(--mms-color-text-muted)]">{{ $session->marketingProfile->area }} · {{ $session->day_name->label() }}, {{ $dateLabel($session->session_date) }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusBg }}">
                            <span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>
                            {{ $session->status->label() }}
                        </span>
                        @if ($session->schedule)
                            <span class="rounded-full border border-[var(--mms-color-border)] px-2.5 py-1 text-[11px] text-[var(--mms-color-text-muted)]">{{ $session->schedule->agenda }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.marketing.show', $session->marketingProfile) }}" class="ml-auto shrink-0 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] px-3 py-2 text-[12px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Profil</a>
            </div>

            {{-- 4 stat mini cards --}}
            <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
                @foreach ([
                    ['label' => 'Mulai',     'value' => $session->started_at?->format('H:i') ?? '-'],
                    ['label' => 'Selesai',   'value' => $session->ended_at?->format('H:i') ?? 'Berjalan'],
                    ['label' => 'Durasi',    'value' => $durationLabel ?? 'Berjalan'],
                    ['label' => 'Kunjungan', 'value' => $session->visit_count],
                ] as $mini)
                    <div class="rounded-[10px] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-center">
                        <p class="text-[15px] font-semibold text-[var(--mms-color-text)]">{{ $mini['value'] }}</p>
                        <p class="mt-0.5 text-[11px] text-[var(--mms-color-text-subtle)]">{{ $mini['label'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-2">
                <div class="rounded-[10px] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-center">
                    <p class="text-[15px] font-semibold text-[var(--mms-color-text)]">{{ $distanceKm }}</p>
                    <p class="mt-0.5 text-[11px] text-[var(--mms-color-text-subtle)]">Jarak Tempuh</p>
                </div>
                <div class="rounded-[10px] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-center">
                    <p class="text-[15px] font-semibold text-[var(--mms-color-text)]">{{ $session->points_count }}</p>
                    <p class="mt-0.5 text-[11px] text-[var(--mms-color-text-subtle)]">Titik Tracking</p>
                </div>
            </div>
        </section>

        {{-- ── Map Route ── --}}
        <section aria-labelledby="journey-map-heading">
            <h2 id="journey-map-heading" class="mb-3 text-[15px] font-semibold text-[var(--mms-color-text)]">Peta Rute Perjalanan</h2>
            <div id="journey-map-shell" class="overflow-hidden rounded-[16px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]">
                <div
                    id="journey-map"
                    class="mms-journey-map"
                    data-tracking-map
                    data-markers="{{ \Illuminate\Support\Js::encode($markers) }}"
                    data-path="{{ \Illuminate\Support\Js::encode($path) }}"
                    aria-label="Peta rute perjalanan {{ $session->marketingProfile->user->name }}"
                ></div>
            </div>
            @if ($points->isEmpty())
                <p class="mt-2 text-center text-[12px] text-[var(--mms-color-text-subtle)]">Titik tracking belum tersedia untuk sesi ini.</p>
            @endif
        </section>

        {{-- ── Timeline Titik (collapsible) ── --}}
        @if ($points->isNotEmpty())
            <section aria-labelledby="journey-timeline-heading">
                <details class="rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]" open>
                    <summary id="journey-timeline-heading" class="flex cursor-pointer select-none list-none items-center justify-between px-4 py-3 text-[14px] font-semibold text-[var(--mms-color-text)] hover:bg-[var(--mms-color-surface-muted)]">
                        <span>Timeline Perjalanan ({{ $points->count() }} titik)</span>
                        <svg class="size-4 text-[var(--mms-color-text-subtle)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                        </svg>
                    </summary>

                    {{-- Mobile: timeline cards --}}
                    <div class="divide-y divide-[var(--mms-color-border)] px-4 pb-4 lg:hidden">
                        @foreach ($points->take(20) as $point)
                            <div class="flex gap-3 py-3">
                                <div class="flex flex-col items-center">
                                    <div class="size-2 rounded-full bg-[var(--mms-color-primary-hover)] mt-1.5"></div>
                                    @if (! $loop->last) <div class="w-px flex-1 bg-[var(--mms-color-border)] mt-1"></div> @endif
                                </div>
                                <div class="min-w-0 pb-1">
                                    <p class="text-[12px] font-semibold text-[var(--mms-color-text)]">{{ $point->recorded_at?->format('H:i:s') }}</p>
                                    <p class="text-[11px] text-[var(--mms-color-text-subtle)]">{{ $point->point_type->label() }}</p>
                                    @if ($point->address)
                                        <p class="mt-1 text-[11px] text-[var(--mms-color-text-muted)] line-clamp-2">{{ $point->address }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @if ($points->count() > 20)
                            <p class="pt-3 text-center text-[12px] text-[var(--mms-color-text-subtle)]">... dan {{ $points->count() - 20 }} titik lainnya</p>
                        @endif
                    </div>

                    {{-- Desktop: compact table --}}
                    <div class="hidden overflow-x-auto border-t border-[var(--mms-color-border)] lg:block">
                        <table class="w-full text-left text-[12px]" aria-label="Timeline titik tracking">
                            <thead class="bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text-subtle)]">
                                <tr>
                                    <th scope="col" class="px-4 py-2.5 font-semibold">Waktu</th>
                                    <th scope="col" class="px-4 py-2.5 font-semibold">Tipe</th>
                                    <th scope="col" class="px-4 py-2.5 font-semibold">Koordinat</th>
                                    <th scope="col" class="px-4 py-2.5 font-semibold">Akurasi</th>
                                    <th scope="col" class="px-4 py-2.5 font-semibold">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--mms-color-border)] text-[var(--mms-color-text-muted)]">
                                @foreach ($points as $point)
                                    <tr class="hover:bg-[var(--mms-color-surface-muted)]">
                                        <td class="px-4 py-2.5 font-medium text-[var(--mms-color-text)]">{{ $point->recorded_at?->format('d/m/Y H:i:s') }}</td>
                                        <td class="px-4 py-2.5"><x-ui.badge variant="neutral">{{ $point->point_type->label() }}</x-ui.badge></td>
                                        <td class="px-4 py-2.5">{{ $point->latitude }}, {{ $point->longitude }}</td>
                                        <td class="px-4 py-2.5">{{ $point->accuracy_meters ?? '—' }}</td>
                                        <td class="px-4 py-2.5">{{ $point->address ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            </section>
        @endif

    </div>
@endsection
