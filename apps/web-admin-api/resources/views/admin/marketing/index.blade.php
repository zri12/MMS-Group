@extends('layouts.admin')

@section('title', 'Data Marketing - '.config('mms.name'))
@section('page_title', 'Data Marketing')

@php
    $rupiah = fn (int $v): string => 'Rp '.number_format($v, 0, ',', '.');
    $rupiahJt = fn (int $v): string => 'Rp '.number_format($v / 1_000_000, 1, ',', '.').' jt';

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

    $activeFilterCount = collect($filters)->filter(fn($v) => $v !== '' && $v !== null)->count();
@endphp

@section('content')
    <div class="space-y-5"
         x-data="{
             filterOpen: false,
             search: @js($filters['search']),
         }"
         x-on:keydown.escape.window="filterOpen = false"
         x-effect="document.body.classList.toggle('overflow-hidden', filterOpen)"
    >

        {{-- ── Page Header ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-[22px] font-semibold text-[var(--mms-color-text)] sm:text-[26px]">Data Marketing</h1>
                <p class="mt-0.5 text-[13px] text-[var(--mms-color-text-subtle)]">Kelola akun, area, hari kerja, dan status marketing.</p>
            </div>
            <x-ui.link-button :href="route('admin.marketing.create')">
                <svg class="mr-1.5 size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/></svg>
                Tambah Marketing
            </x-ui.link-button>
        </div>

        {{-- ── Desktop Filter (compact toolbar) ── --}}
        <div class="hidden lg:block">
            <form method="GET" action="{{ route('admin.marketing.index') }}" class="flex flex-wrap items-end gap-2">
                <div class="min-w-[200px] flex-1">
                    <label for="search-desktop" class="sr-only">Cari marketing</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--mms-color-text-subtle)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z"/>
                        </svg>
                        <input
                            id="search-desktop"
                            type="search"
                            name="search"
                            value="{{ $filters['search'] }}"
                            placeholder="Nama, kode, area, HP..."
                            class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-9 pr-4 text-[13px] text-[var(--mms-color-text)] placeholder:text-[var(--mms-color-text-subtle)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"
                        />
                    </div>
                </div>
                <div>
                    <label for="status-desktop" class="sr-only">Status akun</label>
                    <select id="status-desktop" name="status" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Status</option>
                        <option value="active" @selected($filters['status'] === 'active')>Aktif</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label for="area-desktop" class="sr-only">Area</label>
                    <select id="area-desktop" name="area" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Area</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area }}" @selected($filters['area'] === $area)>{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="day-desktop" class="sr-only">Hari kerja</label>
                    <select id="day-desktop" name="day" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Hari</option>
                        @foreach ($days as $value => $label)
                            <option value="{{ $value }}" @selected($filters['day'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-[var(--mms-radius-md)] bg-[var(--mms-color-primary)] px-4 py-2.5 text-[13px] font-semibold text-[var(--mms-color-primary-foreground)] transition hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Terapkan</button>
                @if ($activeFilterCount > 0)
                    <a href="{{ route('admin.marketing.index') }}" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] px-4 py-2.5 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Reset</a>
                @endif
            </form>
        </div>

        {{-- ── Mobile Toolbar ── --}}
        <div class="flex gap-2 lg:hidden">
            {{-- Search bar mobile --}}
            <form method="GET" action="{{ route('admin.marketing.index') }}" class="flex flex-1 gap-2">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--mms-color-text-subtle)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z"/>
                    </svg>
                    <input
                        id="search-mobile"
                        type="search"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Cari marketing..."
                        class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-9 pr-4 text-[13px] text-[var(--mms-color-text)] placeholder:text-[var(--mms-color-text-subtle)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"
                    />
                    {{-- Preserve other filters when searching --}}
                    @if ($filters['status']) <input type="hidden" name="status" value="{{ $filters['status'] }}"> @endif
                    @if ($filters['area']) <input type="hidden" name="area" value="{{ $filters['area'] }}"> @endif
                    @if ($filters['day']) <input type="hidden" name="day" value="{{ $filters['day'] }}"> @endif
                </div>
            </form>

            {{-- Filter button mobile --}}
            <button
                type="button"
                class="relative inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                x-on:click="filterOpen = true"
                aria-label="Buka filter"
                aria-haspopup="dialog"
                :aria-expanded="filterOpen"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L13 13.414V19a1 1 0 0 1-.553.894l-4 2A1 1 0 0 1 7 21v-7.586L3.293 6.707A1 1 0 0 1 3 6V4Z"/>
                </svg>
                @php $mobileFilterCount = collect([$filters['status'], $filters['area'], $filters['day']])->filter()->count(); @endphp
                @if ($mobileFilterCount > 0)
                    <span class="absolute -right-1 -top-1 grid size-4 place-items-center rounded-full bg-[var(--mms-color-primary)] text-[9px] font-bold text-[var(--mms-color-primary-foreground)]" aria-label="{{ $mobileFilterCount }} filter aktif">{{ $mobileFilterCount }}</span>
                @endif
            </button>
        </div>

        {{-- ── Mobile Filter Bottom Sheet ── --}}
        {{-- Overlay --}}
        <div
            x-cloak
            x-show="filterOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-160"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="mms-sheet-backdrop lg:hidden"
            aria-hidden="true"
            x-on:click="filterOpen = false"
        ></div>

        {{-- Sheet panel --}}
        <div
            id="marketing-filter-sheet"
            role="dialog"
            aria-modal="true"
            aria-labelledby="marketing-filter-title"
            x-cloak
            x-show="filterOpen"
            x-transition:enter="transition ease-out duration-220"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-160"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="mms-sheet-panel lg:hidden"
            style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom))"
        >
            <div class="mx-auto mt-3 h-1 w-12 rounded-full bg-white/20" aria-hidden="true"></div>
            <div class="flex items-center justify-between border-b border-[var(--mms-color-border)] px-4 pb-3 pt-4">
                <div>
                    <h2 id="marketing-filter-title" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Filter Marketing</h2>
                    @if ($mobileFilterCount > 0)
                        <p class="mt-0.5 text-[12px] text-[var(--mms-color-primary)]">{{ $mobileFilterCount }} filter aktif</p>
                    @endif
                </div>
                <button type="button" class="grid size-9 place-items-center rounded-full border border-[var(--mms-color-border)] text-[var(--mms-color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" aria-label="Tutup filter" x-on:click="filterOpen = false">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <form method="GET" action="{{ route('admin.marketing.index') }}" class="mms-scrollbar overflow-y-auto px-4 pt-4" style="max-height: calc(88dvh - 8rem)">
                @if ($filters['search'])
                    <input type="hidden" name="search" value="{{ $filters['search'] }}">
                @endif
                <div class="space-y-4">
                    <div>
                        <label for="status-sheet" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Status Akun</label>
                        <select id="status-sheet" name="status" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                            <option value="">Semua Status</option>
                            <option value="active" @selected($filters['status'] === 'active')>Aktif</option>
                            <option value="inactive" @selected($filters['status'] === 'inactive')>Nonaktif</option>
                        </select>
                    </div>
                    <div>
                        <label for="area-sheet" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Area</label>
                        <select id="area-sheet" name="area" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                            <option value="">Semua Area</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area }}" @selected($filters['area'] === $area)>{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="day-sheet" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Hari Kerja</label>
                        <select id="day-sheet" name="day" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                            <option value="">Semua Hari</option>
                            @foreach ($days as $value => $label)
                                <option value="{{ $value }}" @selected($filters['day'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3 pb-4">
                    <a href="{{ route('admin.marketing.index', $filters['search'] ? ['search' => $filters['search']] : []) }}" class="flex min-h-[44px] items-center justify-center rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] text-[14px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)]">Reset</a>
                    <button type="submit" class="min-h-[44px] rounded-[var(--mms-radius-md)] bg-[var(--mms-color-primary)] text-[14px] font-semibold text-[var(--mms-color-primary-foreground)] transition hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" x-on:click="filterOpen = false">Terapkan</button>
                </div>
            </form>
        </div>

        {{-- ── Marketing Grid ── --}}
        @if ($profiles->isEmpty())
            <x-ui.empty-state
                title="Belum ada marketing"
                description="Tidak ada data marketing yang sesuai filter. Coba reset filter atau tambah marketing baru."
            />
        @else
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($profiles as $profile)
                    @php
                        $photoUrl    = $photoResolver($profile);
                        $session     = $profile->latestTrackingSession;
                        $trackStatus = $session?->status?->label() ?? 'Belum Mulai';
                        $statusValue = $session?->status?->value ?? '';
                        $lastActive  = $session?->updated_at?->timezone(config('app.timezone'))?->format('H:i');
                    @endphp
                    <article class="group rounded-[16px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-4 transition hover:border-[var(--mms-color-border-strong)] hover:bg-[var(--mms-color-surface-elevated)]">

                        {{-- Header: foto + info + status akun --}}
                        <div class="flex items-start gap-3">
                            <div class="relative shrink-0">
                                @if ($photoUrl)
                                    <img
                                        src="{{ $photoUrl }}"
                                        alt="Foto {{ $profile->user->name }}"
                                        loading="lazy"
                                        class="size-14 rounded-[12px] object-cover ring-1 ring-white/10"
                                        onerror="this.style.display='none';this.nextElementSibling.style.display='grid'"
                                    />
                                @endif
                                <div class="{{ $photoUrl ? 'hidden' : 'grid' }} size-14 place-items-center rounded-[12px] bg-[var(--mms-color-surface-muted)] text-[18px] font-bold text-[var(--mms-color-primary)] ring-1 ring-white/10">
                                    {{ mb_strtoupper(mb_substr($profile->user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-bold text-[var(--mms-color-primary)]">{{ $profile->code }}</p>
                                        <p class="truncate text-[15px] font-semibold leading-snug text-[var(--mms-color-text)]">{{ $profile->user->name }}</p>
                                        <p class="text-[12px] text-[var(--mms-color-text-muted)]">{{ $profile->area }}</p>
                                    </div>
                                    <x-ui.badge :variant="$profile->user->is_active ? 'success' : 'danger'" class="shrink-0">
                                        {{ $profile->user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </x-ui.badge>
                                </div>
                                <p class="mt-1 text-[11px] text-[var(--mms-color-text-subtle)]">{{ $profile->phone ?? $profile->user->username }}</p>
                            </div>
                        </div>

                        {{-- Tracking status --}}
                        <div class="mt-3 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusBg($statusValue) }}">
                                <span class="size-1.5 rounded-full {{ $statusDot($statusValue) }}" aria-hidden="true"></span>
                                {{ $trackStatus }}
                            </span>
                            @if ($lastActive && $statusValue === 'Aktif')
                                <span class="text-[11px] text-[var(--mms-color-success)]">{{ $lastActive }} WIB</span>
                            @elseif ($lastActive && $statusValue === 'Offline')
                                <span class="text-[11px] text-[var(--mms-color-text-subtle)]">Terakhir {{ $lastActive }} WIB</span>
                            @endif
                        </div>

                        {{-- Mini operational stats --}}
                        <div class="mt-3 grid grid-cols-3 gap-1.5">
                            <div class="rounded-[8px] bg-[var(--mms-color-surface-muted)] px-2 py-1.5 text-center">
                                <p class="text-[13px] font-semibold text-[var(--mms-color-text)]">{{ $rupiahJt((int) ($profile->total_target_sum ?? 0)) }}</p>
                                <p class="text-[10px] text-[var(--mms-color-text-subtle)]">Target</p>
                            </div>
                            <div class="rounded-[8px] bg-[var(--mms-color-surface-muted)] px-2 py-1.5 text-center">
                                <p class="text-[13px] font-semibold text-[var(--mms-color-text)]">{{ $rupiahJt((int) ($profile->total_drop_sum ?? 0)) }}</p>
                                <p class="text-[10px] text-[var(--mms-color-text-subtle)]">Drop</p>
                            </div>
                            <div class="rounded-[8px] bg-[var(--mms-color-surface-muted)] px-2 py-1.5 text-center">
                                <p class="text-[13px] font-semibold text-[var(--mms-color-text)]">{{ $rupiahJt((int) ($profile->total_storting_sum ?? 0)) }}</p>
                                <p class="text-[10px] text-[var(--mms-color-text-subtle)]">Storting</p>
                            </div>
                        </div>

                        <p class="mt-2 text-[11px] text-[var(--mms-color-text-subtle)]">
                            {{ $profile->prospects_count }} prospek · {{ $profile->members_count }} anggota · {{ $profile->schedules_count }} jadwal
                        </p>

                        {{-- Hari kerja --}}
                        @if ($profile->workDays->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach ($profile->workDays as $workDay)
                                    <span class="rounded-full border border-[var(--mms-color-border)] px-2 py-0.5 text-[10px] font-semibold text-[var(--mms-color-text-subtle)]">{{ $workDay->day_name->label() }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-4 flex flex-wrap gap-2">
                            <x-ui.link-button :href="route('admin.marketing.show', $profile)" size="sm">Detail</x-ui.link-button>
                            <x-ui.link-button :href="route('admin.schedules.index', ['marketing_id' => $profile->id])" variant="outline" size="sm">
                                <svg class="mr-1 size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3m10-3v3M4 9h16M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/></svg>
                                Jadwal
                            </x-ui.link-button>
                            <x-ui.link-button :href="route('admin.marketing.edit', $profile)" variant="outline" size="sm">Edit</x-ui.link-button>
                        </div>
                    </article>
                @endforeach
            </div>

            <x-data.pagination :paginator="$profiles" class="mt-4" />
        @endif

    </div>
@endsection
