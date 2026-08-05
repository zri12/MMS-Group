---
title: "Coding Standards"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.2.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Laravel

- Ikuti PSR-12.
- Gunakan strict typing bila sesuai.
- Controller tipis.
- Validasi melalui Form Request.
- Output API melalui API Resource.
- Business transaction kompleks melalui Action/Service.
- Authorization melalui Policy/Gate.
- Gunakan Eloquent relation dan eager loading.
- Hindari N+1.
- Pagination untuk list.
- Gunakan PHP Enum untuk status.
- Gunakan database transaction untuk operasi multi-record.

Struktur contoh:

```text
app/
├── Actions/
├── Enums/
├── Http/
│   ├── Controllers/Admin/
│   ├── Controllers/Api/V1/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Policies/
└── Services/
```

# Blade / Livewire

- View di `resources/views/`.
- Layout di `resources/views/layouts/`.
- Blade component reusable di `resources/views/components/`.
- Design System component dibagi ke folder `ui`, `form`, `data`, `navigation`, dan `feedback`.
- Gunakan `@props` dan `$attributes->class()` pada anonymous Blade component.
- Variant component harus memakai allowlist, bukan class bebas dari input user.
- Atribut `wire:*` harus diteruskan oleh attribute bag.
- Alpine.js hanya untuk interaksi UI lokal seperti toggle password, dropdown, modal, dan tabs.
- Livewire component di `app/Livewire/Admin/` dan viewnya di `resources/views/livewire/`.
- Jangan melakukan query Eloquent di dalam file Blade.
- Jangan hardcode data bisnis.
- Gunakan Livewire properties untuk state form.
- Gunakan wire:model, wire:click, wire:submit.
- Jangan membuat satu Livewire class ribuan baris.
- Pisahkan modul per domain.

# Flutter

- Feature-first atau layer yang konsisten.
- Model serialization eksplisit.
- Repository memisahkan API dan SQLite.
- Provider tidak melakukan SQL/HTTP langsung.
- Dio interceptor untuk token dan error.
- Local UUID untuk create offline.
- Jangan menyimpan token di SharedPreferences biasa.
- Jangan log lokasi/token/foto pada production.

# Naming

- PHP class: PascalCase
- DB/API: snake_case
- Blade variable: camelCase atau snake_case
- Dart variable: camelCase
- Route name: dot notation
- Component: PascalCase (Livewire), kebab-case (Blade component tag)
- Boolean: is/has/can
- Date field: suffix `_date`
- Timestamp: suffix `_at`
- Foreign key: singular `_id`

# Error handling

- Pesan UI Bahasa Indonesia.
- Log server menyimpan context tanpa password/token.
- API tidak mengembalikan stack trace production.
- Flutter memetakan network, validation, unauthorized, timeout.

# Commit

Contoh:

- `feat(prospects): add admin prospect list`
- `fix(tracking): prevent duplicate offline points`
- `docs(api): update member upload contract`

# Quality commands

```bash
php artisan test
npm run build
composer validate
```

Tambahkan formatter/linter sesuai project, tetapi jangan mengganti style massal tanpa kebutuhan.
