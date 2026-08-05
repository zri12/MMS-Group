# MMS Marketing Mobile

Folder ini disiapkan untuk **aplikasi marketing Flutter** yang merupakan bagian dari
MMS Marketing Monitoring System oleh KSP Manunggal Makmur Sejahtera.

## Status

Project Flutter **belum dibuat** pada tahap ini. Folder ini adalah placeholder
yang akan diisi setelah REST API inti stabil.

## Rencana Teknologi

| Bagian | Teknologi |
|---|---|
| Framework | Flutter |
| Language | Dart |
| HTTP client | Dio |
| Autentikasi | Laravel Sanctum (token via REST API) |
| Local database | SQLite melalui sqflite |
| Token storage | flutter_secure_storage |
| GPS | geolocator |
| Maps | flutter_map + OpenStreetMap |
| Background sync | workmanager |
| State management | Provider |
| Routing | go_router |

## Integrasi

- Aplikasi Flutter mengakses server melalui REST API pada `/api/v1`.
- Flutter **tidak** terhubung langsung ke MySQL.
- Autentikasi menggunakan Laravel Sanctum personal access token.
- Data offline disimpan menggunakan SQLite (`sqflite`).
- Sinkronisasi ke server menggunakan `local_uuid` untuk mencegah duplikasi.

## Catatan

- Jangan membuat source Flutter pada tahap ini.
- Jangan menjalankan `flutter create` pada tahap ini.
- Project Flutter dibuat setelah REST API inti stabil dan diuji.
