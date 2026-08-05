Lanjutkan revisi project UI web admin “MMS Marketing Monitoring” yang sudah ada.

Project ini hanya berupa front-end dan prototype UI untuk memberikan gambaran kepada customer. Tidak perlu membuat backend, database, autentikasi nyata, GPS nyata, API, upload file nyata, atau integrasi peta nyata.

Jangan membuat ulang project dari awal.

Pertahankan:
- tema hitam, putih, abu-abu muda, dan emas;
- sidebar hitam;
- bentuk kartu;
- tabel;
- map dummy;
- panel detail dari sisi kanan;
- font;
- layout desktop;
- responsive drawer pada mobile;
- enam menu sidebar;
- seluruh komponen yang sudah dibuat.

Fokus revisi pada kelengkapan halaman, konsistensi data dummy, keterbacaan tabel, dan interaksi prototype.

==================================================
1. PERTAHANKAN SCOPE BASIC VERSION
==================================================

Dashboard admin ini merupakan versi basic sesuai anggaran Rp2.000.000.

Menu sidebar tetap:

1. Dashboard
2. Monitoring Lokasi
3. Data Marketing
4. Riwayat Perjalanan
5. Data Konsumen
6. Laporan Kunjungan

Bagian bawah sidebar:

- Profil Admin
- Keluar

Jangan menambahkan:

- halaman Data Resort;
- CRUD Resort;
- admin per resort;
- role dan permission;
- grafik kompleks;
- ranking;
- ekspor PDF;
- ekspor Excel;
- cetak laporan;
- approval laporan;
- audit log;
- pengaturan sistem;
- WhatsApp otomatis;
- email otomatis;
- analisis performa;
- analisis kecepatan;
- analisis jarak;
- target;
- drop;
- storting;
- sirkulasi;
- transaksi;
- pinjaman;
- simpanan;
- payroll;
- absensi wajah;
- chat.

==================================================
2. TAMBAHKAN HALAMAN LOGIN ADMIN
==================================================

Buat halaman Login Admin sebelum dashboard.

Tampilan:
- sisi kiri berwarna hitam;
- aksen garis emas;
- placeholder logo MMS Group;
- teks “MMS GROUP”;
- judul “Dashboard Monitoring Marketing”;
- subjudul “KSP Manunggal Makmur Sejahtera”;
- form login pada kartu putih.

Field:
- Username;
- Password;
- ikon tampilkan/sembunyikan password;
- checkbox “Ingat saya”;
- tombol “Masuk”.

Tambahkan teks:
“Hubungi pengelola sistem apabila lupa akun atau password.”

Buat state visual:
- normal;
- field kosong;
- password salah;
- akun tidak aktif;
- proses login;
- server tidak merespons.

Alur prototype:
Login → Dashboard.

Saat admin melakukan logout:
Logout → Modal Konfirmasi → Login.

==================================================
3. HAPUS IKON NOTIFIKASI
==================================================

Hapus ikon lonceng dan titik notifikasi pada header.

Fitur notifikasi internal tidak termasuk versi basic.

Jangan menyisakan ruang kosong pada header.

Header tetap menampilkan:
- tombol sidebar;
- breadcrumb;
- tanggal;
- waktu pembaruan terakhir;
- tombol Refresh;
- avatar admin;
- nama admin;
- dropdown profil.

==================================================
4. KONSISTENSIKAN DATA DUMMY
==================================================

Gunakan data ringkasan yang sama pada seluruh halaman:

- Total Marketing: 13
- Marketing Aktif: 8
- Marketing Offline: 3
- Belum Tracking atau GPS Mati: 2
- Perjalanan Hari Ini: 10
- Total Konsumen: 126
- Laporan Hari Ini: 8

Gunakan data tersebut secara konsisten pada:
- dashboard;
- monitoring lokasi;
- data marketing;
- riwayat perjalanan;
- data konsumen;
- laporan kunjungan.

Tabel tidak harus menampilkan seluruh data sekaligus. Gunakan pagination:
- halaman pertama 1–10;
- halaman kedua 11–13.

==================================================
5. REVISI DASHBOARD UTAMA
==================================================

Buat enam kartu ringkasan:

1. Total Marketing
2. Marketing Aktif
3. Marketing Offline
4. Belum Tracking
5. Total Konsumen
6. Laporan Hari Ini

Gunakan angka dummy yang telah ditentukan.

Pertahankan:
- peta posisi marketing;
- panel Perlu Ditindaklanjuti;
- tabel aktivitas hari ini.

Panel Perlu Ditindaklanjuti menampilkan:
- GPS tidak aktif;
- belum memulai tracking;
- marketing offline terlalu lama.

Aktivitas terbaru maksimal lima data.

Tambahkan tombol:
- Buka Monitoring;
- Lihat Data Marketing;
- Lihat Riwayat.

Semua tombol harus dapat diklik.

==================================================
6. REVISI PETA DAN MARKER
==================================================

Pertahankan desain map dummy yang sudah ada, tetapi buat lebih informatif.

