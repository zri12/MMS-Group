@extends('layouts.admin')

@section('title', 'Design System - '.config('mms.name'))
@section('page_title', 'Design System')

@section('content')
    <div class="space-y-8">
        <x-navigation.page-header
            title="MMS Blade Design System"
            description="Katalog komponen reusable untuk pemeriksaan tampilan local dan testing."
            :breadcrumbs="[['label' => 'Admin', 'href' => route('admin.home')], ['label' => 'Design System']]"
        />

        <x-ui.card title="Warna dan Tipografi" description="Token black, gold, neutral surface, dan status.">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Background', 'bg-[var(--mms-color-background)]'],
                    ['Surface', 'bg-[var(--mms-color-surface)]'],
                    ['Gold', 'bg-[var(--mms-color-primary)]'],
                    ['Info', 'bg-[var(--mms-color-info)]'],
                ] as [$label, $class])
                    <div class="rounded-[var(--mms-radius-md)] border border-[var(--mms-color-border)] p-3">
                        <div class="{{ $class }} h-12 rounded-[var(--mms-radius-sm)] border border-[var(--mms-color-border)]"></div>
                        <p class="mt-2 text-sm font-semibold text-[var(--mms-color-text)]">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card title="Buttons">
            <div class="flex flex-wrap gap-3">
                <x-ui.button>Primary</x-ui.button>
                <x-ui.button variant="secondary">Secondary</x-ui.button>
                <x-ui.button variant="outline">Outline</x-ui.button>
                <x-ui.button variant="ghost">Ghost</x-ui.button>
                <x-ui.button variant="danger">Danger</x-ui.button>
                <x-ui.button loading="true">Loading</x-ui.button>
                <x-ui.icon-button label="Contoh icon">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"></path></svg>
                </x-ui.icon-button>
            </div>
        </x-ui.card>

        <x-ui.card title="Form">
            <div class="grid gap-5 lg:grid-cols-2">
                <x-form.field label="Nama" for="catalog-name" help="Field dengan teks bantuan.">
                    <x-form.input id="catalog-name" name="catalog_name" value="Contoh Nama" />
                </x-form.field>
                <x-form.field label="Password" for="catalog-password">
                    <x-form.password-input id="catalog-password" name="catalog_password" placeholder="Masukkan password" />
                </x-form.field>
                <x-form.field label="Textarea" for="catalog-note">
                    <x-form.textarea id="catalog-note" name="catalog_note">Data contoh</x-form.textarea>
                </x-form.field>
                <x-form.field label="Select" for="catalog-select">
                    <x-form.select id="catalog-select" name="catalog_select" placeholder="Pilih opsi">
                        <option>Opsi A</option>
                        <option>Opsi B</option>
                    </x-form.select>
                </x-form.field>
                <x-form.checkbox name="catalog_checkbox" label="Checkbox aktif" checked="true" />
                <x-form.switch name="catalog_switch" label="Switch aktif" checked="true" />
            </div>
        </x-ui.card>

        <x-ui.card title="Feedback dan Status">
            <div class="grid gap-4 lg:grid-cols-2">
                <x-ui.alert variant="success" title="Success">Tindakan berhasil diproses.</x-ui.alert>
                <x-ui.alert variant="warning" title="Warning">Periksa kembali data sebelum melanjutkan.</x-ui.alert>
                <x-ui.alert variant="danger" title="Danger">Tindakan tidak dapat dilanjutkan.</x-ui.alert>
                <x-ui.alert variant="info" title="Info">Informasi tambahan tersedia.</x-ui.alert>
            </div>
            <div class="mt-5 flex flex-wrap gap-3">
                <x-ui.badge variant="primary">Primary</x-ui.badge>
                <x-ui.badge variant="success">Aktif</x-ui.badge>
                <x-ui.badge variant="warning">Menunggu</x-ui.badge>
                <x-ui.badge variant="danger">Ditolak</x-ui.badge>
                <x-ui.status-indicator variant="online" label="Online" />
                <x-ui.status-indicator variant="offline" label="Offline" />
            </div>
        </x-ui.card>

        <section class="grid gap-4 lg:grid-cols-3">
            <x-ui.stat-card label="Label" value="24" helper="Nilai netral" />
            <x-ui.empty-state title="Belum ada data" description="Area kosong tampil tenang dan jelas." />
            <x-ui.error-state title="Terjadi kendala" description="Pesan kendala tetap generik." />
        </section>

        <x-ui.card title="Table">
            <x-data.table caption="Tabel contoh">
                <x-data.table-head>
                    <tr>
                        <x-data.table-cell heading="true">Nama</x-data.table-cell>
                        <x-data.table-cell heading="true">Status</x-data.table-cell>
                        <x-data.table-cell heading="true">Catatan</x-data.table-cell>
                    </tr>
                </x-data.table-head>
                <x-data.table-body>
                    <x-data.table-row>
                        <x-data.table-cell>Contoh Nama</x-data.table-cell>
                        <x-data.table-cell><x-ui.badge variant="success">Aktif</x-ui.badge></x-data.table-cell>
                        <x-data.table-cell>Data contoh</x-data.table-cell>
                    </x-data.table-row>
                    <x-data.table-row selected="true">
                        <x-data.table-cell>Contoh Lain</x-data.table-cell>
                        <x-data.table-cell><x-ui.badge variant="warning">Menunggu</x-ui.badge></x-data.table-cell>
                        <x-data.table-cell>Baris terpilih</x-data.table-cell>
                    </x-data.table-row>
                </x-data.table-body>
            </x-data.table>
            <x-data.pagination class="mt-4" />
        </x-ui.card>

        <x-ui.card title="Overlay dan Navigasi">
            <div class="flex flex-wrap gap-3">
                <x-ui.button type="button" x-on:click="$dispatch('open-modal', 'catalog-modal')">Buka Modal</x-ui.button>
                <x-ui.button type="button" variant="outline" x-on:click="$dispatch('open-modal', 'catalog-confirm')">Buka Konfirmasi</x-ui.button>
                <x-ui.dropdown label="Dropdown">
                    <a href="#" class="block rounded-[var(--mms-radius-sm)] px-3 py-2 text-sm text-[var(--mms-color-text)] hover:bg-[var(--mms-color-surface-muted)]">Item Satu</a>
                    <a href="#" class="block rounded-[var(--mms-radius-sm)] px-3 py-2 text-sm text-[var(--mms-color-text)] hover:bg-[var(--mms-color-surface-muted)]">Item Dua</a>
                </x-ui.dropdown>
                <x-ui.tooltip text="Tooltip contoh">
                    <x-ui.icon-button label="Info tooltip">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11v5m0-8h.01"></path><circle cx="12" cy="12" r="9"></circle></svg>
                    </x-ui.icon-button>
                </x-ui.tooltip>
            </div>

            <x-ui.tabs
                class="mt-5"
                :tabs="['ringkasan' => 'Ringkasan', 'detail' => 'Detail']"
                :panels="[
                    'ringkasan' => 'Panel ringkasan aktif melalui keyboard dan klik.',
                    'detail' => 'Panel detail terhubung dengan atribut aksesibilitas.',
                ]"
            />
        </x-ui.card>

        <x-ui.card title="Loading dan Skeleton">
            <div class="grid gap-4 sm:grid-cols-3">
                <x-ui.loading-state />
                <x-ui.skeleton />
                <div class="space-y-3">
                    <x-ui.skeleton shape="line" />
                    <x-ui.skeleton shape="line" class="w-2/3" />
                    <x-ui.skeleton shape="circle" />
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.modal name="catalog-modal" title="Modal Contoh">
        <p class="text-sm leading-6 text-[var(--mms-color-text-muted)]">Konten modal tetap responsif dan dapat ditutup dengan Escape.</p>
    </x-ui.modal>

    <x-ui.confirmation-dialog name="catalog-confirm" title="Konfirmasi Contoh" message="Dialog ini hanya contoh tampilan.">
        <x-slot:confirm>
            <x-ui.button type="button" variant="primary" x-on:click="closeDialog()">Konfirmasi</x-ui.button>
        </x-slot:confirm>
    </x-ui.confirmation-dialog>
@endsection
