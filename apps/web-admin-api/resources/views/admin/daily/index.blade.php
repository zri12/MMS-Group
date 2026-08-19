@extends('layouts.admin')

@php use App\Enums\DayName; @endphp

@section('title', config('mms.navigation.labels.daily').' - '.config('mms.name'))
@section('page_title', config('mms.navigation.labels.daily'))

@php
    $currentDay = $selectedDay->value;

    $mainShortcuts = [
        ['label' => 'Tracking Lokasi',     'count' => $counters['tracking_sessions'], 'href' => route('admin.tracking.index', ['date' => $filters['date'], 'day' => $filters['day']]),           'icon' => 'M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z',    'accent' => true],
        ['label' => 'Data Anggota',        'count' => $counters['members'],            'href' => route('admin.members.index', ['day' => $filters['day']]),                                          'icon' => 'M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7 8a7 7 0 0 0-14 0M17 8h4M19 6v4'],
        ['label' => 'Laporan Operasional', 'count' => $counters['operational_reports'],'href' => route('admin.operational-reports.index', ['date_from' => $filters['date'], 'date_to' => $filters['date'], 'day' => $filters['day']]), 'icon' => 'M5 19V5m0 14h15M9 16V9m4 7V7m4 9v-5'],
        ['label' => 'Rekap Operasional',   'count' => $counters['recaps'],             'href' => route('admin.operational-recaps.index', ['date' => $filters['date'], 'day' => $filters['day']]), 'icon' => 'M4 5h16M4 11h16M4 17h16M8 5v14m8-14v14'],
        ['label' => 'Data Prospek',        'count' => $counters['prospects'],           'href' => route('admin.prospects.index', ['day' => $filters['day']]),                                       'icon' => 'M5 5h14v14H5zM8 9h8M8 13h5'],
        ['label' => 'Laporan Kunjungan',   'count' => $counters['visit_reports'],       'href' => route('admin.visit-reports.index'),                                                               'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 4 0H9Zm0 9 2 2 4-4'],
    ];
@endphp

