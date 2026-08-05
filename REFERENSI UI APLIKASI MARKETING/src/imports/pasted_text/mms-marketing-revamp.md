Lanjutkan dan revisi project UI “MMS Marketing” yang sudah ada.

Jangan membuat ulang desain dari awal. Pertahankan karakter visual saat ini karena arah desainnya sudah sesuai, yaitu profesional, premium, bersih, menggunakan warna hitam, putih, abu-abu muda, dan emas.

Pertahankan:
- struktur dashboard marketing;
- kartu status tracking;
- warna hitam, putih, dan emas;
- bottom navigation Beranda, Konsumen, Laporan, Riwayat, dan Profil;
- gaya kartu dengan border tipis dan sudut membulat;
- ikon outline;
- bahasa Indonesia;
- ukuran frame utama Android 390 × 844 px.

Tujuan revisi ini adalah melengkapi seluruh alur aplikasi, meningkatkan keterbacaan, memperbaiki navigasi, dan membuat prototype terasa seperti aplikasi perusahaan yang dirancang secara manual, bukan hasil template AI.

==================================================
1. JANGAN MENGUBAH ARAH VISUAL UTAMA
==================================================

Jangan mengganti seluruh layout atau membuat tema baru.

Jangan menggunakan:
- glassmorphism berlebihan;
- gradient mencolok;
- efek neon;
- ilustrasi 3D;
- kartu yang terlalu banyak;
- ikon warna-warni tanpa fungsi;
- dekorasi futuristik;
- teks promosi yang tidak diperlukan.

Gunakan gambar referensi customer hanya untuk menjaga nuansa hitam, putih, dan emas.

Aplikasi ini hanya untuk marketing. Jangan memasukkan:
- dashboard admin;
- daftar semua resort;
- data marketing lain;
- target keuangan;
- drop;
- storting;
- sirkulasi;
- simpanan;
- pinjaman;
- grafik keuangan.

==================================================
2. PERBAIKI UKURAN DAN KETERBACAAN
==================================================

Perbesar teks yang saat ini terlalu kecil.

Gunakan ketentuan:
- judul halaman: 22–24 px;
- judul kartu: 15–17 px;
- isi utama: 14 px;
- keterangan: minimal 12 px;
- teks tombol: minimal 14 px;
- label bottom navigation: minimal 10–11 px;
- tinggi tombol utama: minimal 48 px;
- area tekan ikon dan tombol: minimal 44 × 44 px.

Pastikan semua teks tetap mudah dibaca saat aplikasi digunakan di luar ruangan.

Kurangi penggunaan font 9 px dan 10 px. Ukuran tersebut hanya boleh digunakan untuk informasi yang benar-benar tidak utama, seperti versi aplikasi.

Jaga kontras teks. Jangan menggunakan abu-abu terlalu terang pada latar putih.

==================================================
3. PERBAIKI STRUKTUR LAYOUT DAN SCROLL
==================================================

Perbaiki struktur halaman agar sesuai layar Android.

Gunakan:
- tinggi layar berbasis dynamic viewport;
- konten utama dapat di-scroll secara vertikal;
- bottom navigation selalu terlihat di bagian bawah layar;
- bottom navigation tidak menutupi isi halaman;
- tambahkan padding bawah yang cukup pada setiap halaman;
- dukung safe area bagian atas dan bawah;
- header tidak terpotong oleh status bar Android;
- modal dan bottom sheet tidak keluar dari layar.

Jangan menggunakan container yang membuat halaman panjang terpotong.

==================================================
4. TAMBAHKAN SPLASH SCREEN
==================================================

Buat halaman awal:

01 Splash Screen

Isi:
- latar hitam;
- placeholder logo MMS Group;
- teks “MMS GROUP”;
- teks “Marketing Monitoring”;
- subjudul “KSP Manunggal Makmur Sejahtera”;
- aksen garis emas yang tipis;
- loading indicator sederhana;
- versi aplikasi dalam teks kecil.

Jangan membuat logo baru. Gunakan kotak atau lingkaran placeholder sederhana dengan tulisan “Logo MMS”.

Setelah sekitar 1–2 detik, prototype berpindah ke halaman login.

==================================================
5. TAMBAHKAN HALAMAN LOGIN
==================================================

Buat halaman:

02 Login Marketing

Isi:
- header hitam dan aksen emas;
- placeholder logo MMS Group;
- judul “Selamat Datang”;
- teks “Masuk menggunakan akun marketing Anda”;
- input username;
- input password;
- ikon tampilkan atau sembunyikan password;
- pilihan “Ingat saya”;
- tombol “Masuk”;
- teks bantuan “Hubungi admin jika lupa akun atau password”.

Buat state:
- form normal;
- input kosong;
- password salah;
- akun tidak aktif;
- proses login;
- tidak ada koneksi internet.

Setelah login berhasil, arahkan pengguna ke alur permintaan izin.

==================================================
6. TAMBAHKAN ALUR PERMINTAAN IZIN
==================================================

Buat halaman terpisah dan dapat diklik:

03 Izin Lokasi
04 Izin Lokasi Background
05 Izin Notifikasi
06 Izin Kamera

Gunakan penjelasan yang sederhana.

Izin lokasi:
“Aplikasi membutuhkan akses lokasi untuk mencatat perjalanan marketing.”

Izin lokasi background:
“Izinkan aplikasi mencatat perjalanan saat aplikasi berada di latar belakang.”

Izin notifikasi:
“Notifikasi digunakan untuk memberi tahu bahwa tracking sedang aktif.”

Izin kamera:
“Kamera digunakan untuk mengambil foto laporan kunjungan.”

Tombol:
- “Izinkan”
- “Nanti Saja”

Tambahkan ilustrasi sederhana berupa ikon, bukan ilustrasi 3D.

Setelah semua izin diberikan, arahkan ke dashboard.

==================================================
7. REVISI DASHBOARD MARKETING
==================================================

Pertahankan dashboard saat ini, tetapi lakukan penyesuaian berikut:

Header:
- tampilkan nama marketing;
- tampilkan Resort 3;
- tampilkan foto profil;
- tampilkan ikon notifikasi;
- gunakan area placeholder logo yang netral.

Kartu tracking:
- tetap menjadi kartu utama;
- tampilkan status “Tracking Aktif”;
- tampilkan waktu aktif;
- tampilkan status GPS;
- tampilkan status internet;
- tampilkan status sinkronisasi;
- tampilkan waktu lokasi terakhir;
- buat seluruh kartu dapat diklik menuju detail tracking.

Statistik hari ini:
- Kunjungan;
- Laporan Terkirim;
- Data Konsumen;
- Belum Tersinkron.

Perjelas data:
“2 terkirim, 1 menunggu sinkronisasi”
apabila terdapat tiga laporan tetapi hanya dua yang terkirim.

Menu cepat:
- Tambah Konsumen;
- Buat Laporan;
- Lihat Riwayat;
- Status Sinkronisasi.

Aktivitas terbaru:
- gunakan ukuran teks minimal 12 px;
- tampilkan maksimal tiga aktivitas;
- tombol “Lihat Semua” harus dapat diklik.

Tambahkan banner offline persisten ketika internet terputus:

“Tidak ada koneksi internet. Data tetap disimpan di perangkat.”

Banner menggunakan warna oranye muda dan muncul pada semua halaman, bukan hanya dashboard.

==================================================
8. BUAT DETAIL TRACKING SEBAGAI HALAMAN PENUH
==================================================

Jangan hanya menggunakan bottom sheet sederhana.

Buat halaman:

07 Detail Tracking

Tampilkan:
- status tracking;
- waktu mulai;
- status GPS;
- status koneksi internet;
- status izin lokasi;
- lokasi terakhir;
- waktu terakhir diperbarui;
- jumlah titik lokasi tersimpan;
- jumlah titik belum tersinkron;
- peta mini atau placeholder peta;
- informasi bahwa lokasi dicatat otomatis;
- informasi penggunaan baterai secara sederhana.

Tombol:
- “Perbarui Lokasi”;
- “Coba Sinkronkan”;
- “Buka Pengaturan GPS” apabila GPS mati.

Gunakan badge hijau, oranye, atau merah sesuai kondisi.

==================================================
9. LENGKAPI DATA KONSUMEN
==================================================

Pertahankan halaman daftar konsumen, tetapi tambahkan:

- nomor kontak;
- tanggal kunjungan terakhir;
- alamat singkat;
- status konsumen;
- kolom pencarian;
- tombol filter;
- filter chip:
  - Semua;
  - Hari Ini;
  - Perlu Follow Up;
  - Selesai.

Buat halaman:

08 Daftar Konsumen
09 Tambah Konsumen
10 Detail Konsumen
11 Edit Konsumen

