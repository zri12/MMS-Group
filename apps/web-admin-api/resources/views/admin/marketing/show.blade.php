@extends('layouts.admin')

@section('title', 'Detail Marketing - '.config('mms.name'))
@section('page_title', 'Detail Marketing')

@php
    $rupiah = fn (int $value): string => 'Rp '.number_format($value, 0, ',', '.');
    $dateLabel = fn ($date): string => $date ? $date->format('d/m/Y') : '-';
@endphp

@section('content')
    <div class="space-y-6" x-data="{ activeTab: 'summary' }">
        <x-navigation.page-header
            title="{{ $marketing->user->name }}"
            description="{{ $marketing->code }} - {{ $marketing->area }}"
            :breadcrumbs="[
                ['label' => 'Admin', 'href' => route('admin.home')],
                ['label' => 'Marketing', 'href' => route('admin.marketing.index')],
                ['label' => $marketing->code],
            ]"
        >
            <x-slot:actions>
                <x-ui.link-button :href="route('admin.marketing.edit', $marketing)" variant="outline">Edit</x-ui.link-button>
            </x-slot:actions>
        </x-navigation.page-header>

        <section class="overflow-hidden rounded-[var(--mms-radius-xl)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface)]">
            <div class="grid gap-0 lg:grid-cols-[0.95fr_1.35fr]">
                <div class="border-b border-[var(--mms-color-border)] bg-[var(--mms-color-sidebar)] p-5 sm:p-6 lg:border-b-0 lg:border-r">
                    <div class="flex items-start gap-4">
                        <x-ui.avatar :name="$marketing->user->name" :src="$photoUrl" size="lg" class="size-20 rounded-[var(--mms-radius-lg)]" />
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-[var(--mms-color-primary-hover)]">Profil Marketing</p>
                            <h2 class="mt-2 truncate text-2xl font-semibold text-[var(--mms-color-text)]">{{ $marketing->user->name }}</h2>
                            <p class="mt-1 text-sm text-[var(--mms-color-text-muted)]">{{ $marketing->user->username }} · {{ $marketing->phone ?? 'Nomor HP belum diisi' }}</p>
                        </div>
                    </div>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <x-ui.badge :variant="$marketing->user->is_active ? 'success' : 'danger'">{{ $marketing->user->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                        <x-ui.badge variant="primary">{{ $marketing->code }}</x-ui.badge>
                        <x-ui.badge variant="neutral">{{ $marketing->latestTrackingSession?->status->label() ?? 'Belum Mulai' }}</x-ui.badge>
                    </div>
                    <div class="mt-5 flex flex-wrap gap-2">
                        @forelse ($marketing->workDays as $workDay)
                            <x-ui.badge variant="neutral">{{ $workDay->day_name->label() }}</x-ui.badge>
                        @empty
                            <x-ui.badge variant="neutral">Hari kerja belum diatur</x-ui.badge>
                        @endforelse
                    </div>
                </div>

                <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-6 xl:grid-cols-4">
                    <x-ui.stat-card label="Prospek" :value="$summary['prospects']" />
                    <x-ui.stat-card label="Anggota" :value="$summary['members']" />
                    <x-ui.stat-card label="Jadwal" :value="$summary['schedules']" />
                    <x-ui.stat-card label="Kunjungan" :value="$summary['visits']" />
                    <x-ui.stat-card label="Total Target" :value="$rupiah($summary['total_target'])" />
                    <x-ui.stat-card label="Total Drop" :value="$rupiah($summary['total_drop'])" />
                    <x-ui.stat-card label="Total Storting" :value="$rupiah($summary['total_storting'])" />
                    <x-ui.stat-card label="Sesi Tracking" :value="$summary['tracking_sessions']" />
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <div class="mms-scrollbar flex gap-2 overflow-x-auto pb-2" role="tablist" aria-label="Detail marketing">
                @foreach ($tabs as $key => $label)
                    <button
                        type="button"
                        class="shrink-0 rounded-full border px-4 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--mms-color-focus)]"
                        x-bind:class="activeTab === '{{ $key }}' ? 'border-[var(--mms-color-border-gold)] bg-[var(--mms-color-primary)] text-[var(--mms-color-primary-foreground)]' : 'border-[var(--mms-color-border)] bg-[var(--mms-color-surface)] text-[var(--mms-color-text-muted)] hover:bg-[var(--mms-color-surface-muted)]'"
                        x-on:click="activeTab = '{{ $key }}'"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div x-show="activeTab === 'summary'">
                <section class="grid gap-4 lg:grid-cols-3">
                    <x-ui.card title="Identitas" class="lg:col-span-2">
                        <x-data.description-list class="grid-cols-1 sm:grid-cols-2">
                            <x-data.description-item label="Kode">{{ $marketing->code }}</x-data.description-item>
                            <x-data.description-item label="Username">{{ $marketing->user->username }}</x-data.description-item>
                            <x-data.description-item label="Email">{{ $marketing->user->email ?? '-' }}</x-data.description-item>
                            <x-data.description-item label="Nomor HP">{{ $marketing->phone ?? '-' }}</x-data.description-item>
                            <x-data.description-item label="Area">{{ $marketing->area }}</x-data.description-item>
                            <x-data.description-item label="Login Terakhir">{{ $marketing->user->last_login_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '-' }}</x-data.description-item>
                        </x-data.description-list>
                    </x-ui.card>

                    <x-ui.card title="Status Akses" description="Menonaktifkan akun akan mencabut token perangkat aktif.">
                        <x-ui.confirmation-dialog
                            name="marketing-status"
                            :title="$marketing->user->is_active ? 'Nonaktifkan marketing' : 'Aktifkan marketing'"
                            :message="$marketing->user->is_active ? 'Akun tidak dapat masuk sampai diaktifkan kembali.' : 'Akun dapat masuk kembali setelah diaktifkan.'"
                        >
                            <x-slot:trigger>
                                <x-ui.button type="button" :variant="$marketing->user->is_active ? 'danger' : 'primary'">
                                    {{ $marketing->user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </x-ui.button>
                            </x-slot:trigger>
                            <x-slot:confirm>
                                <form method="POST" action="{{ route('admin.marketing.status', $marketing) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $marketing->user->is_active ? 0 : 1 }}">
                                    <x-ui.button type="submit" :variant="$marketing->user->is_active ? 'danger' : 'primary'">
                                        {{ $marketing->user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </x-ui.button>
                                </form>
                            </x-slot:confirm>
                        </x-ui.confirmation-dialog>
                    </x-ui.card>
                </section>
            </div>

            <div x-show="activeTab === 'schedules'" x-cloak>
                <x-ui.card title="Jadwal Marketing">
                    <div class="grid gap-3 lg:grid-cols-2">
                        @forelse ($tabData['schedules'] as $schedule)
                            <article class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-[var(--mms-color-text)]">{{ $schedule->agenda }}</p>
                                        <p class="mt-1 text-xs text-[var(--mms-color-text-muted)]">{{ $dateLabel($schedule->schedule_date) }} · {{ $schedule->start_time }}{{ $schedule->end_time ? ' - '.$schedule->end_time : '' }}</p>
                                    </div>
                                    <x-ui.badge variant="primary">{{ $schedule->status->label() }}</x-ui.badge>
                                </div>
                                <p class="mt-3 text-sm text-[var(--mms-color-text-muted)]">{{ $schedule->destination ?? $schedule->area }}</p>
                            </article>
                        @empty
                            <x-ui.empty-state title="Belum ada jadwal" />
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <div x-show="activeTab === 'tracking'" x-cloak>
                <x-ui.card title="Tracking Lokasi">
                    <div class="space-y-3">
                        @forelse ($tabData['tracking'] as $session)
                            <a href="{{ route('admin.tracking.show', $session) }}" class="block rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4 transition hover:border-[var(--mms-color-border-strong)]">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-[var(--mms-color-text)]">{{ $dateLabel($session->session_date) }}</p>
                                        <p class="mt-1 text-xs text-[var(--mms-color-text-muted)]">{{ $session->latestPoint?->address ?? $session->schedule?->destination ?? 'Lokasi terakhir belum tersedia' }}</p>
                                    </div>
                                    <x-ui.badge :variant="$session->status->value === 'Aktif' ? 'success' : 'neutral'">{{ $session->status->label() }}</x-ui.badge>
                                </div>
                            </a>
                        @empty
                            <x-ui.empty-state title="Belum ada tracking" />
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <div x-show="activeTab === 'prospects'" x-cloak>
                <x-ui.card title="Prospek Terbaru">
                    <div class="grid gap-3 lg:grid-cols-2">
                        @forelse ($tabData['prospects'] as $prospect)
                            <a href="{{ route('admin.prospects.show', $prospect) }}" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4">
                                <p class="font-semibold text-[var(--mms-color-text)]">{{ $prospect->name }}</p>
                                <p class="mt-1 text-xs text-[var(--mms-color-text-muted)]">{{ $prospect->business }} · {{ $prospect->resort }}</p>
                                <div class="mt-3"><x-ui.badge variant="neutral">{{ $prospect->status->label() }}</x-ui.badge></div>
                            </a>
                        @empty
                            <x-ui.empty-state title="Belum ada prospek" />
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <div x-show="activeTab === 'members'" x-cloak>
                <x-ui.card title="Anggota Terbaru">
                    <div class="grid gap-3 lg:grid-cols-2">
                        @forelse ($tabData['members'] as $member)
                            <a href="{{ route('admin.members.show', $member) }}" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4">
                                <p class="font-semibold text-[var(--mms-color-text)]">{{ $member->name }}</p>
                                <p class="mt-1 text-xs text-[var(--mms-color-text-muted)]">{{ $member->member_number }} · {{ $rupiah($member->loan_amount) }}</p>
                                <div class="mt-3"><x-ui.badge variant="neutral">{{ $member->approval_status->label() }}</x-ui.badge></div>
                            </a>
                        @empty
                            <x-ui.empty-state title="Belum ada anggota" />
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <div x-show="activeTab === 'operational'" x-cloak>
                <x-ui.card title="Setoran">
                    <div class="space-y-3">
                        @forelse ($tabData['operational'] as $report)
                            <a href="{{ route('admin.operational-reports.show', $report) }}" class="grid gap-3 rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4 sm:grid-cols-4">
                                <div>
                                    <p class="text-xs text-[var(--mms-color-text-muted)]">Tanggal</p>
                                    <p class="font-semibold text-[var(--mms-color-text)]">{{ $dateLabel($report->report_date) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-[var(--mms-color-text-muted)]">Target</p>
                                    <p class="font-semibold text-[var(--mms-color-text)]">{{ $rupiah($report->total_target_amount) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-[var(--mms-color-text-muted)]">Drop</p>
                                    <p class="font-semibold text-[var(--mms-color-text)]">{{ $rupiah($report->drop_amount) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-[var(--mms-color-text-muted)]">Storting</p>
                                    <p class="font-semibold text-[var(--mms-color-text)]">{{ $rupiah($report->storting) }}</p>
                                </div>
                            </a>
                        @empty
                            <x-ui.empty-state title="Belum ada setoran" />
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <div x-show="activeTab === 'visits'" x-cloak>
                <x-ui.card title="Kunjungan">
                    <div class="grid gap-3 lg:grid-cols-2">
                        @forelse ($tabData['visits'] as $visit)
                            <a href="{{ route('admin.visit-reports.show', $visit) }}" class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4">
                                <p class="font-semibold text-[var(--mms-color-text)]">{{ $visit->prospect?->name ?? 'Prospek tidak tersedia' }}</p>
                                <p class="mt-1 text-xs text-[var(--mms-color-text-muted)]">{{ $dateLabel($visit->visit_date) }} · {{ $visit->resort }}</p>
                                <div class="mt-3"><x-ui.badge variant="neutral">{{ $visit->visit_result->label() }}</x-ui.badge></div>
                            </a>
                        @empty
                            <x-ui.empty-state title="Belum ada kunjungan" />
                        @endforelse
                    </div>
                </x-ui.card>
            </div>

            <div x-show="activeTab === 'journeys'" x-cloak>
                <x-ui.card title="Riwayat Perjalanan">
                    <div class="space-y-3">
                        @forelse ($tabData['journeys'] as $session)
                            <a href="{{ route('admin.journeys.show', $session) }}" class="block rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] bg-[var(--mms-color-surface-muted)] p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-[var(--mms-color-text)]">{{ $dateLabel($session->session_date) }}</p>
                                        <p class="mt-1 text-xs text-[var(--mms-color-text-muted)]">{{ $session->distance_meters !== null ? number_format($session->distance_meters / 1000, 2, ',', '.').' km' : 'Jarak belum tersedia' }} · {{ $session->visit_count }} kunjungan</p>
                                    </div>
                                    <x-ui.badge variant="neutral">{{ $session->status->label() }}</x-ui.badge>
                                </div>
                            </a>
                        @empty
                            <x-ui.empty-state title="Belum ada riwayat" />
                        @endforelse
                    </div>
                </x-ui.card>
            </div>
        </section>

        <x-ui.card title="Reset Password" description="Token perangkat marketing akan dicabut setelah password diperbarui.">
            <form method="POST" action="{{ route('admin.marketing.reset-password', $marketing) }}" class="grid gap-4 lg:grid-cols-[1fr_1fr_auto]">
                @csrf
                @method('PATCH')
                <x-form.field label="Password Baru" for="password" required="true" :error="$errors->first('password')">
                    <x-form.password-input id="password" name="password" autocomplete="new-password" required :invalid="$errors->has('password')" />
                </x-form.field>
                <x-form.field label="Konfirmasi Password" for="password_confirmation" required="true" :error="$errors->first('password_confirmation')">
                    <x-form.password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" required :invalid="$errors->has('password_confirmation')" />
                </x-form.field>
                <div class="flex items-end">
                    <x-ui.button type="submit" variant="outline">Perbarui</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
