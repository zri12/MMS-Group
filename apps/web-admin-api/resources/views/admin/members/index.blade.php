@extends('layouts.admin')

@section('title', 'Data Anggota - '.config('mms.name'))
@section('page_title', 'Data Anggota')

@php
    $rupiah = fn (int $value): string => 'Rp '.number_format($value, 0, ',', '.');
    $badgeVariant = fn (string $status): string => match ($status) {
        'Disetujui' => 'success',
        'Ditolak'   => 'danger',
        default     => 'warning',
    };
    $activeFilterCount = collect($filters)->filter(fn ($v) => $v !== '' && $v !== null)->count();
@endphp

@section('content')
    <div
        class="space-y-5"
        x-data="{ filterOpen: false }"
        x-on:keydown.escape.window="filterOpen = false"
        x-effect="document.body.classList.toggle('overflow-hidden', filterOpen)"
    >

        {{-- ── Page Header ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-[22px] font-semibold text-[var(--mms-color-text)] sm:text-[26px]">Data Anggota</h1>
                <p class="mt-0.5 text-[13px] text-[var(--mms-color-text-subtle)]">Kelola data anggota dan proses persetujuan.</p>
            </div>
        </div>

        {{-- ── Desktop Filter Toolbar ── --}}
        <div class="hidden lg:block">
            <form method="GET" action="{{ route('admin.members.index') }}" class="flex flex-wrap items-end gap-2">
                <div class="min-w-[180px] flex-1">
                    <label for="search-m" class="sr-only">Cari anggota</label>
                    <input id="search-m" type="search" name="search" value="{{ $filters['search'] }}" placeholder="Nama, nomor, HP..." class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 px-3 text-[13px] text-[var(--mms-color-text)] placeholder:text-[var(--mms-color-text-subtle)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                </div>
                <div>
                    <label for="status-m" class="sr-only">Status</label>
                    <select id="status-m" name="approval_status" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($filters['approval_status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="resort-m" class="sr-only">Resort</label>
                    <select id="resort-m" name="resort" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Resort</option>
                        @foreach ($resorts as $resort)
                            <option value="{{ $resort }}" @selected($filters['resort'] === $resort)>{{ $resort }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="marketing-m" class="sr-only">Marketing</label>
                    <select id="marketing-m" name="marketing_id" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Marketing</option>
                        @foreach ($marketingOptions as $marketing)
                            <option value="{{ $marketing->id }}" @selected((int) $filters['marketing_id'] === $marketing->id)>{{ $marketing->code }} - {{ $marketing->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="day-m" class="sr-only">Hari</label>
                    <select id="day-m" name="day" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 pl-3 pr-8 text-[13px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                        <option value="">Semua Hari</option>
                        @foreach ($days as $value => $label)
                            <option value="{{ $value }}" @selected($filters['day'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-[var(--mms-radius-md)] bg-[var(--mms-color-primary)] px-4 py-2.5 text-[13px] font-semibold text-[var(--mms-color-primary-foreground)] transition hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Terapkan</button>
                @if ($activeFilterCount > 0)
                    <a href="{{ route('admin.members.index') }}" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] px-4 py-2.5 text-[13px] font-semibold text-[var(--mms-color-text-muted)] transition hover:bg-[var(--mms-color-surface-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]">Reset</a>
                @endif
            </form>
        </div>

        {{-- ── Mobile Toolbar ── --}}
        <div class="flex items-center gap-2 lg:hidden">
            <form method="GET" action="{{ route('admin.members.index') }}" class="flex-1">
                <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Cari anggota..." class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] py-2.5 px-3 text-[13px] text-[var(--mms-color-text)] placeholder:text-[var(--mms-color-text-subtle)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
            </form>
            <button type="button" class="relative inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] px-3 text-[var(--mms-color-text-muted)]" x-on:click="filterOpen = true" aria-label="Buka filter" aria-haspopup="dialog" :aria-expanded="filterOpen">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L13 13.414V19a1 1 0 0 1-.553.894l-4 2A1 1 0 0 1 7 21v-7.586L3.293 6.707A1 1 0 0 1 3 6V4Z"/></svg>
                @if ($activeFilterCount > 0)
                    <span class="absolute -right-1 -top-1 grid size-4 place-items-center rounded-full bg-[var(--mms-color-primary)] text-[9px] font-bold text-[var(--mms-color-primary-foreground)]" aria-hidden="true">{{ $activeFilterCount }}</span>
                @endif
            </button>
        </div>

        {{-- ── Mobile Filter Bottom Sheet ── --}}
        <div x-cloak x-show="filterOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-160" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mms-sheet-backdrop lg:hidden" aria-hidden="true" x-on:click="filterOpen = false"></div>
        <div role="dialog" aria-modal="true" aria-labelledby="member-filter-title" x-cloak x-show="filterOpen" x-transition:enter="transition ease-out duration-220" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-160" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="mms-sheet-panel lg:hidden">
            <div class="mx-auto mt-3 h-1 w-12 rounded-full bg-white/20" aria-hidden="true"></div>
            <div class="flex items-center justify-between border-b border-[var(--mms-color-border)] px-4 pb-3 pt-4">
                <h2 id="member-filter-title" class="text-[16px] font-semibold text-[var(--mms-color-text)]">Filter Anggota</h2>
                <button type="button" class="grid size-9 place-items-center rounded-full border border-[var(--mms-color-border)] text-[var(--mms-color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" aria-label="Tutup filter" x-on:click="filterOpen = false">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <form method="GET" action="{{ route('admin.members.index') }}" class="mms-scrollbar overflow-y-auto px-4 pt-4 pb-2" style="max-height: calc(88dvh - 8rem)">
                <div class="space-y-4">
                    <div>
                        <label for="status-mb-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Status Persetujuan</label>
                        <select id="status-mb-m" name="approval_status" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $v => $l)<option value="{{ $v }}" @selected($filters['approval_status'] === $v)>{{ $l }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="resort-mb-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Resort</label>
                        <select id="resort-mb-m" name="resort" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                            <option value="">Semua Resort</option>
                            @foreach ($resorts as $r)<option value="{{ $r }}" @selected($filters['resort'] === $r)>{{ $r }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="mkt-mb-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Marketing</label>
                        <select id="mkt-mb-m" name="marketing_id" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                            <option value="">Semua Marketing</option>
                            @foreach ($marketingOptions as $m)<option value="{{ $m->id }}" @selected((int) $filters['marketing_id'] === $m->id)>{{ $m->code }} - {{ $m->user->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label for="day-mb-m" class="mb-1.5 block text-[13px] font-semibold text-[var(--mms-color-text-muted)]">Hari</label>
                        <select id="day-mb-m" name="day" class="w-full rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] px-3 py-2.5 text-[14px] text-[var(--mms-color-text)] focus:border-[var(--mms-color-border-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--mms-color-focus)]">
                            <option value="">Semua Hari</option>
                            @foreach ($days as $v => $l)<option value="{{ $v }}" @selected($filters['day'] === $v)>{{ $l }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3 pb-4">
                    <a href="{{ route('admin.members.index') }}" class="flex min-h-[44px] items-center justify-center rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] text-[14px] font-semibold text-[var(--mms-color-text-muted)]">Reset</a>
                    <button type="submit" class="min-h-[44px] rounded-[var(--mms-radius-md)] bg-[var(--mms-color-primary)] text-[14px] font-semibold text-[var(--mms-color-primary-foreground)] hover:bg-[var(--mms-color-primary-hover)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]" x-on:click="filterOpen = false">Terapkan</button>
                </div>
            </form>
        </div>

        {{-- ── Content List ── --}}
        @if ($members->isEmpty())
            <x-ui.empty-state title="Belum ada anggota" description="Anggota sesuai filter belum tersedia." />
        @else
            {{-- Mobile Cards --}}
            <div class="grid gap-3 lg:hidden">
                @foreach ($members as $member)
                    <a
                        href="{{ route('admin.members.show', $member) }}"
                        class="rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] p-4 transition hover:border-[var(--mms-color-border-strong)] hover:bg-[var(--mms-color-surface-elevated)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-[15px] font-semibold text-[var(--mms-color-text)]">{{ $member->name }}</p>
                                <p class="text-[12px] text-[var(--mms-color-text-muted)]">{{ $member->member_number }} · {{ $member->resort }}</p>
                            </div>
                            <x-ui.badge :variant="$badgeVariant($member->approval_status->value)">{{ $member->approval_status->label() }}</x-ui.badge>
                        </div>
                        <div class="mt-3 flex items-center justify-between border-t border-[var(--mms-color-border)] pt-3">
                            <div>
                                <p class="text-[10px] text-[var(--mms-color-text-subtle)]">Pinjaman</p>
                                <p class="text-[14px] font-semibold text-[var(--mms-color-primary)]">{{ $rupiah($member->loan_amount) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-[var(--mms-color-text-subtle)]">Marketing</p>
                                <p class="text-[12px] font-semibold text-[var(--mms-color-text)]">{{ $member->marketingProfile->code }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Desktop Table --}}
            <div class="hidden overflow-x-auto rounded-[14px] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] lg:block">
                <table class="w-full text-left text-[13px]" aria-label="Data anggota">
                    <caption class="sr-only">Data anggota</caption>
                    <thead class="bg-[var(--mms-color-surface-muted)] text-[var(--mms-color-text-subtle)]">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">Nama</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Nomor</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Pinjaman</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Resort</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Marketing</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Tanggal</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--mms-color-border)] text-[var(--mms-color-text-muted)]">
                        @foreach ($members as $member)
                            <tr class="hover:bg-[var(--mms-color-surface-muted)]">
                                <td class="px-4 py-3 font-semibold text-[var(--mms-color-text)]">{{ $member->name }}</td>
                                <td class="px-4 py-3">{{ $member->member_number }}</td>
                                <td class="px-4 py-3 font-semibold text-[var(--mms-color-primary)]">{{ $rupiah($member->loan_amount) }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$badgeVariant($member->approval_status->value)">{{ $member->approval_status->label() }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3">{{ $member->resort }}</td>
                                <td class="px-4 py-3 font-semibold text-[var(--mms-color-text)]">{{ $member->marketingProfile->code }}</td>
                                <td class="px-4 py-3">{{ $member->input_date->format('d/m/Y') }} {{ $member->input_time }}</td>
                                <td class="px-4 py-3"><x-ui.link-button :href="route('admin.members.show', $member)" size="sm">Detail</x-ui.link-button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-data.pagination :paginator="$members" class="mt-4" />
        @endif
    </div>
@endsection
