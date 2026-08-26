@extends('layouts.admin')

@section('title', 'Tracking PDL - '.config('mms.name'))
@section('page_title', 'Tracking PDL')

@php
    $statusBg = fn (string $sv): string => match ($sv) {
        'Aktif'           => 'bg-[rgba(34,197,94,0.1)] text-[var(--mms-color-success)]',
        'Offline'         => 'bg-[rgba(239,68,68,0.1)] text-[var(--mms-color-danger)]',
        'GPS Tidak Aktif' => 'bg-[rgba(245,158,11,0.1)] text-[var(--mms-color-warning)]',
        'Belum Mulai'     => 'bg-[rgba(167,175,186,0.1)] text-[var(--mms-color-text-muted)]',
        default           => 'bg-[rgba(91,100,113,0.1)] text-[var(--mms-color-text-subtle)]',
    };
    $statusDot = fn (string $sv): string => match ($sv) {
        'Aktif'           => 'bg-[var(--mms-color-success)]',
        'Offline'         => 'bg-[var(--mms-color-danger)]',
        'GPS Tidak Aktif' => 'bg-[var(--mms-color-warning)]',
        default           => 'bg-[var(--mms-color-text-subtle)]',
    };
    $activeFilterCount = collect([
        'date' => $filters['date'] !== now()->toDateString() ? $filters['date'] : null,
        'day' => $filters['day'],
        'status' => $filters['status'],
        'marketing_id' => $filters['marketing_id'],
    ])->filter(fn ($v) => $v !== '' && $v !== null)->count();
@endphp

