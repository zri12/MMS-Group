@extends('layouts.admin')

@section('title', config('mms.navigation.labels.journeys').' - '.config('mms.name'))
@section('page_title', config('mms.navigation.labels.journeys'))

@section('content')
    <div class="space-y-5">

        {{-- ── Page Header ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-[22px] font-semibold text-[var(--mms-color-text)] sm:text-[26px]">{{ config('mms.navigation.labels.journeys') }}</h1>
                <p class="mt-0.5 text-[13px] text-[var(--mms-color-text-subtle)]">Riwayat sesi tracking semua marketing.</p>
            </div>
        </div>

        {{-- ── Compact Filter (Desktop) ── --}}
        <div class="hidden lg:block">
            <form method="GET" action="{{ route('admin.journeys.index') }}" class="flex flex-wrap items-end gap-2">
                <div>
                    <label for="jrn-date" class="sr-only">Tanggal</label>
                    <input id="jrn-date" type="date" name="date" value="{{ $filters['date'] }}" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                </div>
                <div>
                    <label for="jrn-day" class="sr-only">Hari</label>
                    <select id="jrn-day" name="day" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Hari</option>
                        @foreach ($days as $value => $label)
                            <option value="{{ $value }}" @selected($filters['day'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="jrn-status" class="sr-only">Status</label>
                    <select id="jrn-status" name="status" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="jrn-marketing" class="sr-only">Marketing</label>
                    <select id="jrn-marketing" name="marketing_id" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Marketing</option>
                        @foreach ($marketingOptions as $marketing)
                            <option value="{{ $marketing->id }}" @selected((int) $filters['marketing_id'] === $marketing->id)>{{ $marketing->code }} - {{ $marketing->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-[var(--mms-radius-md)] bg-[var(--mms-color-primary)] px-4 py-2.5 text-[13px] font-semibold text-[var(--mms-color-primary-foreground)] transition hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Terapkan</button>
                <a href="{{ route('admin.journeys.index') }}" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] px-4 py-2.5 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Reset</a>
            </form>
        </div>

        {{-- ── Mobile Filter ── --}}
        <div class="lg:hidden" x-data="{ open: false }" x-on:keydown.escape.window="open = false" x-effect="document.body.classList.toggle('overflow-hidden', open)">
            <button type="button" class="flex min-h-[44px] w-full items-center justify-between rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-4 text-[13px] font-semibold text-[var(--mms-color-text-muted)]" x-on:click="open = true" aria-haspopup="dialog" :aria-expanded="open">
                <span class="flex items-center gap-2">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L13 13.414V19a1 1 0 0 1-.553.894l-4 2A1 1 0 0 1 7 21v-7.586L3.293 6.707A1 1 0 0 1 3 6V4Z"/></svg>
                    Filter Riwayat
                </span>
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
            </button>
            {{-- Overlay + Sheet --}}
            <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-160" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mms-sheet-backdrop" aria-hidden="true" x-on:click="open = false"></div>
            <div role="dialog" aria-modal="true" aria-labelledby="jrn-filter-title" x-cloak x-show="open" x-transition:enter="transition ease-out duration-220" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-160" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="mms-sheet-panel">
                <div class="mx-auto mt-3 h-1 w-12 rounded-full bg-white/20" aria-hidden="true"></div>
                <div class="flex items-center justify-between border-b border-[var(--mms-color-border)] px-4 pb-3 pt-4">
                    <h2 id="jrn-filter-title" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Filter Riwayat</h2>
                    <button type="button" class="grid size-9 place-items-center rounded-full border border-[var(--mms-color-border)] text-[var(--mms-color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" aria-label="Tutup filter" x-on:click="open = false">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"/></svg>
                    </button>
                </div>
                <form method="GET" action="{{ route('admin.journeys.index') }}" class="mms-scrollbar overflow-y-auto px-4 pt-4 pb-2" style="max-height: calc(88dvh - 8rem)">
                    <div class="space-y-4">
                        <div><label for="jrn-date-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Tanggal</label><input id="jrn-date-m" type="date" name="date" value="{{ $filters['date'] }}" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"></div>
                        <div><label for="jrn-day-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Hari</label><select id="jrn-day-m" name="day" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"><option value="">Semua Hari</option>@foreach ($days as $v => $l)<option value="{{ $v }}" @selected($filters['day'] === $v)>{{ $l }}</option>@endforeach</select></div>
                        <div><label for="jrn-status-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Status</label><select id="jrn-status-m" name="status" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"><option value="">Semua Status</option>@foreach ($statuses as $v => $l)<option value="{{ $v }}" @selected($filters['status'] === $v)>{{ $l }}</option>@endforeach</select></div>
                        <div><label for="jrn-mkt-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Marketing</label><select id="jrn-mkt-m" name="marketing_id" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]"><option value="">Semua Marketing</option>@foreach ($marketingOptions as $mkt)<option value="{{ $mkt->id }}" @selected((int) $filters['marketing_id'] === $mkt->id)>{{ $mkt->code }} - {{ $mkt->user->name }}</option>@endforeach</select></div>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-3 pb-4">
                        <a href="{{ route('admin.journeys.index') }}" class="flex min-h-[44px] items-center justify-center rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] text-[14px] font-semibold text-[var(--mms-color-text-muted)]">Reset</a>
                        <button type="submit" class="min-h-[44px] rounded-[var(--mms-radius-md)] bg-[var(--mms-color-primary)] text-[14px] font-semibold text-[var(--mms-color-primary-foreground)] hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" x-on:click="open = false">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Content ── --}}
        @if ($sessions->isEmpty())
            <x-ui.empty-state title="Belum ada riwayat" description="Sesi tracking sesuai filter belum tersedia." />
        @else
            {{-- Mobile cards --}}
            <div class="grid gap-3 lg:hidden">
                @foreach ($sessions as $session)
                    <a
                        href="{{ route('admin.journeys.show', $session) }}"
                        class="rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-4 transition hover:border-[var(--mms-color-border-strong)] hover:bg-[var(--mms-color-surface-elevated)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[11px] font-bold text-[var(--mms-color-primary)]">{{ $session->marketingProfile->code }}</p>
                                <p class="truncate text-[15px] font-semibold text-[var(--mms-color-text)]">{{ $session->marketingProfile->user->name }}</p>
                            </div>
                            <x-ui.badge variant="neutral">{{ $session->status->label() }}</x-ui.badge>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-[8px] bg-[var(--mms-color-surface-muted)] px-2 py-2">
                                <p class="text-[13px] font-semibold text-[var(--mms-color-text)]">{{ $session->session_date->format('d/m') }}</p>
                                <p class="text-[10px] text-[var(--mms-color-text-subtle)]">Tanggal</p>
                            </div>
                            <div class="rounded-[8px] bg-[var(--mms-color-surface-muted)] px-2 py-2">
                                <p class="text-[13px] font-semibold text-[var(--mms-color-text)]">{{ $session->visit_count }}</p>
                                <p class="text-[10px] text-[var(--mms-color-text-subtle)]">Kunjungan</p>
                            </div>
                            <div class="rounded-[8px] bg-[var(--mms-color-surface-muted)] px-2 py-2">
                                @if ($session->distance_meters !== null)
                                    <p class="text-[13px] font-semibold text-[var(--mms-color-text)]">{{ number_format($session->distance_meters / 1000, 1, ',', '.') }}</p>
                                    <p class="text-[10px] text-[var(--mms-color-text-subtle)]">km</p>
                                @else
                                    <p class="text-[11px] font-medium text-[var(--mms-color-text-subtle)]">—</p>
                                    <p class="text-[10px] text-[var(--mms-color-text-subtle)]">Jarak</p>
                                @endif
                            </div>
                        </div>
                        <p class="mt-3 text-[12px] text-[var(--mms-color-text-subtle)]">{{ $session->started_at->format('H:i') }}{{ $session->ended_at ? ' – '.$session->ended_at->format('H:i') : ' – berjalan' }} · {{ $session->points_count }} titik</p>
                    </a>
                @endforeach
            </div>

            {{-- Desktop table --}}
            <div class="hidden overflow-x-auto rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] lg:block">
                <table class="w-full text-left text-[13px]" aria-label="Riwayat perjalanan">
                    <caption class="sr-only">Riwayat perjalanan marketing</caption>
                    <thead class="bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text-subtle)]">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">Marketing</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Tanggal</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Mulai / Selesai</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Jarak</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Kunjungan</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Titik</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--mms-color-border)] text-[var(--mms-color-text-muted)]">
                        @foreach ($sessions as $session)
                            <tr class="hover:bg-[var(--mms-color-surface-muted)]">
                                <td class="px-4 py-3 font-semibold text-[var(--mms-color-text)]">{{ $session->marketingProfile->code }} - {{ $session->marketingProfile->user->name }}</td>
                                <td class="px-4 py-3">{{ $session->session_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ $session->started_at->format('H:i') }}{{ $session->ended_at ? ' – '.$session->ended_at->format('H:i') : ' – berjalan' }}</td>
                                <td class="px-4 py-3">{{ $session->distance_meters !== null ? number_format($session->distance_meters / 1000, 2, ',', '.').' km' : '—' }}</td>
                                <td class="px-4 py-3">{{ $session->visit_count }}</td>
                                <td class="px-4 py-3">{{ $session->points_count }}</td>
                                <td class="px-4 py-3"><x-ui.badge variant="neutral">{{ $session->status->label() }}</x-ui.badge></td>
                                <td class="px-4 py-3"><x-ui.link-button :href="route('admin.journeys.show', $session)" size="sm">Detail</x-ui.link-button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-data.pagination :paginator="$sessions" class="mt-4" />
        @endif
    </div>
@endsection
