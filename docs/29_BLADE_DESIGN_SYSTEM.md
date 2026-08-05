---
title: "MMS Blade Design System"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# MMS Blade Design System

## 1. Tujuan

Design system ini menjadi fondasi UI reusable Web Admin MMS. Tujuannya adalah menjaga konsistensi Modern Calm Black-Gold, accessibility, responsive behavior, dan kompatibilitas Livewire tanpa menambahkan package UI pihak ketiga.

## 2. Technology

- Blade anonymous components.
- Livewire-compatible attribute forwarding.
- Tailwind CSS 4.
- Alpine.js untuk interaksi UI lokal.
- Vite.

## 3. Visual Direction

- Background black dan neutral dark surface.
- Gold hanya untuk aksi utama, active state, selected state, dan focus state.
- Status memakai success, warning, danger, info, online, dan offline.
- Mobile-first dan nyaman untuk monitoring operasional.
- Tidak memakai gradient/glow berat atau dekorasi yang mengganggu.

## 4. Design Tokens

Token utama berada di `apps/web-admin-api/resources/css/app.css`:

- `--mms-color-background`
- `--mms-color-background-subtle`
- `--mms-color-surface`
- `--mms-color-surface-elevated`
- `--mms-color-surface-muted`
- `--mms-color-border`
- `--mms-color-border-strong`
- `--mms-color-border-gold`
- `--mms-color-text`
- `--mms-color-text-muted`
- `--mms-color-text-subtle`
- `--mms-color-primary`
- `--mms-color-primary-hover`
- `--mms-color-primary-active`
- `--mms-color-primary-foreground`
- `--mms-color-success`
- `--mms-color-warning`
- `--mms-color-danger`
- `--mms-color-info`
- `--mms-color-online`
- `--mms-color-offline`
- `--mms-color-focus`
- `--mms-radius-sm`, `--mms-radius-md`, `--mms-radius-lg`, `--mms-radius-xl`
- `--mms-shadow-sm`, `--mms-shadow-md`, `--mms-shadow-lg`
- `--mms-sidebar-width`, `--mms-header-height`, `--mms-mobile-nav-height`
- `--mms-transition-fast`, `--mms-transition-normal`

Alias lama seperti `--bg-primary` dan `--gold-primary` tetap tersedia selama transisi.

## 5. Component Structure

Komponen berada di `apps/web-admin-api/resources/views/components/`:

- `ui/`: button, icon-button, link-button, badge, status-indicator, alert, card, stat-card, divider, spinner, skeleton, empty-state, error-state, loading-state, avatar, logo, modal, confirmation-dialog, dropdown, tooltip, tabs.
- `form/`: field, label, input, password-input, textarea, select, checkbox, radio, switch, error, help-text, input-group.
- `data/`: table, table-head, table-body, table-row, table-cell, table-empty, pagination, description-list, description-item.
- `navigation/`: sidebar, sidebar-item, mobile-nav, mobile-nav-item, topbar, breadcrumb, page-header.
- `feedback/`: flash-messages, validation-summary, toast.

## 6. Component API

Contoh:

```blade
<x-ui.button type="submit">Simpan</x-ui.button>
<x-form.input name="username" :value="old('username')" />
<x-form.password-input name="password" show-label="Tampilkan password" />
<x-ui.alert variant="success">Data tersimpan.</x-ui.alert>
<x-ui.card title="Informasi">Konten</x-ui.card>
<x-ui.badge variant="warning">Menunggu</x-ui.badge>
<x-data.table caption="Tabel contoh">...</x-data.table>
<x-ui.modal name="modal-name" title="Judul">...</x-ui.modal>
<x-navigation.page-header title="Profil Admin" />
```

Variant component memakai allowlist. Atribut HTML dan `wire:*` diteruskan melalui attribute bag.

## 7. Layout

Admin shell memakai:

- desktop sidebar tetap dengan width `--mms-sidebar-width`;
- topbar ringkas;
- mobile bottom navigation;
- content max width;
- flash message terpusat di layout.

Route navigasi admin saat ini menampilkan Beranda, Marketing, Prospek, Anggota, Laporan, Tracking, Profil, Logout, dan Design System pada local/testing.

## 8. Accessibility

- Semua form control memiliki label.
- Password toggle memakai `type="button"`, dynamic `aria-label`, dan `aria-pressed`.
- Icon-only button memakai `aria-label`.
- Modal memakai `role="dialog"` dan `aria-modal="true"`.
- Dropdown memakai `aria-expanded`.
- Alert memakai `role="status"` atau `role="alert"`.
- Loading state memakai `aria-busy`.
- Status tidak hanya dibedakan oleh warna.
- Focus-visible menggunakan gold lembut.

## 9. Responsive Behavior

- Mobile content memakai padding bawah agar tidak tertutup bottom navigation.
- Table memakai wrapper horizontal scroll, bukan body overflow.
- Sidebar hanya desktop pada tahap ini.
- Bottom navigation hanya mobile.
- Modal memakai width responsif dan padding viewport.

## 10. Livewire Compatibility

Komponen tidak menyimpan state server dan meneruskan atribut seperti `wire:model`, `wire:click`, `wire:loading.attr`, dan `wire:target`. Alpine hanya dipakai untuk interaksi lokal seperti password toggle, dropdown, modal, dan tabs.

Tracking map memakai Leaflet.js dari Vite bundle, OpenStreetMap tile, tombol refresh, fit marker, fullscreen native, dan popup berbasis text content.

## 11. Component Catalog

Katalog tersedia hanya pada environment `local` dan `testing`:

```text
GET /admin/design-system
name: admin.design-system
middleware: auth, active, admin
```

Route ini tidak didaftarkan pada production.

## 12. Testing

Test terkait:

- `tests/Feature/DesignSystem/DesignSystemPageTest.php`
- `tests/Feature/DesignSystem/BladeComponentRenderTest.php`
- `tests/Feature/DesignSystem/LayoutAccessTest.php`

## 13. Non-Scope

- Flutter.
- Offline SQLite Flutter.
- Background tracking Flutter.
- Deployment.

## 14. Contribution Rules

- Tambahkan variant hanya melalui allowlist.
- Jangan membuat class dari input user tanpa mapping.
- Jangan membuat query di Blade component.
- Jangan menambahkan package UI besar.
- Pertahankan token `--mms-*` sebagai sumber styling utama.
