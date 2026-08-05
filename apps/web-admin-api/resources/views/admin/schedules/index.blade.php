@extends('layouts.admin')

@section('title', config('mms.navigation.labels.schedules').' - '.config('mms.name'))
@section('page_title', config('mms.navigation.labels.schedules'))

@php
    $activeFilterCount = collect([
        'search' => $filters['search'],
        'date' => $filters['date'] !== now()->toDateString() ? $filters['date'] : null,
        'day' => $filters['day'],
        'status' => $filters['status'],
        'marketing_id' => $filters['marketing_id'],
    ])->filter(fn ($v) => $v !== '' && $v !== null)->count();
@endphp

@section('content')
    <div class="space-y-5" x-data="{ filterOpen: false }">

        {{-- ── Header ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-[20px] font-semibold text-[var(--mms-color-text)] sm:text-[22px]">{{ config('mms.navigation.labels.schedules') }}</h1>
                <p class="mt-0.5 text-[13px] text-[var(--mms-color-text-subtle)]">Kelola agenda kunjungan marketing.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.schedules.create') }}" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-[10px] bg-[var(--mms-color-primary)] px-3.5 text-[12px] font-semibold text-[var(--mms-color-primary-foreground)] transition hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                    </svg>
                    <span>Tambah Jadwal</span>
                </a>
            </div>
        </div>

        {{-- ── Desktop Compact Filter & Mobile Toolbar ── --}}
        <div class="flex items-center justify-between gap-3 lg:hidden">
            <button
                type="button"
                x-on:click="filterOpen = true"
                class="inline-flex h-9 flex-1 items-center justify-center gap-2 rounded-[10px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-[12px] font-semibold text-[var(--mms-color-text)] transition hover:bg-[var(--mms-color-surface-elevated)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L13 13.414V19a1 1 0 0 1-.553.894l-4 2A1 1 0 0 1 7 21v-7.586L3.293 6.707A1 1 0 0 1 3 6V4Z" />
                </svg>
                Filter & Cari
                @if ($activeFilterCount > 0)
                    <span class="inline-flex size-5 items-center justify-center rounded-full bg-[var(--mms-color-primary)] text-[10px] font-bold text-[var(--mms-color-primary-foreground)]">{{ $activeFilterCount }}</span>
                @endif
            </button>
        </div>

        <div class="hidden rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-2 lg:block">
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex flex-wrap items-center gap-2">
                <div class="flex-1 min-w-[200px]">
                    <label for="d-search" class="sr-only">Cari</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[var(--mms-color-text-subtle)]">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="d-search" name="search" value="{{ $filters['search'] }}" placeholder="Agenda, konsumen..." class="h-9 w-full rounded-[8px] border border-[var(--mms-color-border)] bg-transparent pl-9 pr-3 text-[12px] text-[var(--mms-color-text)] placeholder:text-[var(--mms-color-text-subtle)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)]">
                    </div>
                </div>
                <div class="w-[140px]">
                    <label for="d-date" class="sr-only">Tanggal</label>
                    <input type="date" id="d-date" name="date" value="{{ $filters['date'] }}" class="h-9 w-full rounded-[8px] border border-[var(--mms-color-border)] bg-transparent px-3 text-[12px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)]">
                </div>
                <div class="w-[140px]">
                    <label for="d-day" class="sr-only">Hari</label>
                    <select id="d-day" name="day" class="h-9 w-full rounded-[8px] border border-[var(--mms-color-border)] bg-transparent px-3 text-[12px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)] appearance-none">
                        <option value="">Semua hari</option>
                        @foreach ($days as $value => $label)
                            <option value="{{ $value }}" @selected($filters['day'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-[140px]">
                    <label for="d-status" class="sr-only">Status</label>
                    <select id="d-status" name="status" class="h-9 w-full rounded-[8px] border border-[var(--mms-color-border)] bg-transparent px-3 text-[12px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)] appearance-none">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-[180px]">
                    <label for="d-marketing" class="sr-only">Marketing</label>
                    <select id="d-marketing" name="marketing_id" class="h-9 w-full rounded-[8px] border border-[var(--mms-color-border)] bg-transparent px-3 text-[12px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)] appearance-none">
                        <option value="">Semua marketing</option>
                        @foreach ($marketingOptions as $marketing)
                            <option value="{{ $marketing->id }}" @selected((int) $filters['marketing_id'] === $marketing->id)>{{ $marketing->code }} - {{ $marketing->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex h-9 items-center justify-center rounded-[8px] bg-[var(--mms-color-surface-muted)] px-4 text-[12px] font-semibold text-[var(--mms-color-text)] transition hover:bg-[var(--mms-color-border)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">
                    Terapkan
                </button>
                @if ($activeFilterCount > 0)
                    <a href="{{ route('admin.schedules.index') }}" class="inline-flex h-9 items-center justify-center rounded-[8px] px-3 text-[12px] font-semibold text-[var(--mms-color-danger)] transition hover:bg-red-50 focus-visible:outline-none">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- ── Mobile Bottom Sheet Filter ── --}}
        <div x-show="filterOpen" x-cloak class="relative z-50 lg:hidden" aria-labelledby="filter-modal-title" role="dialog" aria-modal="true">
            <div x-show="filterOpen" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm" aria-hidden="true"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center text-center">
                    <div
                        x-show="filterOpen"
                        x-transition:enter="transform transition ease-out duration-300"
                        x-transition:enter-start="translate-y-full"
                        x-transition:enter-end="translate-y-0"
                        x-transition:leave="transform transition ease-in duration-200"
                        x-transition:leave-start="translate-y-0"
                        x-transition:leave-end="translate-y-full"
                        x-on:click.away="filterOpen = false"
                        class="relative w-full max-w-md rounded-t-[20px] bg-[var(--mms-color-surface)] text-left shadow-2xl ring-1 ring-[var(--mms-color-border)]"
                    >
                        <div class="flex items-center justify-between border-b border-[var(--mms-color-border)] px-5 py-4">
                            <h2 id="filter-modal-title" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Filter Jadwal</h2>
                            <button x-on:click="filterOpen = false" class="rounded-full p-1.5 text-[var(--mms-color-text-subtle)] hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form method="GET" action="{{ route('admin.schedules.index') }}" class="p-5">
                            <div class="space-y-4">
                                <div>
                                    <label for="m-search" class="mb-1.5 block text-[12px] font-semibold text-[var(--mms-color-text-muted)]">Cari</label>
                                    <input type="text" id="m-search" name="search" value="{{ $filters['search'] }}" placeholder="Agenda, konsumen..." class="h-10 w-full rounded-[10px] border border-[var(--mms-color-border)] bg-transparent px-3 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)]">
                                </div>
                                <div>
                                    <label for="m-date" class="mb-1.5 block text-[12px] font-semibold text-[var(--mms-color-text-muted)]">Tanggal</label>
                                    <input type="date" id="m-date" name="date" value="{{ $filters['date'] }}" class="h-10 w-full rounded-[10px] border border-[var(--mms-color-border)] bg-transparent px-3 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)]">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="m-day" class="mb-1.5 block text-[12px] font-semibold text-[var(--mms-color-text-muted)]">Hari</label>
                                        <select id="m-day" name="day" class="h-10 w-full rounded-[10px] border border-[var(--mms-color-border)] bg-transparent px-3 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)]">
                                            <option value="">Semua</option>
                                            @foreach ($days as $value => $label)
                                                <option value="{{ $value }}" @selected($filters['day'] === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="m-status" class="mb-1.5 block text-[12px] font-semibold text-[var(--mms-color-text-muted)]">Status</label>
                                        <select id="m-status" name="status" class="h-10 w-full rounded-[10px] border border-[var(--mms-color-border)] bg-transparent px-3 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)]">
                                            <option value="">Semua</option>
                                            @foreach ($statuses as $value => $label)
                                                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="m-marketing" class="mb-1.5 block text-[12px] font-semibold text-[var(--mms-color-text-muted)]">Marketing</label>
                                    <select id="m-marketing" name="marketing_id" class="h-10 w-full rounded-[10px] border border-[var(--mms-color-border)] bg-transparent px-3 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--mms-color-primary)]">
                                        <option value="">Semua marketing</option>
                                        @foreach ($marketingOptions as $marketing)
                                            <option value="{{ $marketing->id }}" @selected((int) $filters['marketing_id'] === $marketing->id)>{{ $marketing->code }} - {{ $marketing->user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-6 flex gap-3">
                                @if ($activeFilterCount > 0)
                                    <a href="{{ route('admin.schedules.index') }}" class="inline-flex h-11 flex-1 items-center justify-center rounded-[10px] border border-[var(--mms-color-border)] bg-transparent text-[13px] font-semibold text-[var(--mms-color-text)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none">
                                        Reset
                                    </a>
                                @endif
                                <button type="submit" class="inline-flex h-11 flex-1 items-center justify-center rounded-[10px] bg-[var(--mms-color-primary)] text-[13px] font-semibold text-[var(--mms-color-primary-foreground)] transition hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none">
                                    Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Data List ── --}}
        @if ($schedules->isEmpty())
            <x-ui.empty-state title="Belum ada jadwal" description="Jadwal sesuai filter belum tersedia." />
        @else
            {{-- Mobile Cards --}}
            <div class="grid gap-3 lg:hidden">
                @foreach ($schedules as $schedule)
                    <article class="group relative flex flex-col gap-3 rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-4 transition hover:-translate-y-px hover:border-[var(--mms-color-border-strong)] hover:shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[14px] font-semibold text-[var(--mms-color-text)]">{{ $schedule->agenda }}</p>
                                <p class="mt-0.5 text-[12px] font-medium text-[var(--mms-color-primary)]">{{ $schedule->marketingProfile->code }} - {{ $schedule->marketingProfile->user->name }}</p>
                            </div>
                            @php
                                $statusLabel = $schedule->status->label();
                                $stBg = match($statusLabel) {
                                    'Selesai' => 'bg-[rgba(34,197,94,0.1)] text-[var(--mms-color-success)]',
                                    'Batal' => 'bg-[rgba(239,68,68,0.1)] text-[var(--mms-color-danger)]',
                                    default => 'bg-[rgba(245,158,11,0.1)] text-[var(--mms-color-warning)]',
                                };
                            @endphp
                            <span class="inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $stBg }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 rounded-[10px] bg-[var(--mms-color-surface-muted)] p-2.5">
                            <div>
                                <p class="text-[10px] uppercase text-[var(--mms-color-text-muted)]">Tanggal</p>
                                <p class="mt-0.5 text-[12px] font-semibold text-[var(--mms-color-text)]">{{ $schedule->schedule_date->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase text-[var(--mms-color-text-muted)]">Waktu</p>
                                <p class="mt-0.5 text-[12px] font-semibold text-[var(--mms-color-text)]">{{ substr($schedule->start_time, 0, 5) }}{{ $schedule->end_time ? ' - '.substr($schedule->end_time, 0, 5) : '' }}</p>
                            </div>
                        </div>

                        <div class="space-y-1 text-[12px] text-[var(--mms-color-text-subtle)]">
                            <p><span class="font-medium text-[var(--mms-color-text-muted)]">Konsumen:</span> {{ $schedule->consumer_name_snapshot ?? $schedule->prospect?->name ?? '-' }}</p>
                            <p><span class="font-medium text-[var(--mms-color-text-muted)]">Area:</span> {{ $schedule->area }}{{ $schedule->resort ? ' - '.$schedule->resort : '' }}</p>
                        </div>

                        <div class="mt-2 flex">
                            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="inline-flex h-8 items-center justify-center rounded-[8px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-[11px] font-semibold text-[var(--mms-color-text)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none">
                                Edit Jadwal
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Desktop Table --}}
            <div class="hidden overflow-x-auto rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] lg:block">
                <table class="mms-table-sticky w-full border-collapse text-left text-[13px]">
                    <thead class="bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text-subtle)]">
                        <tr>
                            <th scope="col" class="border-b border-[var(--mms-color-border)] px-4 py-3 font-semibold">Waktu</th>
                            <th scope="col" class="border-b border-[var(--mms-color-border)] px-4 py-3 font-semibold">Marketing</th>
                            <th scope="col" class="border-b border-[var(--mms-color-border)] px-4 py-3 font-semibold">Konsumen</th>
                            <th scope="col" class="border-b border-[var(--mms-color-border)] px-4 py-3 font-semibold">Agenda</th>
                            <th scope="col" class="border-b border-[var(--mms-color-border)] px-4 py-3 font-semibold">Area</th>
                            <th scope="col" class="border-b border-[var(--mms-color-border)] px-4 py-3 font-semibold">Status</th>
                            <th scope="col" class="border-b border-[var(--mms-color-border)] px-4 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-[var(--mms-color-text)]">
                        @foreach ($schedules as $schedule)
                            <tr class="group border-b border-[var(--mms-color-border)] transition hover:bg-[var(--mms-color-surface-muted)] last:border-0">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-medium">{{ $schedule->schedule_date->format('d/m/Y') }}</span>
                                    <span class="text-[var(--mms-color-text-subtle)] block text-[11px] mt-0.5">{{ substr($schedule->start_time, 0, 5) }}{{ $schedule->end_time ? ' - '.substr($schedule->end_time, 0, 5) : '' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-[var(--mms-color-primary)]">{{ $schedule->marketingProfile->code }}</span>
                                    <span class="block text-[12px] text-[var(--mms-color-text-subtle)] mt-0.5">{{ $schedule->marketingProfile->user->name }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $schedule->consumer_name_snapshot ?? $schedule->prospect?->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $schedule->agenda }}</td>
                                <td class="px-4 py-3 text-[var(--mms-color-text-subtle)]">{{ $schedule->area }}{{ $schedule->resort ? ' - '.$schedule->resort : '' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $statusLabel = $schedule->status->label();
                                        $stBg = match($statusLabel) {
                                            'Selesai' => 'bg-[rgba(34,197,94,0.1)] text-[var(--mms-color-success)]',
                                            'Batal' => 'bg-[rgba(239,68,68,0.1)] text-[var(--mms-color-danger)]',
                                            default => 'bg-[rgba(245,158,11,0.1)] text-[var(--mms-color-warning)]',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $stBg }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="inline-flex h-8 items-center justify-center rounded-[8px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-[11px] font-semibold text-[var(--mms-color-text)] transition hover:bg-[var(--mms-color-border-strong)] focus-visible:outline-none">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <x-data.pagination :paginator="$schedules" />
            </div>
        @endif
    </div>
@endsection
