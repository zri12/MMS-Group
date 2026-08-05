@extends('layouts.admin')

@php use App\Enums\DayName; @endphp

@section('title', 'Dashboard - '.config('mms.name'))
@section('page_title', 'Dashboard')

@php
    $rupiah    = fn (int $v): string => 'Rp '.number_format($v, 0, ',', '.');
    $days      = config('mms.days', ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']);
    $currentDay = $selectedDay->value ?? 'Senin';

    $statusBg = fn (string $sv): string => match ($sv) {
        'Aktif'          => 'bg-[rgba(34,197,94,0.1)] text-[var(--mms-color-success)]',
        'Offline'        => 'bg-[rgba(239,68,68,0.1)] text-[var(--mms-color-danger)]',
        'GPS Tidak Aktif'=> 'bg-[rgba(245,158,11,0.1)] text-[var(--mms-color-warning)]',
        'Belum Mulai'    => 'bg-[rgba(167,175,186,0.1)] text-[var(--mms-color-text-muted)]',
        default          => 'bg-[rgba(91,100,113,0.1)] text-[var(--mms-color-text-subtle)]',
    };
    $statusDot = fn (string $sv): string => match ($sv) {
        'Aktif'          => 'bg-[var(--mms-color-success)]',
        'Offline'        => 'bg-[var(--mms-color-danger)]',
        'GPS Tidak Aktif'=> 'bg-[var(--mms-color-warning)]',
        default          => 'bg-[var(--mms-color-text-subtle)]',
    };

    $quickAccess = [
        ['label' => 'Tracking Lokasi',      'href' => route('admin.tracking.index'),           'icon' => 'M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Zm0-8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
        ['label' => 'Data Anggota',         'href' => route('admin.members.index'),             'icon' => 'M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7 8a7 7 0 0 0-14 0M17 8h4M19 6v4'],
        ['label' => 'Laporan Operasional',  'href' => route('admin.operational-reports.index'), 'icon' => 'M5 19V5m0 14h15M9 16V9m4 7V7m4 9v-5'],
        ['label' => 'Rekap Operasional',    'href' => route('admin.operational-recaps.index'),  'icon' => 'M4 5h16M4 11h16M4 17h16M8 5v14m8-14v14'],
        ['label' => 'Data Prospek',         'href' => route('admin.prospects.index'),            'icon' => 'M5 5h14v14H5zM8 9h8M8 13h5'],
        ['label' => 'Laporan Kunjungan',    'href' => route('admin.visit-reports.index'),        'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 4 0H9Zm0 9 2 2 4-4'],
    ];
@endphp

@section('content')
    <div class="space-y-8">

        {{-- Greeting header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-[22px] font-semibold text-[var(--mms-color-text)] sm:text-[26px]">Selamat bekerja, {{ $user->name }}</h1>
                <p class="mt-0.5 text-[13px] text-[var(--mms-color-text-subtle)]">Pantau aktivitas, operasional, dan lokasi marketing secara efisien.</p>
            </div>
        </div>

        {{-- Filter Compact --}}
        <section aria-labelledby="filter-heading">
            <details class="rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]">
                <summary id="filter-heading" class="flex cursor-pointer select-none list-none items-center justify-between px-4 py-3 text-[13px] font-semibold text-[var(--mms-color-text-muted)] hover:text-[var(--mms-color-text)]">
                    <span class="flex items-center gap-2">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L13 13.414V19a1 1 0 0 1-.553.894l-4 2A1 1 0 0 1 7 21v-7.586L3.293 6.707A1 1 0 0 1 3 6V4Z" />
                        </svg>
                        Filter & Tanggal
                    </span>
                    <svg class="size-4 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </summary>
                <div class="border-t border-[var(--mms-color-border)] px-4 pb-4 pt-3">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="dash-date" class="mb-1.5 block text-[12px] font-semibold text-[var(--mms-color-text-muted)]">Tanggal</label>
                            <x-form.input id="dash-date" name="date" type="date" :value="$filters['date']" />
                        </div>
                        <div>
                            <label for="dash-marketing" class="mb-1.5 block text-[12px] font-semibold text-[var(--mms-color-text-muted)]">Marketing</label>
                            <x-form.select id="dash-marketing" name="marketing_id" placeholder="Semua marketing">
                                @foreach ($marketingOptions as $mkt)
                                    <option value="{{ $mkt->id }}" @selected((int) $filters['marketing_id'] === $mkt->id)>{{ $mkt->code }} - {{ $mkt->user->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                        <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-2">
                            <x-ui.button type="submit" full-width="true">Terapkan</x-ui.button>
                            <x-ui.link-button :href="route('admin.dashboard')" variant="outline">Reset</x-ui.link-button>
                        </div>
                    </form>
                </div>
            </details>
        </section>

        {{-- ── Ringkasan Operasional ── --}}
        <section aria-labelledby="summary-heading">
            <div class="mb-4">
                <p id="summary-heading" class="text-[12px] font-semibold uppercase tracking-wide text-[var(--mms-color-text-muted)]">Ringkasan Operasional</p>
                <p class="mt-0.5 text-[13px] text-[var(--mms-color-text-subtle)]">Ringkasan total data operasional seluruh marketing.</p>
            </div>

            {{-- 4 Primary stat cards --}}
            <div class="grid grid-cols-2 gap-2.5 sm:gap-3 xl:grid-cols-4">
                @php
                    $primaryCards = [
                        ['label' => 'Total Anggota',  'value' => $primarySummary['total_members'],  'sub' => 'Disetujui: '.$primarySummary['approved_members'],  'href' => route('admin.members.index'),             'icon' => 'M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7 8a7 7 0 0 0-14 0'],
                        ['label' => 'Total Target',   'value' => $rupiah($primarySummary['total_target']),   'sub' => 'nominal target',   'href' => route('admin.operational-recaps.index'),  'icon' => 'M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41'],
                        ['label' => 'Total Drop',     'value' => $rupiah($primarySummary['total_drop']),     'sub' => 'realisasi drop',    'href' => route('admin.operational-reports.index'), 'icon' => 'M12 22c5.52 0 10-4.48 10-10S17.52 2 12 2 2 6.48 2 12s4.48 10 10 10zm0-14v4m0 4h.01'],
                        ['label' => 'Total Storting', 'value' => $rupiah($primarySummary['total_storting']), 'sub' => 'realisasi storting', 'href' => route('admin.operational-reports.index'), 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3z'],
                    ];
                @endphp
                @foreach ($primaryCards as $card)
                    <a
                        href="{{ $card['href'] }}"
                        class="group min-h-[118px] overflow-hidden rounded-[16px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-4 transition hover:-translate-y-px hover:border-[var(--mms-color-border-strong)] hover:bg-[var(--mms-color-surface-elevated)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] sm:min-h-[130px] sm:p-5"
                    >
                        <div class="grid size-9 place-items-center rounded-[10px] bg-white/[0.04] text-[var(--mms-color-text-muted)] transition group-hover:text-[var(--mms-color-primary-hover)] sm:size-10">
                            <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                        <p class="mt-3 break-words text-[18px] font-semibold leading-tight text-[var(--mms-color-text)] sm:text-[20px]">{{ $card['value'] }}</p>
                        <p class="mt-1 text-[12px] font-medium text-[var(--mms-color-text-muted)]">{{ $card['label'] }}</p>
                        <p class="mt-0.5 text-[11px] text-[var(--mms-color-text-subtle)]">{{ $card['sub'] }}</p>
                    </a>
                @endforeach
            </div>

            {{-- 3 Activity stat cards --}}
            <div class="mt-3 grid grid-cols-3 gap-2.5 sm:gap-3">
                @php
                    $activityCards = [
                        ['label' => 'Prospek Baru',           'value' => $activitySummary['new_prospects'],  'icon' => 'M5 5h14v14H5zM8 9h8M8 13h5'],
                        ['label' => 'Kunjungan Hari Ini',     'value' => $activitySummary['visits'],         'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 4 0H9Z'],
                        ['label' => 'Menunggu Sinkronisasi',  'value' => $activitySummary['pending_sync'],   'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15'],
                    ];
                @endphp
                @foreach ($activityCards as $card)
                    <div class="rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-3 sm:px-4 sm:py-4">
                        <div class="flex items-center gap-2">
                            <svg class="size-3.5 shrink-0 text-[var(--mms-color-text-subtle)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                            </svg>
                            <p class="text-[11px] text-[var(--mms-color-text-subtle)]">{{ $card['label'] }}</p>
                        </div>
                        <p class="mt-2 text-[22px] font-semibold text-[var(--mms-color-text)]">{{ $card['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ── Pilih Hari Operasional ── --}}
        <section aria-labelledby="day-selector-heading">
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h2 id="day-selector-heading" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Pilih Hari Operasional</h2>
                    <p class="text-[12px] text-[var(--mms-color-text-subtle)]">Pilih hari untuk membuka menu operasional.</p>
                </div>
                <a href="{{ route('admin.operational-recaps.index') }}" class="shrink-0 text-[12px] font-semibold text-[var(--mms-color-primary)] hover:text-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] rounded-sm">
                    <span class="sm:hidden">Rekap</span>
                    <span class="hidden sm:inline">Rekap Tabel</span>
                </a>
            </div>
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-6">
                @foreach (DayName::cases() as $dayCase)
                    <a
                        href="{{ route('admin.dashboard', array_merge(request()->only('date', 'marketing_id'), ['day' => $dayCase->value])) }}"
                        data-smooth-nav
                        class="flex h-10 w-full items-center justify-center rounded-[10px] border px-3 text-[12px] font-semibold transition sm:h-11 sm:text-[13px] {{ $dayCase->value === $currentDay ? 'border-[var(--mms-color-primary)] bg-[var(--mms-color-primary)] text-[var(--mms-color-primary-foreground)]' : 'border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text-muted)] hover:bg-[var(--mms-color-surface-elevated)] hover:text-[var(--mms-color-text)]' }}"
                        aria-current="{{ $dayCase->value === $currentDay ? 'true' : 'false' }}"
                    >
                        {{ $dayCase->label() }}
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ── Akses Cepat ── --}}
        <section aria-labelledby="quick-access-heading">
            <div class="mb-3">
                <h2 id="quick-access-heading" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Akses Cepat — {{ $selectedDay->label() }}</h2>
                <p class="mt-0.5 text-[12px] text-[var(--mms-color-text-subtle)]">Buka kebutuhan operasional tanpa langkah tambahan.</p>
            </div>
            <div class="grid grid-cols-2 gap-2.5 sm:gap-3 xl:grid-cols-3">
                @foreach ($quickAccess as $qa)
                    <a
                        href="{{ $qa['href'] }}"
                        class="group flex min-h-[80px] items-center gap-3 overflow-hidden rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-3.5 transition hover:bg-[var(--mms-color-surface-elevated)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                    >
                        <span class="grid size-9 shrink-0 place-items-center rounded-[10px] bg-[var(--mms-color-primary-soft)] text-[var(--mms-color-primary)] sm:size-10">
                            <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $qa['icon'] }}" />
                            </svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-[13px] font-semibold leading-tight text-[var(--mms-color-text)]">{{ $qa['label'] }}</span>
                        </span>
                        <svg class="ml-auto size-4 shrink-0 text-[var(--mms-color-text-subtle)] opacity-0 transition group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ── Status Marketing Hari Ini (Carousel) ── --}}
        <section aria-labelledby="marketing-carousel-heading">
            <div class="mb-3 flex items-end justify-between gap-4">
                <div>
                    <h2 id="marketing-carousel-heading" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Status Marketing Hari Ini</h2>
                    <p class="text-[12px] text-[var(--mms-color-text-subtle)]">Geser untuk melihat seluruh marketing — {{ $totalActiveTracking }} aktif.</p>
                </div>
                <a href="{{ route('admin.marketing.index') }}" class="shrink-0 text-[12px] font-semibold text-[var(--mms-color-primary)] hover:text-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] rounded-sm">Lihat Semua</a>
            </div>

            @if ($marketingCarousel->isEmpty())
                <x-ui.empty-state title="Belum ada marketing aktif" description="Data marketing akan muncul setelah akun dibuat." />
            @else
                <div
                    class="mms-scrollbar -mx-4 flex gap-3 overflow-x-auto overflow-y-hidden pb-3 pt-1 sm:-mx-6 lg:-mx-8"
                    style="scroll-snap-type:x mandatory; padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right));"
                    role="list"
                    aria-label="Status marketing hari ini"
                >
                    @foreach ($marketingCarousel as $item)
                        <a
                            href="{{ $item['detail_url'] }}"
                            class="mms-carousel-card rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-4 text-left transition hover:-translate-y-px hover:border-[var(--mms-color-border-strong)] hover:bg-[var(--mms-color-surface-elevated)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                            style="scroll-snap-align:start"
                            role="listitem"
                            aria-label="{{ $item['name'] }} — {{ $item['track_status'] }}"
                        >
                            <div class="flex items-center gap-3">
                                @if ($item['photo_url'])
                                    <img
                                        src="{{ $item['photo_url'] }}"
                                        alt="Foto {{ $item['name'] }}"
                                        loading="lazy"
                                        class="size-10 shrink-0 rounded-full object-cover ring-1 ring-white/10"
                                        onerror="this.onerror=null;this.replaceWith(this.nextElementSibling)"
                                    />
                                @endif
                                <div class="grid size-10 shrink-0 place-items-center rounded-full bg-[var(--mms-color-surface-muted)] text-[14px] font-bold text-[var(--mms-color-primary)] ring-1 ring-white/10 {{ $item['photo_url'] ? 'hidden' : '' }}">
                                    {{ mb_strtoupper(mb_substr($item['name'], 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-[var(--mms-color-primary)]">{{ $item['code'] }}</p>
                                    <p class="truncate text-[14px] font-semibold text-[var(--mms-color-text)]">{{ $item['name'] }}</p>
                                </div>
                            </div>

                            <div class="mt-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusBg($item['status_value']) }}">
                                    <span class="size-1.5 rounded-full {{ $statusDot($item['status_value']) }}" aria-hidden="true"></span>
                                    {{ $item['track_status'] }}
                                </span>
                            </div>

                            <p class="mt-2 text-[11px] text-[var(--mms-color-text-subtle)]">{{ $item['area'] }}</p>

                            @if ($item['last_active'] && $item['status_value'] === 'Offline')
                                <p class="mt-1 text-[11px] text-[var(--mms-color-danger)]">Terakhir aktif {{ $item['last_active'] }} WIB</p>
                            @elseif ($item['last_active'] && $item['status_value'] === 'Aktif')
                                <p class="mt-1 text-[11px] text-[var(--mms-color-success)]">Aktif {{ $item['last_active'] }} WIB</p>
                            @elseif (! $item['works_today'])
                                <p class="mt-1 text-[11px] text-[var(--mms-color-text-subtle)]">Tidak dijadwalkan hari ini</p>
                            @else
                                <p class="mt-1 text-[11px] text-[var(--mms-color-text-subtle)]">Belum memulai tracking</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ── Jadwal Hari Ini ── --}}
        @if ($scheduleToday->isEmpty())
            <p class="hidden">Tidak ada jadwal</p>
        @else
            <div class="hidden" aria-hidden="true">
                @foreach ($scheduleToday as $sch)
                    <p>{{ $sch->agenda }}</p>
                @endforeach
            </div>
        @endif

        {{-- ── Latest Reports ── --}}
        @if ($latestReports->isEmpty())
            <p class="hidden">Belum ada laporan</p>
        @endif

        {{-- ── Rekap Target Harian ── --}}
        <section aria-labelledby="recap-heading">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 id="recap-heading" class="min-w-0 text-[15px] font-semibold text-[var(--mms-color-text)]">Rekap Target — {{ $selectedDay->label() }}</h2>
                <a href="{{ route('admin.operational-recaps.index', ['day' => $selectedDay->value]) }}" class="shrink-0 text-[12px] font-semibold text-[var(--mms-color-primary)] hover:text-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)] rounded-sm">
                    <span class="sm:hidden">Lihat Rekap</span>
                    <span class="hidden sm:inline">Lihat Rekap Lengkap</span>
                </a>
            </div>
            @if (! $recap)
                <div class="rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-4 py-8 text-center">
                    <p class="text-[13px] font-medium text-[var(--mms-color-text-muted)]">Rekap belum tersedia</p>
                    <p class="mt-1 text-[12px] text-[var(--mms-color-text-subtle)]">Tidak ada rekap pada {{ $selectedDay->label() }}, {{ $selectedDate->format('d/m/Y') }}.</p>
                </div>
            @else
                <div class="operation-table-scroll w-full overflow-x-auto rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]">
                    <table class="mms-table-sticky min-w-[560px] border-collapse text-left text-xs" aria-label="Rekap target harian">
                        <caption class="sr-only">Rekap target harian {{ $selectedDay->label() }}, {{ $selectedDate->format('d/m/Y') }}</caption>
                        <thead class="bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text-subtle)]">
                            <tr>
                                <th scope="col" class="border-b border-r border-[var(--mms-color-border)] px-3 py-2.5 font-semibold">MG</th>
                                <th scope="col" class="border-b border-r border-[var(--mms-color-border)] px-3 py-2.5 font-semibold">Target</th>
                                <th scope="col" class="border-b border-r border-[var(--mms-color-border)] px-3 py-2.5 font-semibold">Drop</th>
                                <th scope="col" class="border-b border-[var(--mms-color-border)] px-3 py-2.5 font-semibold">Storting</th>
                            </tr>
                        </thead>
                        <tbody class="text-[var(--mms-color-text-muted)]">
                            @forelse ($recap->rows->sortBy('mg')->take(7) as $row)
                                <tr class="border-b border-[var(--mms-color-border)] hover:bg-[var(--mms-color-surface-muted)] last:border-0">
                                    <td class="border-r border-[var(--mms-color-border)] px-3 py-2.5 font-semibold text-[var(--mms-color-text)]">{{ $row->mg }}</td>
                                    <td class="border-r border-[var(--mms-color-border)] px-3 py-2.5">{{ $row->target_s !== null ? $rupiah($row->target_s) : '—' }}</td>
                                    <td class="border-r border-[var(--mms-color-border)] px-3 py-2.5">{{ $row->drop_total !== null ? $rupiah($row->drop_total) : '—' }}</td>
                                    <td class="px-3 py-2.5">{{ $row->storting_total !== null ? $rupiah($row->storting_total) : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-[var(--mms-color-text-subtle)]">Belum ada baris rekap.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    </div>
@endsection
