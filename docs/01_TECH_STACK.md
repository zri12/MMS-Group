---
title: "Technology Stack"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.1.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Web admin dan backend

| Bagian | Teknologi | Keputusan |
|---|---|---|
| Framework | Laravel 12 | Backend, web, API, auth, storage |
| PHP | PHP 8.2+ | Menyesuaikan cPanel |
| Template | Laravel Blade | Halaman admin server-side |
| Interaktif | Laravel Livewire | Komponen dinamis tanpa SPA terpisah |
| Micro-interaksi | Alpine.js (bawaan Livewire) | Toggle, dropdown, modal ringan |
| Styling | Tailwind CSS v4 | Mengikuti prototype |
| Build | Vite | Build asset Laravel |
| Auth web | Laravel session | Admin web |
| Auth mobile | Laravel Sanctum | Token Flutter |
| ORM | Eloquent | Database access |
| Validation | Form Request | Validasi terpusat |
| Serialization | API Resource | Respons Flutter |
| Maps web | Leaflet.js + OpenStreetMap | Peta web admin |
| Icons | SVG inline via Blade component | Tidak membutuhkan library icon besar |
| Testing | PHPUnit (bawaan Laravel) | Konsisten dengan fondasi |

# Aplikasi marketing

| Bagian | Teknologi |
|---|---|
| Framework | Flutter |
| Language | Dart |
| State management | Provider |
| HTTP client | Dio |
| Routing | go_router |
| Local database | sqflite |
| Background sync | workmanager |
| Connectivity | connectivity_plus |
| GPS | geolocator |
| Map | flutter_map + OpenStreetMap |
| Camera/gallery | image_picker |
| Image display/cache | cached_network_image |
| Token storage | flutter_secure_storage |
| Date/number formatting | intl |
| UUID offline | uuid |

# Database dan storage

- Database utama: MySQL 8 atau MariaDB kompatibel.
- Database perangkat: SQLite.
- Foto server: Laravel Storage `public` disk.
- Path file disimpan di MySQL.
- Foto tidak disimpan sebagai base64.
- Backup database dan storage dilakukan terpisah.

# Hosting

Target awal:

- Shared hosting cPanel yang mendukung PHP 8.2+, MySQL, Composer/Terminal, SSL, Cron Jobs, dan document root ke `/public`.
- Build frontend dapat dilakukan di komputer lokal bila cPanel tidak menyediakan Node.js.
- Web dan API berada pada domain yang sama:
  - `/admin/...`
  - `/api/v1/...`

# Teknologi yang tidak digunakan pada versi awal

- Firebase
- Supabase
- MongoDB
- Node.js/Express backend
- React / React DOM
- TypeScript / TSX
- Inertia.js
- React Router
- React Leaflet
- React Context
- WebSocket/Reverb
- Redis wajib
- Microservices
- Kubernetes
- GraphQL
- Object storage eksternal, kecuali storage cPanel tidak mencukupi

# Kebijakan versi

- Gunakan versi stable yang kompatibel dengan Laravel 12 dan PHP hosting.
- Jangan melakukan major upgrade saat modul sedang dikerjakan.
- Setiap upgrade dependency harus diuji dengan test dan build.
- Lock file Composer dan NPM harus disimpan di repository.
