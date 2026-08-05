---
title: "Development Seed Data"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.1.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Tujuan

Seeder menjaga data development konsisten dengan UI yang telah disetujui. Seeder tidak boleh dipakai di production kecuali initial data master yang disetujui.

# Admin

Contoh development:

- Nama: Admin KSP MMS
- Username: admin
- Role: admin
- Password: gunakan environment/seeder development, jangan dokumentasikan password production.

# Marketing M01-M13

| Kode | Nama | Area |
|---|---|---|
| M01 | Deden | Gedebage |
| M02 | Angil | Rancasari |
| M03 | Ari | Buahbatu |
| M04 | Feri | Ujungberung |
| M05 | Sukma | Cibiru |
| M06 | Sandi | Antapani |
| M07 | Vikri | Kiaracondong |
| M08 | Farhad | Cicaheum |
| M09 | Doni | Sukajadi |
| M10 | Faiz | Lengkong |
| M11 | Agung | Arcamanik |
| M12 | Faisal | Cimahi |
| M13 | Agnes | Cileunyi |

Username format development:

- `m01.deden`
- `m02.angil`
- dan seterusnya.

# Prospek Utama M01

1. Ahmad Hidayat
   - Toko Kelontong
   - Tertarik
   - Gedebage
2. Siti Nurjanah
   - Warung Sembako
   - Perlu Follow Up
   - Rancasari
3. Toko Berkah Jaya
   - Grosir
   - Baru
   - Buahbatu

# Anggota

Seeder development membuat 39 anggota:

- 13 marketing.
- 3 anggota per marketing.
- 13 status Menunggu.
- 13 status Disetujui.
- 13 status Ditolak.
- Semua `source_prospect_id` bernilai null sampai aturan konversi prospek menjadi anggota dikonfirmasi.

Anggota utama M01:

1. Herman Malik - 0468 - Menunggu
2. Wawan Setiawan - 0469 - Disetujui
3. Yuli Astuti - 0470 - Ditolak

# Jadwal Senin M01

- 08.30 Ahmad Hidayat - Presentasi produk tabungan - Selesai
- 10.00 Herman Malik - Survei pengajuan anggota - Berlangsung
- 13.30 Siti Nurjanah - Follow up produk simpanan - Belum Dikunjungi

# Tracking M01

- Mulai: Kantor KSP MMS, 08.05
- Perjalanan antar titik
- Kunjungan: Ahmad Hidayat
- Perjalanan antar titik
- Kunjungan: Herman Malik
- Perjalanan antar titik
- Kunjungan: Siti Nurjanah
- Perjalanan menuju akhir
- Selesai: Gedebage, 15.30
- Jarak contoh: 8,4 km
- Jumlah kunjungan: 3
- Jumlah titik tracking: 9

# Dashboard Reference

Seeder development harus menghasilkan angka berikut dari database:

| Field | Total |
|---|---:|
| Anggota | 39 |
| Target | 123.500.000 |
| Drop | 91.000.000 |
| Storting | 37.700.000 |

Jangan hardcode angka di komponen web. Komponen UI wajib membaca agregat dari database.

Angka dashboard development berasal dari:

- `daily_operational_reports` tanggal Senin, 20 Juli 2026, berisi 13 laporan marketing.
- `operational_recap_rows` tanggal Senin, 20 Juli 2026, berisi 13 row marketing dengan total sama.

# Tanggal Simulasi

Gunakan tanggal kalender yang benar. Contoh pekan:

- Senin, 20 Juli 2026
- Selasa, 21 Juli 2026
- Rabu, 22 Juli 2026
- Kamis, 23 Juli 2026
- Jumat, 24 Juli 2026
- Sabtu, 25 Juli 2026

# Laporan Operasional Harian

Seeder development membuat 13 laporan operasional untuk Senin, 20 Juli 2026, satu laporan per marketing.

Target aggregate Senin:

| Field | Total |
|---|---:|
| Target | 123.500.000 |
| Drop | 91.000.000 |
| Storting | 37.700.000 |

# Rekap Operasional

Seeder development membuat rekap Senin sampai Sabtu, 20-25 Juli 2026.

Aturan seed rekap:

- Satu header per tanggal.
- Setiap header memiliki 13 row marketing.
- Nilai uang disimpan eksplisit.
- `percentage` bernilai null karena rumus persentase belum dikonfirmasi customer.
- `previous_circulation` dan `current_circulation` bernilai 0 sebagai fixture eksplisit development, bukan hasil formula bisnis.
- Tidak ada formula runtime untuk menghasilkan angka rekap.