@section('content')
    <div
        class="space-y-4"
        x-data="{
            filterOpen: false,
            pickerOpen: false,
            selectedMarketing: {{ $filters['marketing_id'] ? 'true' : 'false' }},
        }"
        x-on:keydown.escape.window="filterOpen = false; pickerOpen = false"
        x-effect="document.body.classList.toggle('overflow-hidden', filterOpen || pickerOpen)"
    >

        {{-- Page Header --}}
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h1 class="text-[22px] font-semibold text-[var(--mms-color-text)] sm:text-[24px]">Tracking PDL</h1>
                <p class="mt-0.5 text-[12px] text-[var(--mms-color-text-subtle)]">Pantau posisi real-time semua marketing. Update setiap {{ $pollingSeconds }}s.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ request()->fullUrl() }}" class="inline-flex min-h-[40px] items-center gap-2 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15"/></svg>
                    <span class="hidden sm:inline">Refresh</span>
                </a>
                <button type="button" class="inline-flex min-h-[40px] items-center gap-2 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" data-tracking-map-fit="tracking-map-index">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                    <span class="hidden sm:inline">Fit</span>
                </button>
                <button type="button" class="inline-flex min-h-[40px] items-center gap-2 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" data-tracking-map-fullscreen="tracking-map-shell">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9m11.25-5.25h-4.5m4.5 0v4.5m0-4.5L15 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15m11.25 5.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                    <span class="hidden sm:inline">Fullscreen</span>
                </button>
            </div>
        </div>

        {{-- Desktop Filter Toolbar --}}
        <div class="hidden lg:block">
            <form method="GET" action="{{ route('admin.tracking.index') }}" class="flex flex-wrap items-end gap-2">
                <div>
                    <label for="trk-date" class="sr-only">Tanggal</label>
                    <input id="trk-date" type="date" name="date" value="{{ $filters['date'] }}" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                </div>
                <div>
                    <label for="trk-day" class="sr-only">Hari</label>
                    <select id="trk-day" name="day" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Hari</option>
                        @foreach ($days as $value => $label)
                            <option value="{{ $value }}" @selected($filters['day'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="trk-marketing" class="sr-only">Marketing</label>
                    <select id="trk-marketing" name="marketing_id" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Marketing</option>
                        @foreach ($marketingOptions as $marketing)
                            <option value="{{ $marketing->id }}" @selected((int) $filters['marketing_id'] === $marketing->id)>{{ $marketing->code }} - {{ $marketing->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="trk-status" class="sr-only">Status</label>
                    <select id="trk-status" name="status" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-[var(--mms-radius-md)] bg-[var(--mms-color-primary)] px-4 py-2.5 text-[13px] font-semibold text-[var(--mms-color-primary-foreground)] transition hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Terapkan</button>
                @if ($activeFilterCount > 0)
                    <a href="{{ route('admin.tracking.index') }}" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] px-4 py-2.5 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Reset</a>
                @endif
            </form>
        </div>

        {{-- Mobile Toolbar --}}
        <div class="flex items-center gap-2 lg:hidden">
            <button type="button" class="flex min-h-[44px] flex-1 items-center gap-2 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-4 text-[13px] font-semibold text-[var(--mms-color-text-muted)]" x-on:click="pickerOpen = true" aria-haspopup="dialog" :aria-expanded="pickerOpen">
                <svg class="size-4 shrink-0 text-[var(--mms-color-primary)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7 8a7 7 0 0 0-14 0M17 8h4M19 6v4"/></svg>
                <span>Pilih PDL</span>
            </button>
            <button type="button" class="relative inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-[var(--mms-color-text-muted)]" x-on:click="filterOpen = true" aria-label="Buka filter" aria-haspopup="dialog" :aria-expanded="filterOpen">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L13 13.414V19a1 1 0 0 1-.553.894l-4 2A1 1 0 0 1 7 21v-7.586L3.293 6.707A1 1 0 0 1 3 6V4Z"/></svg>
                @if ($activeFilterCount > 0)
                    <span class="absolute -right-1 -top-1 grid size-4 place-items-center rounded-full bg-[var(--mms-color-primary)] text-[9px] font-bold text-[var(--mms-color-primary-foreground)]" aria-hidden="true">!</span>
                @endif
            </button>
        </div>

        {{-- Marketing Picker Sheet (Mobile) --}}
        <div x-cloak x-show="pickerOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-160" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mms-sheet-backdrop lg:hidden" aria-hidden="true" x-on:click="pickerOpen = false"></div>
        <div role="dialog" aria-modal="true" aria-labelledby="picker-title" x-cloak x-show="pickerOpen" x-transition:enter="transition ease-out duration-220" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-160" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="mms-sheet-panel mms-sheet-panel-tall lg:hidden">
            <div class="mx-auto mt-3 h-1 w-12 rounded-full bg-white/20" aria-hidden="true"></div>
            <div class="flex items-center justify-between border-b border-[var(--mms-color-border)] px-4 pb-3 pt-4">
                <h2 id="picker-title" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Pilih PDL</h2>
                <button type="button" class="grid size-9 place-items-center rounded-full border border-[var(--mms-color-border)] text-[var(--mms-color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" aria-label="Tutup picker" x-on:click="pickerOpen = false">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <div class="mms-scrollbar overflow-y-auto px-4 pt-3 pb-4" style="max-height: calc(90dvh - 7rem)">
                {{-- All marketing option --}}
                <a href="{{ route('admin.tracking.index', array_filter(['date' => $filters['date'], 'day' => $filters['day'], 'status' => $filters['status']])) }}" class="flex min-h-[48px] items-center gap-3 rounded-[10px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[13px] font-semibold text-[var(--mms-color-text)] mb-2 transition hover:border-[var(--mms-color-border-gold)]">
                    <div class="grid size-8 place-items-center rounded-full bg-[var(--mms-color-primary-soft)] text-[var(--mms-color-primary)]">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7 8a7 7 0 0 0-14 0"/></svg>
                    </div>
                    Semua Marketing
                </a>
                <div class="space-y-1.5">
                    @foreach ($rows as $row)
                        <a href="{{ route('admin.tracking.index', array_filter(['date' => $filters['date'], 'day' => $filters['day'], 'status' => $filters['status'], 'marketing_id' => $row['session']?->marketing_profile_id])) }}" class="flex min-h-[52px] items-center gap-3 rounded-[10px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 py-2.5 transition hover:border-[var(--mms-color-border-gold)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">
                            @if ($row['photo_url'])
                                <img src="{{ $row['photo_url'] }}" alt="" loading="lazy" class="size-9 shrink-0 rounded-full object-cover ring-1 ring-white/10">
                            @else
                                <div class="grid size-9 shrink-0 place-items-center rounded-full bg-[var(--mms-color-surface-muted)] text-[12px] font-bold text-[var(--mms-color-primary)] ring-1 ring-white/10">{{ mb_strtoupper(mb_substr(explode(' - ', $row['marketing_label'])[1] ?? '?', 0, 1)) }}</div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13px] font-semibold text-[var(--mms-color-text)]">{{ $row['marketing_label'] }}</p>
                                <p class="text-[11px] text-[var(--mms-color-text-subtle)]">{{ $row['area'] }}</p>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold shrink-0 {{ $statusBg($row['status_value']) }}">
                                <span class="size-1.5 rounded-full {{ $statusDot($row['status_value']) }}" aria-hidden="true"></span>
                                {{ $row['status_label'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Filter Sheet (Mobile) --}}
        <div x-cloak x-show="filterOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-160" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mms-sheet-backdrop lg:hidden" aria-hidden="true" x-on:click="filterOpen = false"></div>
        <div role="dialog" aria-modal="true" aria-labelledby="trk-filter-title" x-cloak x-show="filterOpen" x-transition:enter="transition ease-out duration-220" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-160" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="mms-sheet-panel lg:hidden">
            <div class="mx-auto mt-3 h-1 w-12 rounded-full bg-white/20" aria-hidden="true"></div>
            <div class="flex items-center justify-between border-b border-[var(--mms-color-border)] px-4 pb-3 pt-4">
                <h2 id="trk-filter-title" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Filter Tracking</h2>
                <button type="button" class="grid size-9 place-items-center rounded-full border border-[var(--mms-color-border)] text-[var(--mms-color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" aria-label="Tutup filter" x-on:click="filterOpen = false">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <form method="GET" action="{{ route('admin.tracking.index') }}" class="mms-scrollbar overflow-y-auto px-4 pt-4 pb-2" style="max-height: calc(88dvh - 8rem)">
                @if ($filters['marketing_id'])
                    <input type="hidden" name="marketing_id" value="{{ $filters['marketing_id'] }}">
                @endif
                <div class="space-y-4">
                    <div><label for="trk-date-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Tanggal</label><input id="trk-date-m" type="date" name="date" value="{{ $filters['date'] }}" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"></div>
                    <div><label for="trk-day-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Hari Kerja</label><select id="trk-day-m" name="day" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"><option value="">Semua Hari</option>@foreach ($days as $v => $l)<option value="{{ $v }}" @selected($filters['day'] === $v)>{{ $l }}</option>@endforeach</select></div>
                    <div><label for="trk-status-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Status Tracking</label><select id="trk-status-m" name="status" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"><option value="">Semua Status</option>@foreach ($statuses as $v => $l)<option value="{{ $v }}" @selected($filters['status'] === $v)>{{ $l }}</option>@endforeach</select></div>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3 pb-4">
                    <a href="{{ route('admin.tracking.index', $filters['marketing_id'] ? ['marketing_id' => $filters['marketing_id']] : []) }}" class="flex min-h-[44px] items-center justify-center rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] text-[14px] font-semibold text-[var(--mms-color-text-muted)]">Reset Filter</a>
                    <button type="submit" class="min-h-[44px] rounded-[var(--mms-radius-md)] bg-[var(--mms-color-primary)] text-[14px] font-semibold text-[var(--mms-color-primary-foreground)] hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" x-on:click="filterOpen = false">Terapkan</button>
                </div>
            </form>
        </div>

        {{-- Map + Status List --}}
        <section class="grid gap-4 xl:grid-cols-[1.35fr_0.65fr]" aria-label="Peta tracking">
            <div id="tracking-map-shell" class="overflow-hidden rounded-[16px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]">
                <div
                    id="tracking-map-index"
                    class="mms-map-overview"
                    data-tracking-map
                    data-markers="{{ \Illuminate\Support\Js::encode($markers) }}"
                    data-poll-seconds="{{ $pollingSeconds }}"
                    data-feed-url="{{ route('admin.tracking.feed', array_filter($filters)) }}"
                    aria-label="Peta tracking marketing"
                ></div>
            </div>

            <div class="overflow-hidden rounded-[16px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]">
                <div class="border-b border-[var(--mms-color-border)] px-4 py-3">
                    <h2 class="text-[14px] font-semibold text-[var(--mms-color-text)]">Status PDL</h2>
                    <p class="mt-0.5 text-[11px] text-[var(--mms-color-text-subtle)]">Filter tanggal: {{ \Carbon\CarbonImmutable::parse($filters['date'])->format('d/m/Y') }}</p>
                </div>

                @if ($rows->isEmpty())
                    <div class="px-4 py-8 text-center">
                        <p class="text-[13px] text-[var(--mms-color-text-subtle)]">Belum ada PDL untuk ditampilkan.</p>
                    </div>
                @else
                    <div class="mms-scrollbar space-y-0 divide-y divide-[var(--mms-color-border)] overflow-y-auto" style="max-height: 580px">
                        @foreach ($rows as $row)
                            <div class="px-4 py-3">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="truncate text-[13px] font-semibold text-[var(--mms-color-text)]">{{ $row['marketing_label'] }}</p>
                                        <p class="text-[11px] text-[var(--mms-color-text-subtle)]">{{ $row['area'] }}</p>
                                    </div>
                                    <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $statusBg($row['status_value']) }}">
                                        <span class="size-1.5 rounded-full {{ $statusDot($row['status_value']) }}" aria-hidden="true"></span>
                                        {{ $row['status_label'] }}
                                    </span>
                                </div>
                                @if ($row['last_recorded_at'])
                                    <p class="mt-1.5 text-[11px] text-[var(--mms-color-text-subtle)]">Update: {{ $row['last_recorded_at'] }}</p>
                                @endif
                                @if ($row['location'] !== '-')
                                    <p class="mt-0.5 text-[11px] text-[var(--mms-color-text-subtle)] truncate">{{ $row['location'] }}</p>
                                @endif
                                @if ($row['detail_url'])
                                    <div class="mt-2">
                                        <a href="{{ $row['detail_url'] }}" class="text-[11px] font-semibold text-[var(--mms-color-primary)] hover:text-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Detail Tracking &rarr;</a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <x-data.pagination :paginator="$profiles" class="p-3 border-t border-[var(--mms-color-border)]" />
                @endif
            </div>
        </section>

    </div>
@endsection