Tambahkan:
- bentuk jalan yang lebih jelas;
- nama wilayah;
- marker marketing;
- legenda status;
- tombol zoom dummy;
- waktu pembaruan terakhir.

Warna marker:
- hijau: Tracking Aktif;
- oranye: Offline;
- merah: GPS Tidak Aktif;
- abu-abu: Belum Tracking.

Jangan menggunakan satu warna hitam untuk seluruh marker.

Saat marker diklik, buka panel Detail Marketing.

==================================================
7. LENGKAPI MONITORING LOKASI
==================================================

Tambahkan filter:

- Semua Resort;
- Resort 1 sampai Resort 10;
- Semua Marketing;
- Semua Status;
- kolom pencarian;
- tombol Refresh.

Status:
- Tracking Aktif;
- Offline;
- GPS Tidak Aktif;
- Belum Tracking.

Daftar marketing menampilkan:
- avatar;
- nama;
- resort;
- status;
- lokasi terakhir;
- waktu terakhir diperbarui.

Panel Detail Marketing menampilkan:
- nama;
- username atau ID;
- resort;
- status akun;
- status tracking;
- status internet;
- lokasi terakhir;
- waktu terakhir diperbarui;
- waktu tracking dimulai;
- jumlah kunjungan hari ini;
- jumlah laporan hari ini.

Tombol:
- Lihat Perjalanan;
- Lihat Laporan;
- Edit Data Marketing.

Semua tombol hanya prototype perpindahan halaman.

==================================================
8. LENGKAPI DATA MARKETING
==================================================

Pada halaman Data Marketing tampilkan:

- jumlah akun;
- pencarian nama atau username;
- filter resort;
- filter status akun;
- filter status tracking;
- tombol Tambah Marketing.

Tabel:

- No.
- Marketing
- Username atau ID
- Resort
- Nomor Telepon
- Status Akun
- Status Tracking
- Terakhir Aktif
- Aksi

Aksi:
- Lihat Detail;
- Edit Data;
- Reset Password;
- Aktifkan atau Nonaktifkan.

Jangan menggunakan tombol hapus permanen.

Buat:
- Form Tambah Marketing;
- Form Edit Marketing;
- Detail Marketing;
- Modal Reset Password;
- Modal Aktifkan Akun;
- Modal Nonaktifkan Akun.

==================================================
9. FORM TAMBAH DAN EDIT MARKETING
==================================================

Field:
- Nama Lengkap;
- Username;
- ID Marketing;
- Nomor Telepon;
- Resort;
- Password Awal;
- Konfirmasi Password;
- Status Akun.

Pilihan Resort:
- Resort 1;
- Resort 2;
- Resort 3;
- Resort 4;
- Resort 5;
- Resort 6;
- Resort 7;
- Resort 8;
- Resort 9;
- Resort 10.

Validasi visual:
- Nama wajib diisi;
- Username wajib diisi;
- Nomor telepon wajib diisi;
- Resort wajib dipilih;
- Password minimal delapan karakter;
- Konfirmasi password tidak sama.

Tombol:
- Simpan;
- Batal.

Tidak perlu fungsi penyimpanan nyata.

==================================================
10. LENGKAPI RIWAYAT PERJALANAN
==================================================

Tambahkan filter:

- Resort;
- Marketing;
- Tanggal Mulai;
- Tanggal Selesai;
- Status;
- Terapkan Filter;
- Reset.

Tabel:

- Tanggal
- Marketing
- Resort
- Waktu Mulai
- Waktu Selesai
- Durasi
- Jumlah Kunjungan
- Status
- Aksi

Status:
- Selesai;
- Sedang Berjalan;
- Menunggu Sinkronisasi.

Buat halaman Detail Perjalanan.

Detail menampilkan:
- nama marketing;
- resort;
- tanggal;
- waktu mulai;
- waktu selesai;
- durasi;
- jumlah kunjungan;
- status sinkronisasi;
- peta rute dummy;
- titik awal;
- titik kunjungan;
- titik akhir;
- konsumen yang dikunjungi;
- laporan terkait.

Jangan menampilkan:
- analisis kecepatan;
- waktu berhenti;
- skor produktivitas;
- ranking;
- analisis performa.

==================================================
11. REVISI DATA KONSUMEN
==================================================

Hapus tombol “Tambah Konsumen” dari halaman admin.

Admin basic hanya melihat data konsumen yang dikirim dari aplikasi marketing.

Tambahkan filter:
- pencarian;
- resort;
- marketing;
- status;
- tanggal kunjungan.

Tabel:

- Nama Konsumen
- Nomor Kontak
- Alamat
- Status
- Marketing
- Resort
- Kunjungan Terakhir
- Aksi

Status:
- Baru;
- Tertarik;
- Follow Up;
- Selesai.

Aksi hanya:
- Lihat Detail.

Buat halaman atau panel Detail Konsumen.

Detail menampilkan:
- nama;
- nomor kontak;
- alamat;
- lokasi pada peta dummy;
- status;
- marketing;
- resort;
- tanggal kunjungan terakhir;
- catatan;
- riwayat kunjungan;
- laporan terakhir;
- satu foto laporan.