Form tambah dan edit konsumen berisi:
- nama konsumen;
- nomor telepon;
- alamat;
- nama usaha atau pekerjaan, opsional;
- status konsumen;
- hasil awal kunjungan;
- catatan;
- lokasi saat ini;
- tanggal dan waktu.

Status konsumen:
- Baru;
- Tertarik;
- Perlu Follow Up;
- Tidak Tertarik;
- Selesai.

Tombol:
- “Simpan Konsumen”;
- “Batal”.

Tambahkan validasi field wajib.

Detail konsumen menampilkan:
- informasi konsumen;
- lokasi;
- status;
- riwayat kunjungan;
- laporan terakhir;
- foto terakhir;
- tombol “Buat Laporan”;
- tombol “Edit Data”;
- tombol “Hubungi Konsumen”.

==================================================
10. BUAT FORM LAPORAN YANG SEBENARNYA
==================================================

Jangan lagi menggunakan pop-up singkat untuk membuat laporan.

Buat alur beberapa langkah:

12 Pilih Konsumen
13 Isi Hasil Kunjungan
14 Tambah Foto
15 Konfirmasi Laporan
16 Laporan Berhasil
17 Laporan Disimpan Offline

Tahap pilih konsumen:
- pencarian;
- daftar konsumen;
- opsi tambah konsumen baru.

Tahap hasil kunjungan:
- tujuan kunjungan;
- hasil kunjungan;
- status konsumen;
- catatan;
- rencana tindak lanjut;
- tanggal tindak lanjut, opsional.

Pilihan hasil:
- Berhasil Bertemu;
- Tidak Bertemu;
- Tertarik;
- Perlu Follow Up;
- Tidak Tertarik;
- Transaksi Selesai.

Tahap foto:
- tombol “Ambil Foto”;
- tombol “Pilih dari Galeri”;
- preview;
- hapus;
- ganti foto;
- keterangan foto.

Tampilkan informasi otomatis:
- tanggal;
- waktu;
- lokasi;
- nama marketing;
- resort.

Tahap konfirmasi:
- ringkasan konsumen;
- hasil;
- catatan;
- foto;
- lokasi;
- status koneksi.

Tombol:
- “Kirim Laporan”;
- “Simpan sebagai Draft”;
- “Kembali”.

Kondisi berhasil:
“Laporan berhasil dikirim.”

Kondisi offline:
“Laporan disimpan di perangkat dan akan dikirim setelah internet kembali aktif.”

==================================================
11. REVISI HALAMAN LAPORAN
==================================================

Tambahkan tab atau filter:

- Semua;
- Terkirim;
- Menunggu;
- Draft.

Setiap kartu laporan menampilkan:
- nama konsumen;
- hasil kunjungan;
- tanggal dan waktu;
- thumbnail foto;
- lokasi singkat;
- status pengiriman.

Status:
- Terkirim;
- Menunggu Sinkronisasi;
- Draft;
- Gagal.

Tambahkan halaman detail laporan lengkap, bukan bottom sheet umum.

==================================================
12. REVISI RIWAYAT PERJALANAN
==================================================

Pertahankan kartu perjalanan saat ini, tetapi tambahkan filter:

- Hari Ini;
- Minggu Ini;
- Bulan Ini;
- Pilih Tanggal.

Setiap kartu menampilkan:
- tanggal;
- waktu mulai;
- waktu selesai atau status sedang berjalan;
- durasi;
- jarak;
- jumlah titik;
- jumlah kunjungan;
- status sinkronisasi.

Buat halaman:

18 Riwayat Perjalanan
19 Detail Perjalanan

Detail perjalanan berisi:
- peta rute;
- titik awal;
- titik akhir;
- waktu mulai;
- waktu selesai;
- durasi;
- jarak;
- konsumen yang dikunjungi;
- laporan terkait;
- status sinkronisasi.

Jangan menyediakan fitur edit rute.

==================================================
13. BUAT HALAMAN STATUS SINKRONISASI
==================================================

Buat halaman:

20 Status Sinkronisasi

Tampilkan:
- titik lokasi menunggu;
- laporan menunggu;
- foto menunggu;
- sinkronisasi terakhir;
- status internet.

Buat daftar antrean dengan:
- jenis data;
- waktu;
- status;
- progress.

Tombol:
“Sinkronkan Sekarang”

State:
- semua tersinkron;
- sedang mengirim;
- gagal sebagian;
- tidak ada koneksi.

==================================================
14. LENGKAPI PROFIL
==================================================

Pertahankan header profil.

