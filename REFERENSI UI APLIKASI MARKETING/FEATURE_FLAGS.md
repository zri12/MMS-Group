# Feature Flags — MMS Marketing

Feature flags menjaga UI versi lengkap tetap tersimpan tanpa menampilkannya pada prototype customer **Basic Version**. Konfigurasi ada di `src/config/featureFlags.ts` dan tidak pernah dirender di UI.

## Basic Version (default)
`fullVersion: false` hanya menampilkan alur utama: login ringkas, dashboard, konsumen, satu foto laporan, laporan sederhana, riwayat sederhana, profil, dan logout.

## Full Version
Semua halaman dan komponen lanjutan tetap ada di proyek. Aktifkan fitur spesifik atau gunakan `fullVersion: true` untuk menampilkan kembali menu dan kontrol terkait.

Contoh:
```ts
reportDraft: true          // tampilkan Draft Laporan
mobileJourneyMap: true     // tampilkan peta rute perjalanan
advancedCharts: true       // tersedia untuk UI admin lengkap
fullVersion: true          // tampilkan seluruh fitur lengkap
```

Untuk menyembunyikan kembali fitur, ubah nilainya ke `false`. Jangan menambahkan kontrol feature flag ke prototype customer.