Jangan tampilkan:
- Edit Konsumen;
- Hapus Konsumen;
- Tambah Konsumen;
- Hubungi melalui WhatsApp.

==================================================
12. REVISI LAPORAN KUNJUNGAN
==================================================

Jangan menggunakan status “Dalam Perjalanan” untuk laporan.

Buat kartu ringkasan:
- Total Hari Ini;
- Terkirim;
- Menunggu Sinkronisasi;
- Gagal.

Tambahkan filter:
- resort;
- marketing;
- tanggal;
- hasil kunjungan;
- status konsumen;
- status pengiriman.

Tabel:

- Foto
- Konsumen
- Marketing
- Resort
- Hasil Kunjungan
- Tanggal dan Waktu
- Lokasi
- Status Pengiriman
- Aksi

Hasil kunjungan:
- Berhasil Bertemu;
- Tidak Bertemu;
- Tertarik;
- Tidak Tertarik.

Status pengiriman:
- Terkirim;
- Menunggu Sinkronisasi;
- Gagal.

Gunakan foto dummy lokal sebagai thumbnail.

Buat halaman Detail Laporan.

Detail menampilkan:
- foto laporan;
- nama konsumen;
- marketing;
- resort;
- tujuan kunjungan;
- hasil kunjungan;
- status konsumen;
- catatan;
- lokasi;
- peta dummy;
- tanggal dan waktu;
- status pengiriman.

Jangan tampilkan:
- Setujui;
- Tolak;
- Minta Revisi;
- Ekspor;
- Cetak;
- Kirim WhatsApp.

==================================================
13. PERBAIKI PROFIL DAN LOGOUT
==================================================

Pertahankan Profil Admin dan Ubah Password.

Saat tombol Simpan Password ditekan:
- tampilkan feedback “Password berhasil diperbarui”;
- kembali ke profil admin.

Saat logout:
- tampilkan modal konfirmasi;
- tombol “Ya, Keluar” harus menuju halaman Login.

==================================================
14. PERBAIKI INTERAKSI PROTOTYPE
==================================================

Pastikan tombol berikut dapat diklik:

- Masuk;
- Buka Monitoring;
- Lihat Data Marketing;
- Lihat Riwayat;
- Refresh;
- Tambah Marketing;
- Lihat Detail Marketing;
- Edit Marketing;
- Reset Password;
- Aktifkan Akun;
- Nonaktifkan Akun;
- Detail Perjalanan;
- Detail Konsumen;
- Detail Laporan;
- Profil Admin;
- Ubah Password;
- Logout.

Tidak perlu fungsi nyata. Cukup perpindahan halaman, modal, drawer, panel, loading, atau toast.

Jangan menampilkan tombol yang tidak memiliki tujuan prototype.

==================================================
15. PERBAIKI RESPONSIVE
==================================================

Desktop:
- sidebar tetap;
- tabel lebar dan rapi;
- panel detail dari sisi kanan.

Tablet:
- sidebar dapat menjadi drawer;
- filter dapat berpindah baris;
- tabel menggunakan horizontal scroll.

Mobile:
- sidebar menjadi drawer;
- kartu ringkasan menjadi dua kolom atau satu kolom;
- peta tetap dapat dilihat;
- tabel penting dapat berubah menjadi kartu;
- tombol tidak terpotong;
- panel detail menggunakan lebar penuh.

==================================================
16. PERBAIKI UKURAN TEKS
==================================================

Gunakan:
- judul halaman: 24–28 px;
- judul kartu: 15–17 px;
- isi tabel: minimal 13 px;
- keterangan: minimal 12 px;
- tombol: minimal 13–14 px;
- area klik tombol ikon: minimal 40 × 40 px.

Kurangi penggunaan teks 10 px pada informasi utama.

Teks 10 px hanya boleh digunakan untuk metadata yang tidak penting.

==================================================
17. EMPTY, LOADING, DAN ERROR STATE
==================================================

Buat state visual untuk:

- belum ada marketing;
- belum ada perjalanan;
- belum ada konsumen;
- belum ada laporan;
- hasil pencarian kosong;
- data sedang dimuat;
- gagal memuat data;
- server tidak merespons;
- sesi login berakhir.

Gunakan skeleton loading dan tombol “Coba Lagi”.

==================================================
18. HASIL AKHIR
==================================================

Hasil akhir harus:

- mempertahankan desain utama saat ini;
- tidak membuat ulang project;
- memiliki halaman login;
- tidak memiliki ikon notifikasi;
- menggunakan data dummy yang konsisten;
- memiliki enam kartu ringkasan;
- memiliki filter monitoring lengkap;
- memiliki form marketing;
- memiliki detail perjalanan;
- memiliki detail konsumen;
- memiliki detail laporan;
- tidak memiliki tombol Tambah Konsumen;
- memiliki seluruh tombol prototype yang dapat digunakan;
- responsive;
- mudah dibaca;
- konsisten dengan aplikasi Android marketing;
- tetap berada dalam scope Basic Version;
- berhasil dijalankan tanpa error build.