@section('content')
    <div class="space-y-8">

        {{-- ── Day Selector ── --}}
        <section aria-labelledby="daily-day-heading">
            <div class="mb-3 flex items-end justify-between gap-3">
                <div>
                    <h2 id="daily-day-heading" class="text-[16px] font-semibold text-[var(--mms-color-text)]">{{ config('mms.navigation.labels.daily') }}</h2>
                    <p class="mt-0.5 text-[12px] text-[var(--mms-color-text-subtle)]">Pilih hari untuk membuka data operasional.</p>
                </div>
                <span class="text-[12px] text-[var(--mms-color-text-subtle)]">{{ $selectedDate->format('d/m/Y') }}</span>
            </div>

            <div class="grid grid-cols-3 gap-2 sm:grid-cols-6">
                @foreach (DayName::cases() as $dayCase)
                    <a
                        href="{{ route('admin.daily.index', ['day' => $dayCase->value]) }}"
                        class="flex h-10 w-full items-center justify-center rounded-[10px] border px-3 text-[12px] font-semibold transition sm:h-11 sm:text-[13px] {{ $dayCase->value === $currentDay ? 'border-[var(--mms-color-primary)] bg-[var(--mms-color-primary)] text-[var(--mms-color-primary-foreground)]' : 'border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text-muted)] hover:bg-[var(--mms-color-surface-elevated)] hover:text-[var(--mms-color-text)]' }}"
                        aria-current="{{ $dayCase->value === $currentDay ? 'true' : 'false' }}"
                    >
                        {{ $dayCase->label() }}
                    </a>
                @endforeach
            </div>

            {{-- Custom date filter --}}
            <div class="mt-3">
                <form method="GET" action="{{ route('admin.daily.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="day" value="{{ $filters['day'] }}">
                    <label for="daily-date" class="text-[12px] text-[var(--mms-color-text-subtle)]">Tanggal:</label>
                    <input
                        id="daily-date"
                        type="date"
                        name="date"
                        value="{{ $filters['date'] }}"
                        class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-1.5 text-[12px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"
                        onchange="this.form.submit()"
                    />
                    @if ($filters['date'] !== now()->toDateString())
                        <a href="{{ route('admin.daily.index', ['day' => $filters['day']]) }}" class="text-[12px] text-[var(--mms-color-text-subtle)] hover:text-[var(--mms-color-text)]">Hari ini</a>
                    @endif
                </form>
            </div>
        </section>

        {{-- ── 4 Primary Stat Cards ── --}}
        <section aria-labelledby="daily-stats-heading">
            <p id="daily-stats-heading" class="mb-3 text-[12px] font-semibold uppercase tracking-wide text-[var(--mms-color-text-subtle)]">Ringkasan {{ $selectedDay->label() }}, {{ $selectedDate->format('d/m/Y') }}</p>
            <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4 sm:gap-3">
                @foreach ([
                    ['label' => 'Prospek Baru',     'value' => $counters['prospects'],           'href' => route('admin.prospects.index', ['day' => $filters['day']])],
                    ['label' => 'Anggota Masuk',    'value' => $counters['members'],             'href' => route('admin.members.index', ['day' => $filters['day']])],
                    ['label' => 'Laporan Masuk',    'value' => $counters['operational_reports'], 'href' => route('admin.operational-reports.index', ['date_from' => $filters['date'], 'date_to' => $filters['date']])],
                    ['label' => 'Kunjungan',        'value' => $counters['visit_reports'],       'href' => route('admin.visit-reports.index')],
                ] as $card)
                    <a
                        href="{{ $card['href'] }}"
                        class="group rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-3.5 transition hover:border-[var(--mms-color-border-strong)] hover:bg-[var(--mms-color-surface-elevated)] sm:p-4"
                    >
                        <p class="text-[26px] font-semibold text-[var(--mms-color-text)] sm:text-[30px]">{{ $card['value'] }}</p>
                        <p class="mt-1 text-[12px] font-medium text-[var(--mms-color-text-muted)]">{{ $card['label'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ── Akses Cepat Hari Ini ── --}}
        <section aria-labelledby="daily-shortcuts-heading">
            <h2 id="daily-shortcuts-heading" class="mb-3 text-[16px] font-semibold text-[var(--mms-color-text)]">Akses Cepat — {{ $selectedDay->label() }}</h2>
            <div class="grid grid-cols-2 gap-2.5 sm:gap-3 xl:grid-cols-3">
                @foreach ($mainShortcuts as $s)
                    <a
                        href="{{ $s['href'] }}"
                        class="group flex min-h-[88px] flex-col justify-between overflow-hidden rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-3.5 transition hover:bg-[var(--mms-color-surface-elevated)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] sm:p-4 {{ ($s['accent'] ?? false) ? 'hover:border-[var(--mms-color-border-gold)]' : '' }}"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span class="grid size-8 place-items-center rounded-[8px] bg-[var(--mms-color-primary-soft)] text-[var(--mms-color-primary)] sm:size-9">
                                <svg class="size-4 sm:size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}" />
                                </svg>
                            </span>
                            <span class="text-[20px] font-semibold text-[var(--mms-color-primary-hover)] sm:text-[24px]">{{ $s['count'] }}</span>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold leading-snug text-[var(--mms-color-text)] sm:text-[13px]">{{ $s['label'] }}</p>
                            <p class="mt-0.5 text-[11px] text-[var(--mms-color-text-subtle)]">Buka data</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ── Marketing Aktif Hari Ini ── --}}
        @if ($activeTrackingList->isNotEmpty())
            <section aria-labelledby="daily-tracking-heading">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 id="daily-tracking-heading" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Marketing Aktif Tracking</h2>
                    <a href="{{ route('admin.tracking.index', ['date' => $filters['date'], 'day' => $filters['day']]) }}" class="text-[12px] font-semibold text-[var(--mms-color-primary)] hover:text-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] rounded-sm">Lihat Peta</a>
                </div>
                <div class="space-y-2">
                    @foreach ($activeTrackingList as $session)
                        <a
                            href="{{ route('admin.tracking.show', $session) }}"
                            class="flex items-center gap-3 rounded-[12px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-3 transition hover:bg-[var(--mms-color-surface-elevated)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                        >
                            @php $photo = $photoResolver($session->marketingProfile); @endphp
                            @if ($photo)
                                <img src="{{ $photo }}" alt="Foto {{ $session->marketingProfile->user->name }}" loading="lazy" class="size-10 shrink-0 rounded-full object-cover ring-1 ring-white/10">
                            @else
                                <div class="grid size-10 shrink-0 place-items-center rounded-full bg-[var(--mms-color-surface-muted)] text-[14px] font-bold text-[var(--mms-color-primary)] ring-1 ring-white/10">{{ mb_strtoupper(mb_substr($session->marketingProfile->user->name, 0, 1)) }}</div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[14px] font-semibold text-[var(--mms-color-text)]">{{ $session->marketingProfile->user->name }}</p>
                                <p class="text-[12px] text-[var(--mms-color-text-muted)]">{{ $session->marketingProfile->code }} · {{ $session->marketingProfile->area }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[rgba(34,197,94,0.1)] px-2.5 py-1 text-[11px] font-semibold text-[var(--mms-color-success)] shrink-0">
                                <span class="size-1.5 rounded-full bg-[var(--mms-color-success)]" aria-hidden="true"></span>
                                Aktif
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ── Jadwal Hari Ini ── --}}
        @if ($scheduleToday->isNotEmpty())
            <section aria-labelledby="daily-schedule-heading">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h2 id="daily-schedule-heading" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Jadwal Hari Ini</h2>
                    <a href="{{ route('admin.schedules.index', ['date' => $filters['date'], 'day' => $filters['day']]) }}" class="text-[12px] font-semibold text-[var(--mms-color-primary)] hover:text-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] rounded-sm">Lihat Semua</a>
                </div>
                <div class="space-y-2">
                    @foreach ($scheduleToday as $schedule)
                        <div class="flex items-start gap-3 rounded-[12px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-3.5">
                            <div class="min-w-[54px] text-center">
                                <p class="text-[13px] font-semibold text-[var(--mms-color-text)]">{{ $schedule->start_time }}</p>
                                @if ($schedule->end_time)
                                    <p class="text-[11px] text-[var(--mms-color-text-subtle)]">{{ $schedule->end_time }}</p>
                                @endif
                            </div>
                            <div class="h-full w-px bg-[var(--mms-color-border)]" aria-hidden="true"></div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[14px] font-semibold text-[var(--mms-color-text)]">{{ $schedule->agenda }}</p>
                                <p class="text-[12px] text-[var(--mms-color-text-muted)]">{{ $schedule->marketingProfile->user->name }} · {{ $schedule->destination ?? $schedule->area }}</p>
                            </div>
                            <x-ui.badge variant="primary">{{ $schedule->status->label() }}</x-ui.badge>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection
