Lanjutkan revisi kecil project UI web admin “MMS Marketing Monitoring” yang sudah ada.

Project ini hanya berupa front-end dan prototype UI untuk memberikan gambaran kepada customer. Tidak perlu membuat backend, database, autentikasi nyata, GPS nyata, API, atau integrasi peta nyata.

Jangan membuat ulang project dari awal.

Pertahankan seluruh tampilan utama yang sekarang:
- tema hitam, putih, abu-abu muda, dan emas;
- halaman login;
- sidebar;
- enam menu utama;
- enam kartu ringkasan;
- peta monitoring;
- tabel;
- panel detail dari sisi kanan;
- pagination;
- form marketing;
- tampilan responsive;
- font dan komponen yang sudah digunakan.

Fokus hanya pada revisi berikut.

==================================================
1. PERBAIKI WARNA BADGE STATUS
==================================================

Perbaiki logika status agar teks “GPS Tidak Aktif” tidak dianggap sebagai status hijau hanya karena mengandung kata “Aktif”.

Gunakan aturan warna dengan urutan berikut:

Merah:
- GPS Tidak Aktif
- Gagal

Oranye:
- Offline
- Menunggu Sinkronisasi

Abu-abu:
- Belum Tracking
- Nonaktif

Hijau:
- Tracking Aktif
- Aktif
- Terkirim
- Tersinkron
- Selesai

Biru:
- Sedang Berjalan

Pastikan:
- GPS Tidak Aktif selalu merah;
- Tracking Aktif selalu hijau;
- akun Nonaktif tidak berwarna hijau;
- Sedang Berjalan mudah dibedakan dari Tersinkron.

Jangan mengubah bentuk badge yang sekarang.

==================================================
2. HILANGKAN KONTROL DEMO DARI LOGIN
==================================================

Pada halaman Login Admin, sembunyikan tombol:

- Contoh field kosong
- Contoh password salah

State visual error tetap disimpan untuk kebutuhan pengembangan, tetapi tombol demonstrasi tidak boleh muncul pada tampilan customer.

Pertahankan:
- Username;
- Password;
- tampilkan atau sembunyikan password;
- Ingat saya;
- tombol Masuk;
- pesan bantuan.

==================================================
3. UBAH JUDUL AKTIVITAS TERBARU
==================================================

Pada Dashboard, bagian yang saat ini berjudul:

Aktivitas Terbaru

sebenarnya menampilkan status dan lokasi marketing.

Ubah judul menjadi:

Status Marketing Terbaru

Ubah subjudul menjadi:

Posisi dan status terakhir beberapa marketing hari ini.

Pertahankan tabel:
- Marketing;
- Lokasi Terakhir;
- Diperbarui;
- Status Tracking;
- Detail.

Tombol “Lihat Riwayat” boleh tetap tersedia dan menuju Riwayat Perjalanan.

==================================================
4. BEDAKAN FORM TAMBAH DAN EDIT MARKETING
==================================================

Jangan menggunakan judul “Tambah Marketing” untuk semua kondisi.

Buat dua mode dari form yang sama:

A. Tambah Marketing

Judul:
Tambah Marketing

Field dalam keadaan kosong.

Tombol utama:
Simpan Marketing

B. Edit Marketing

Judul:
Edit Data Marketing

Field sudah terisi berdasarkan marketing yang dipilih.

Tombol utama:
Simpan Perubahan

Gunakan komponen form yang sama agar desain tetap konsisten.

Jangan menghapus data dummy atau halaman yang sudah ada.

==================================================
5. HUBUNGKAN TOMBOL EDIT MARKETING
==================================================

Pada panel Detail Marketing, tombol:

Edit Data Marketing

harus benar-benar membuka form Edit Data Marketing dengan data pengguna yang dipilih.

Jangan hanya menampilkan toast atau pesan.

Alur prototype:

Detail Marketing
→ Edit Data Marketing
→ Simpan Perubahan
→ Toast “Data marketing berhasil diperbarui”
→ Kembali ke Detail Marketing atau Data Marketing.

==================================================
6. SAMAKAN ALUR LOGOUT
==================================================

Tombol logout dari sidebar sudah menggunakan modal konfirmasi.

Terapkan modal yang sama pada tombol:

Keluar dari akun

di halaman Profil Admin.

Alur:

Profil Admin
→ Keluar dari akun
→ Modal “Keluar dari dashboard?”
→ Batal atau Ya, Keluar
→ Login Admin.

Jangan langsung logout tanpa konfirmasi.

==================================================
7. PERBAIKI STATUS DETAIL PERJALANAN
==================================================

Pada Detail Perjalanan, bedakan:

Status Perjalanan:
- Selesai
- Sedang Berjalan

Status Sinkronisasi:
- Tersinkron
- Menunggu Sinkronisasi
- Gagal

Untuk perjalanan Budi Santoso gunakan:

Status Perjalanan: Selesai
Status Sinkronisasi: Tersinkron

Jangan menggunakan kata “Selesai” sebagai status sinkronisasi.

==================================================
8. GUNAKAN FOTO LAPORAN DUMMY
==================================================

Gunakan satu atau beberapa aset foto dummy lokal yang netral dan sesuai konteks kunjungan marketing.

Tampilkan foto pada:
- thumbnail di tabel Laporan Kunjungan;
- foto besar pada Detail Laporan;
- laporan terakhir pada Detail Konsumen.

Jangan memakai ikon lokasi sebagai pengganti foto.

Jangan menggunakan URL gambar eksternal. Simpan sebagai aset lokal project agar tetap muncul ketika prototype dibuka.

Foto hanya visual dummy dan tidak perlu memiliki fungsi upload nyata.

==================================================
9. PERBAIKI PETA DETAIL PERJALANAN
==================================================

Pertahankan peta utama Monitoring Lokasi.

Perbaiki peta pada Detail Perjalanan agar tidak hanya berupa kotak garis putus-putus.

Gunakan mock map sederhana dengan:
- bentuk jalan;
- garis rute berwarna emas;
- titik awal berwarna hijau;
- titik kunjungan berwarna emas;
- titik akhir berwarna hitam atau merah;
- label Gedebage;
- label Toko Berkah Jaya;
- label Buahbatu;
- informasi durasi dan jumlah kunjungan.

Peta tetap hanya prototype visual dan tidak memerlukan Maps API.

==================================================
10. BERSIHKAN PLACEHOLDER LOGO
==================================================

Karena logo resmi MMS Group belum tersedia, pertahankan monogram:

MMS

dan teks:

MMS GROUP

Namun, hapus tulisan:

LOGO MMS GROUP

dari halaman login dan bagian lain yang terlihat customer.

Jangan membuat logo baru yang seolah-olah resmi.

==================================================
11. PERTAHANKAN SCOPE BASIC
==================================================

Jangan menambahkan:
- Data Resort;
- CRUD Resort;
- admin per resort;
- role dan permission;
- grafik kompleks;
- ranking;
- ekspor PDF atau Excel;
- approval laporan;
- audit log;
- notifikasi WhatsApp atau email;
- analisis performa;
- analisis kecepatan;
- analisis jarak;
- target;
- drop;
- storting;
- sirkulasi;
- payroll;
- absensi wajah;
- chat.

==================================================
HASIL AKHIR
==================================================

Pastikan:
- tampilan utama tidak berubah;
- badge GPS Tidak Aktif berwarna merah;
- kontrol demo login tidak tampil;
- judul tabel dashboard sesuai isinya;
- tambah dan edit marketing dapat dibedakan;
- tombol edit benar-benar membuka form;
- seluruh logout menggunakan konfirmasi;
- status perjalanan dan sinkronisasi tidak tercampur;
- foto laporan terlihat nyata sebagai dummy;
- peta perjalanan terlihat rapi;
- tulisan LOGO MMS GROUP tidak tampil;
- desain tetap responsive;
- project berhasil dijalankan tanpa error build.