Tambahkan menu:
- Informasi Akun;
- Ubah Password;
- Status Izin Aplikasi;
- Panduan Penggunaan;
- Tentang Aplikasi;
- Status Sinkronisasi;
- Keluar.

Buat halaman Ubah Password yang sebenarnya.

Saat logout, tampilkan modal:

“Keluar dari aplikasi?”

“Tracking perjalanan akan berhenti setelah Anda keluar.”

Tombol:
- “Batal”;
- “Ya, Keluar”.

==================================================
15. TAMBAHKAN KONDISI KHUSUS
==================================================

Buat state:

21 GPS Mati
22 Izin Lokasi Ditolak
23 Internet Terputus
24 Sinkronisasi Gagal
25 Sesi Login Berakhir
26 Empty State
27 Loading State
28 Error State

GPS mati:
- teks “GPS Tidak Aktif”;
- tombol “Buka Pengaturan GPS”.

Izin ditolak:
- teks “Akses lokasi diperlukan agar tracking dapat berjalan”;
- tombol “Buka Pengaturan Aplikasi”.

Offline:
- aplikasi tetap dapat digunakan;
- data baru diberi badge “Belum Tersinkron”.

Loading:
- gunakan skeleton;
- jangan hanya spinner di tengah halaman.

==================================================
16. PERBAIKI INTERAKSI PROTOTYPE
==================================================

Saat ini beberapa tombol hanya membuka satu bottom sheet generik. Ganti dengan navigasi halaman yang sesuai.

Buat interaksi:

Splash → Login
Login → Izin Lokasi
Izin Lokasi → Izin Background
Izin Background → Izin Notifikasi
Izin Notifikasi → Izin Kamera
Izin Kamera → Dashboard

Dashboard → Detail Tracking
Dashboard → Tambah Konsumen
Dashboard → Buat Laporan
Dashboard → Riwayat
Dashboard → Sinkronisasi

Konsumen → Detail Konsumen
Konsumen → Tambah Konsumen
Detail Konsumen → Edit
Detail Konsumen → Buat Laporan

Laporan → Detail Laporan
Buat Laporan → Foto
Foto → Konfirmasi
Konfirmasi → Berhasil atau Tersimpan Offline

Riwayat → Detail Perjalanan
Profil → Ubah Password
Profil → Logout
Logout → Login

Gunakan tombol kembali pada semua halaman detail.

==================================================
17. KONSISTENSI KOMPONEN
==================================================

Buat komponen reusable:

- header;
- bottom navigation;
- tombol utama;
- tombol sekunder;
- input;
- dropdown;
- filter chip;
- status badge;
- kartu konsumen;
- kartu laporan;
- kartu perjalanan;
- banner offline;
- modal konfirmasi;
- upload foto;
- empty state;
- loading skeleton;
- error state.

Gunakan jarak konsisten:
4, 8, 12, 16, 24, dan 32 px.

Gunakan radius konsisten antara 14–18 px.

==================================================
18. PERBAIKI LOGO PLACEHOLDER
==================================================

Jangan membuat lambang atau logo baru yang seolah-olah merupakan logo resmi MMS Group.

Selama logo resmi belum tersedia, gunakan placeholder netral:
- lingkaran atau kotak;
- teks “MMS”;
- label kecil “Logo Placeholder”.

Pastikan placeholder mudah diganti dengan logo resmi nanti.

==================================================
19. PERBAIKI METADATA PROJECT
==================================================

Gunakan:
- judul: “MMS Marketing – Monitoring Perjalanan”
- deskripsi: “Prototype aplikasi Android untuk tracking perjalanan marketing, data konsumen, dan laporan kunjungan.”

Jangan menggunakan deskripsi dashboard resort management atau dashboard admin.

==================================================
20. HASIL AKHIR
==================================================

Hasil revisi harus:

- tetap mempertahankan visual saat ini;
- tidak membuat desain ulang total;
- memiliki seluruh halaman penting;
- dapat diprototipekan dari login sampai logout;
- memiliki kondisi online dan offline;
- memiliki form lengkap;
- memiliki halaman detail, bukan hanya bottom sheet generik;
- nyaman dibaca;
- tidak memotong konten;
- bottom navigation selalu mudah diakses;
- terlihat seperti aplikasi operasional perusahaan yang dirancang oleh UI/UX designer secara manual;
- tetap dapat dijalankan dan tidak menghasilkan error build.

Setelah revisi, susun seluruh frame atau halaman secara berurutan dan beri nama yang jelas.