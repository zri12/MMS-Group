---
title: "Business Rules"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# 1. Aturan akun marketing

- Kode marketing unik.
- Format kode awal: `M01` sampai `M99`.
- Username unik.
- Akun nonaktif tidak dapat login.
- Menonaktifkan akun tidak menghapus data historis.
- Marketing hanya mengakses data sendiri.

# 2. Hari kerja

- Hari yang digunakan UI: Senin–Sabtu.
- Marketing dapat memiliki kombinasi hari kerja berbeda.
- Tidak memiliki hari kerja pada hari terpilih berarti status `Tidak Dijadwalkan`, bukan `Tidak Aktif`.
- Minggu tidak ditampilkan pada versi awal.

# 3. Prospek

- Prospek dimiliki satu marketing.
- Status:
  - Baru
  - Tertarik
  - Perlu Follow Up
  - Tidak Tertarik
  - Selesai
- Prospek dapat memiliki banyak laporan kunjungan.
- Prospek dapat dikaitkan ke satu anggota.
- Konversi ke anggota tidak boleh terjadi otomatis hanya karena status `Selesai`.
- Marketing hanya mengedit prospek miliknya.
- Admin melihat seluruh prospek.

# 4. Anggota

- Anggota dibuat dari Flutter.
- Status awal selalu `Menunggu`.
- Marketing tidak dapat mengirim status `Disetujui` atau `Ditolak`.
- Hanya admin dapat memperbarui persetujuan.
- Transisi:
  - Menunggu → Disetujui
  - Menunggu → Ditolak
  - Perubahan ulang setelah final harus membutuhkan konfirmasi dan audit; detail final pending.
- Nomor anggota dan nomor pinjaman harus unik.
- Foto anggota terkait dengan data anggota, bukan laporan operasional.

# 5. Laporan operasional

- Laporan dimiliki satu marketing.
- Field hanya yang tercantum di Data Dictionary.
- Total target dari Flutter dikirim eksplisit dan dapat diverifikasi server.
- Server boleh memvalidasi bahwa nominal tidak negatif.
- Aturan satu laporan per marketing per tanggal belum dikonfirmasi.
- Tidak ada bukti transfer, foto anggota, lokasi setoran, atau field Lain-lain pada modul ini.

# 6. Laporan kunjungan

- Laporan kunjungan selalu terkait prospek.
- Prospek harus dimiliki marketing terautentikasi.
- Status prospek setelah kunjungan diperbarui dalam transaction yang sama.
- Foto kunjungan maksimal satu pada scope saat ini.
- Follow-up date optional.
- Hasil:
  - Berhasil Bertemu
  - Tidak Bertemu
  - Transaksi Selesai

# 7. Jadwal

- Jadwal dibuat dan dikelola admin.
- Status:
  - Belum Dikunjungi
  - Berlangsung
  - Selesai
  - Dibatalkan
- Transisi minimum:
  - Belum Dikunjungi → Berlangsung
  - Belum Dikunjungi → Dibatalkan
  - Berlangsung → Selesai
  - Berlangsung → Dibatalkan
- Jadwal selesai tidak diedit marketing.
- Admin dapat melakukan koreksi dengan audit bila diperlukan.

# 8. Tracking

- Marketing hanya boleh memiliki satu sesi tracking aktif.
- Tracking hanya dimulai oleh user terautentikasi.
- Titik lokasi harus memiliki waktu rekam dari perangkat.
- Server menyimpan `received_at` untuk membedakan keterlambatan sinkronisasi.
- Status web:
  - Aktif
  - Offline
  - Belum Mulai
  - GPS Tidak Aktif
  - Tidak Dijadwalkan
- Definisi batas waktu Offline dan frekuensi titik masih pending confirmation.
- Web admin versi awal menggunakan polling, bukan WebSocket.
- Tracking offline disimpan SQLite dan dikirim batch.

# 9. Status sinkronisasi

Flutter lokal:

- Draft
- Menunggu Sinkronisasi
- Terkirim
- Gagal

Web/API:

- Tersinkronisasi
- Menunggu Sinkronisasi
- Gagal

Mapping:

- Terkirim → Tersinkronisasi
- Menunggu Sinkronisasi → Menunggu Sinkronisasi
- Gagal → Gagal
- Draft tidak dikirim dan tidak tampil di admin

# 10. Rekap operasional

Struktur tabel dikunci:

- MG
- Anggota L/M/K/S
- Target Lalu/MSK/KLR/S
- Drop Lalu/Kini/Total
- Storting Lalu/Kini/Total
- %
- Sirkulasi Lalu
- Sirkulasi Sekarang
- Diikuti Oleh
- Kas Pagi

Aturan penting:

- Data rekap tidak boleh dibuat dengan rumus dummy.
- Arti dan rumus seluruh kolom yang belum dikonfirmasi harus disimpan eksplisit.
- Jangan menghitung persentase otomatis sebelum rumus disetujui.
- Tabel tetap satu tabel utuh di mobile dan desktop.
- Satu row mewakili satu marketing.

# 11. Nilai uang

- Tidak boleh negatif.
- Disimpan dalam rupiah tanpa format.
- UI menggunakan format `Rp`.
- Pengguna Flutter memasukkan angka, bukan string berformat.
- Nilai sangat besar harus memakai BIGINT.

# 12. Waktu dan tanggal

- UI Bahasa Indonesia.
- Zona waktu operasional Asia/Jakarta.
- Hari harus sesuai tanggal kalender.
- Server menjadi sumber waktu penerimaan.
- Perangkat mengirim waktu kejadian untuk offline sync.

# 13. Penghapusan data

- Data transaksi/laporan tidak dihapus permanen melalui UI versi awal.
- Master marketing dinonaktifkan.
- Prospek/anggota boleh soft delete untuk kebutuhan koreksi.
- Penghapusan file mengikuti lifecycle record.

# 14. Aturan pending

Hal berikut tidak boleh diasumsikan:

- arti MG;
- arti L/M/K/S;
- rumus target/drop/storting/sirkulasi/persentase;
- definisi Diikuti Oleh;
- Kas Pagi;
- satu atau banyak laporan operasional per hari;
- batas offline tracking;
- interval pengiriman titik.
