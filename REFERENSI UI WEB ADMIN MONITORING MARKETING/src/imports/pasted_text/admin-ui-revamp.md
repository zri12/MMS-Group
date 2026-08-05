Lanjutkan revisi project UI web admin KSP Marketing Monitoring yang sudah ada.

Jangan membuat ulang project dari awal. Pertahankan tema black dan gold, logo resmi KSP, bottom navigation mobile, sidebar desktop, dashboard, status marketing horizontal, tombol hari Senin sampai Sabtu, dan tabel rekap yang sudah dibuat.

Project ini hanya front-end dan prototype. Tidak perlu backend, database, GPS nyata, Maps API, upload nyata, atau perhitungan nyata.

Fokus hanya melengkapi fitur dan alur yang masih belum tersedia.

1. Tambahkan halaman Login Admin sebelum Dashboard.
- Logo resmi KSP.
- Username.
- Password.
- Ingat saya.
- Tombol Masuk.
- Logout harus kembali ke Login.

2. Perbaiki arti Total Anggota.
- Total Anggota berarti jumlah anggota atau nasabah, bukan jumlah marketing.
- Gunakan data dummy Total Anggota 39.
- Jumlah marketing tetap 13 dan ditampilkan pada bagian Status Marketing.
- Pastikan angka anggota konsisten pada dashboard, tabel, dan detail.

3. Konsistensikan data keuangan.
Gunakan total yang sama pada kartu dan hasil penjumlahan 13 marketing:
- Total Target: Rp123.500.000
- Total Drop: Rp91.000.000
- Total Storting: Rp37.700.000
- Total Anggota: 39

4. Buat detail berbeda untuk setiap kartu:
- Detail Total Anggota.
- Detail Total Target.
- Detail Total Drop.
- Detail Total Storting.

Jangan membuka drawer yang sama untuk semua kartu.

5. Lengkapi tabel Rekap Target Harian.
Pertahankan:
- Identitas.
- Anggota: MG, L, M, K, S.
- Target: Lalu, MSK, KLR, S.
- Drop: Lalu, Kini, Total.
- Storting: Lalu, Kini, Total.

Tambahkan:
- Persentase.
- Sirkulasi Lalu.
- Sirkulasi Sekarang.
- Dibuat Oleh.
- Keterangan.

Pada mobile gunakan horizontal scroll dan sticky column Marketing serta Kode.

6. Buat jadwal berbeda untuk setiap marketing.
Tambahkan:
- Halaman Jadwal Marketing.
- Detail Jadwal.
- Form Tambah Jadwal.
- Form Edit Jadwal.

Field:
- Marketing.
- Hari.
- Tanggal.
- Jam mulai.
- Jam selesai.
- Area atau tujuan.
- Catatan.
- Status.

Jangan gunakan jadwal 08.00–16.00 yang sama untuk seluruh marketing.

7. Lengkapi Data Anggota berdasarkan hari.

Buat halaman:
- Daftar Data Anggota.
- Detail Anggota.

Daftar anggota harus dapat difilter berdasarkan:
- Hari.
- Tanggal.
- Marketing.
- Resort.
- Status ACC.
- Pencarian nama atau nomor anggota.

Detail anggota menampilkan:
- Foto anggota atau nasabah.
- Resort.
- Tanggal.
- Nama.
- Nomor anggota.
- Nomor pinjaman.
- Alamat.
- Nomor HP.
- Usaha.
- Pinjaman.
- Angsuran.
- Asuransi.
- Jaminan.
- Status ACC.
- Lokasi.
- Peta lokasi.
- Marketing pembuat.
- Tanggal dan waktu input.
- Foto bukti transfer jika tersedia.

Ketika anggota diklik, buka Detail Anggota, bukan Detail Marketing.

Jangan gunakan gambar formulir sebagai thumbnail anggota. Gunakan foto dummy lokal yang netral.

8. Lengkapi Laporan Harian berdasarkan hari.

Buat halaman:
- Daftar Laporan Harian.
- Detail Laporan Harian.

Gunakan field:
- Marketing.
- Kode.
- Hari.
- Tanggal.
- Storting.
- Drop.
- Tabungan Keluar.
- Lain-lain.
- Target Lama dalam Rupiah dan jumlah orang.
- Target Masuk dalam Rupiah dan jumlah orang.
- Target Keluar dalam Rupiah dan jumlah orang.
- Jumlah Target dalam Rupiah dan jumlah orang.
- Drop Baru.
- Drop Lanjut.
- Foto Anggota atau Nasabah.
- Foto Bukti Transfer.
- Lokasi.
- Alamat.
- Tanggal dan waktu input.

Jangan tampilkan:
- Kas Bon.
- Potongan Admin.
- Potongan Tabungan.
- Asuransi pada laporan setoran.
- Jumlah Uang Tunai.
- Plus.
- Min.
- Sirkulasi pada form laporan.

Ubah teks “Laporan Kunjungan” menjadi “Laporan Operasional Harian”.

9. Buat halaman Bukti Kunjungan.

Tampilkan:
- Foto anggota.
- Foto bukti transfer.
- Nama anggota.
- Nomor anggota.
- Marketing.
- Kode marketing.
- Lokasi.
- Alamat.
- Peta.
- Tanggal.
- Waktu.
- Catatan singkat.

Gunakan dua foto dummy lokal.

Hubungkan tombol Bukti Kunjungan pada Detail Marketing ke halaman tersebut.

10. Lengkapi Tracking.

Tampilkan seluruh 13 marketing.

Tambahkan:
- Filter hari.
- Filter marketing.
- Filter status.
- Pencarian.
- Tombol Refresh.
- Waktu pembaruan terakhir.
- Detail tracking.
- Riwayat perjalanan.
- Timeline perjalanan.

Timeline berisi:
- Titik awal.
- Perjalanan.
- Kunjungan pertama.
- Perjalanan berikutnya.
- Titik kunjungan.
- Titik akhir.

Saat marketing offline tampilkan:
“Marketing sedang offline. Lokasi terakhir diperbarui pukul 14.20.”

Gunakan marker:
- Hijau untuk aktif.
- Merah untuk offline.
- Abu-abu untuk belum mulai.

11. Buat form prototype yang sebenarnya untuk:
- Tambah Marketing.
- Edit Marketing.
- Atur Jadwal.
- Reset Password.
- Ubah Password Admin.

Jangan hanya menampilkan toast.

12. Hubungkan tombol yang belum berfungsi:
- Lihat Semua Marketing.
- Refresh.
- Bukti Kunjungan.
- Tambah Marketing.
- Edit Marketing.
- Jadwal.
- Ubah Password.
- Logout.

13. Buat state:
- Belum ada anggota.
- Belum ada jadwal.
- Belum ada laporan harian.
- Belum ada bukti kunjungan.
- Marketing offline.
- Marketing belum mulai.
- Loading.
- Error.
- Hasil pencarian kosong.
- Sesi login habis.

14. Pertahankan responsive.
- Mobile memakai bottom navigation.
- Desktop memakai sidebar.
- Tabel dapat digeser di mobile.
- Detail tampil sebagai halaman penuh pada HP.
- Bottom navigation tidak menutupi konten.

15. Jangan menambahkan:
- Backend.
- Database nyata.
- API nyata.
- Maps API nyata.
- Export Excel nyata.
- Export PDF.
- Approval laporan.
- Audit log.
- WhatsApp otomatis.
- Email otomatis.
- Payroll.
- Absensi wajah.
- Chat.
- AI.

Pastikan seluruh tombol utama dapat diklik sebagai prototype dan project tetap berjalan tanpa error build.