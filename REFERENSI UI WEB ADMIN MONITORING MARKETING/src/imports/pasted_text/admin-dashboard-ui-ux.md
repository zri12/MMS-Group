Buat rancangan UI/UX web dashboard admin untuk sistem:

“MMS Marketing Monitoring”
MMS Group
KSP Manunggal Makmur Sejahtera

Dashboard ini digunakan oleh satu admin utama untuk memantau aktivitas marketing, melihat posisi terakhir marketing, melihat riwayat perjalanan, mengelola akun marketing, melihat data konsumen, dan melihat laporan kunjungan.

Project ini hanya berupa front-end dan prototype UI untuk memberikan gambaran kepada customer. Tidak perlu membuat backend, database, API, tracking GPS nyata, autentikasi nyata, upload file nyata, atau integrasi peta nyata.

Gunakan data dummy yang realistis dan buat seluruh tombol utama dapat diklik sebagai prototype.

Gunakan gambar referensi customer yang saya lampirkan hanya sebagai acuan gaya visual, warna, bentuk kartu, header, garis dekoratif, dan kesan premium.

Jangan menyalin fitur keuangan dari gambar referensi.

Jangan memasukkan:
- target;
- drop;
- storting;
- sirkulasi;
- simpanan;
- pinjaman;
- transaksi;
- grafik keuangan;
- data anggota koperasi;
- payroll;
- absensi wajah;
- WhatsApp blast.

==================================================
1. TUJUAN DESAIN
==================================================

Buat dashboard yang:

- profesional;
- modern;
- premium tetapi tetap sederhana;
- mudah dipahami oleh pengguna non-IT;
- tidak terlihat seperti template dashboard AI;
- memiliki hierarki informasi yang jelas;
- tidak terlalu ramai;
- memiliki tabel yang nyaman dibaca;
- menggunakan peta sebagai bagian penting;
- responsif untuk laptop, tablet, dan HP;
- realistis untuk dikembangkan menggunakan Laravel Blade atau React;
- menggunakan bahasa Indonesia.

Fokus utama dashboard:

1. mengetahui marketing yang aktif dan offline;
2. melihat posisi terakhir marketing;
3. melihat riwayat perjalanan;
4. melihat data konsumen;
5. melihat laporan kunjungan;
6. mengelola akun marketing.

==================================================
2. UKURAN DAN RESPONSIVE
==================================================

Gunakan frame desktop utama sekitar:

1440 × 1024 px

Buat juga contoh responsive:

- desktop 1440 px;
- laptop 1280 px;
- tablet sekitar 768 px;
- mobile sekitar 390 px.

Pada desktop:
- gunakan sidebar kiri;
- header bagian atas;
- area konten utama luas;
- tabel dapat menggunakan seluruh lebar konten.

Pada tablet:
- sidebar dapat diperkecil menjadi ikon;
- filter tetap mudah digunakan;
- tabel dapat menggunakan horizontal scroll.

Pada mobile:
- sidebar berubah menjadi drawer;
- kartu ringkasan menjadi dua kolom atau satu kolom;
- tabel dapat berubah menjadi kartu;
- peta tetap dapat dilihat;
- jangan membuat aplikasi admin mobile khusus.

==================================================
3. ARAH VISUAL
==================================================

Gunakan palet warna:

- hitam utama: #080A0D;
- hitam sekunder: #11151A;
- emas utama: #D4AF37;
- emas terang: #E5C45C;
- putih: #FFFFFF;
- abu-abu latar: #F5F6F8;
- abu-abu border: #E5E7EB;
- abu-abu teks: #667085;
- hijau aktif: #16A34A;
- oranye offline: #F59E0B;
- merah masalah: #DC2626;
- biru informasi: #2563EB.

Gaya visual:

- sidebar hitam;
- menu aktif menggunakan aksen emas;
- konten utama menggunakan latar abu-abu sangat muda;
- kartu putih dengan border tipis;
- bayangan sangat lembut;
- radius kartu sekitar 12–16 px;
- tabel putih dan rapi;
- header tabel menggunakan abu-abu muda;
- gunakan garis dekoratif emas secara tipis;
- hindari gradient berlebihan;
- hindari glassmorphism;
- hindari warna neon;
- hindari ilustrasi 3D;
- hindari terlalu banyak kartu kecil;
- hindari grafik dekoratif tanpa fungsi.

Gunakan font:

- Manrope;
- Inter;
- atau Poppins.

Gunakan satu font secara konsisten.

Ukuran teks:

- judul halaman: 24–28 px;
- judul kartu: 15–17 px;
- isi tabel: 13–14 px;
- label dan keterangan: minimal 12 px;
- tombol: minimal 14 px.

==================================================
4. IDENTITAS APLIKASI
==================================================

Gunakan placeholder logo MMS Group karena logo resmi belum tersedia.

Jangan membuat logo baru yang terlihat resmi.

Gunakan placeholder sederhana:
- kotak atau lingkaran;
- teks “MMS”;
- label kecil “Logo MMS Group”.

Pada sidebar tampilkan:

MMS GROUP
Marketing Monitoring

Pada halaman login tampilkan:

Dashboard Monitoring Marketing
KSP Manunggal Makmur Sejahtera

==================================================
5. STRUKTUR SIDEBAR BASIC VERSION
==================================================

Gunakan menu sidebar:

1. Dashboard
2. Monitoring Lokasi
3. Data Marketing
4. Riwayat Perjalanan
5. Data Konsumen
6. Laporan Kunjungan

Pada bagian bawah sidebar tampilkan:

- Profil Admin;
- Keluar.

Gunakan ikon outline yang konsisten.

Jangan membuat menu khusus:

- Data Resort;
- Detail Resort;
- Ranking;
- Statistik;
- Ekspor;
- Approval;
- Audit Log;
- Pengaturan Sistem;
- Kelola Role;
- Notifikasi;
- Data Keuangan.

Resort cukup menjadi filter dan informasi pada akun marketing.

==================================================
6. HALAMAN LOGIN ADMIN
==================================================

Buat halaman:

01 Login Admin

Layout:

- sisi kiri atau bagian atas menggunakan area hitam;
- aksen garis emas;
- placeholder logo MMS Group;
- judul “Dashboard Monitoring Marketing”;
- subjudul “KSP Manunggal Makmur Sejahtera”;
- form login pada kartu putih.

Field:

- Username;
- Password;
- ikon tampilkan atau sembunyikan password;
- checkbox “Ingat saya”;
- tombol “Masuk”.

Tambahkan teks:

“Hubungi pengelola sistem apabila lupa akun atau password.”

Buat state visual:

- normal;
- field kosong;
- password salah;
- akun tidak aktif;
- loading login;
- server tidak merespons.

Setelah login, arahkan ke Dashboard.

==================================================
7. STRUKTUR HEADER
==================================================

Pada halaman setelah login, buat header yang menampilkan:

- tombol buka atau tutup sidebar;
- breadcrumb;
- judul halaman;
- tanggal hari ini;
- tombol Refresh;
- avatar admin;
- nama admin;
- dropdown profil dan logout.

Jangan gunakan ikon notifikasi karena notifikasi internal tidak termasuk versi basic.

Tampilkan teks kecil:

“Terakhir diperbarui: 21 Juli 2026, 14.35”

Tombol Refresh hanya berupa prototype visual.

==================================================
8. DASHBOARD UTAMA
==================================================

Buat halaman:

02 Dashboard Utama

Tampilkan enam kartu ringkasan:

1. Total Marketing
2. Marketing Aktif
3. Marketing Offline
4. Perjalanan Hari Ini
5. Total Konsumen
6. Laporan Hari Ini

Gunakan data dummy:

- Total Marketing: 13
- Marketing Aktif: 8
- Marketing Offline: 3
- Belum Tracking: 2
- Perjalanan Hari Ini: 10
- Total Konsumen: 126
- Laporan Hari Ini: 8

Setiap kartu memiliki:
- ikon;
- angka utama;
- label;
- keterangan singkat;
- aksen warna status.

Jangan menampilkan grafik besar yang kompleks.

Tambahkan satu area utama berupa peta monitoring ringkas.

Peta menampilkan beberapa marker marketing dengan warna:

- hijau: aktif dan online;
- oranye: offline;
- merah: GPS tidak aktif;
- abu-abu: belum tracking.

Gunakan mock map yang realistis:
- bentuk jalan;
- nama wilayah;
- marker;
- legenda;
- tombol zoom dummy;
- tombol “Lihat Monitoring Lengkap”.

Pada sisi peta atau di bawahnya tampilkan daftar marketing aktif:

- nama;
- resort;
- status;
- lokasi terakhir;
- waktu pembaruan.

Tambahkan bagian aktivitas terbaru maksimal lima data:

Contoh:
- Budi Santoso mengirim laporan kunjungan.
- Dedi Kurniawan memulai tracking.
- Siti Rahma menambahkan konsumen baru.
- Ahmad sedang offline sejak pukul 13.42.
- Laporan Resort 3 berhasil diterima.

Jangan membuat halaman log aktivitas khusus.

==================================================
9. MONITORING LOKASI
==================================================

Buat halaman:

03 Monitoring Lokasi

Layout desktop:

- peta besar di sisi kanan atau tengah;
- daftar marketing di sisi kiri;
- filter di bagian atas.

Filter:

- Semua Resort;
- Resort 1 sampai Resort 10;
- Semua Marketing;
- Semua Status;
- kolom pencarian nama;
- tombol Refresh.

Status:

- Tracking Aktif;
- Offline;
- GPS Tidak Aktif;
- Belum Tracking.

Setiap kartu atau baris marketing menampilkan:

- avatar;
- nama;
- resort;
- status tracking;
- status internet;
- lokasi terakhir;
- waktu terakhir diperbarui;
- tombol “Lihat Detail”.

Saat marker atau marketing dipilih, tampilkan panel detail:

- nama marketing;
- ID marketing;
- resort;
- status akun;
- status tracking;
- status internet;
- lokasi terakhir;
- waktu terakhir aktif;
- waktu tracking dimulai;
- jumlah kunjungan hari ini;
- jumlah laporan hari ini;
- tombol “Lihat Perjalanan”;
- tombol “Lihat Laporan”.

Gunakan informasi:

“Terakhir diperbarui pukul 13.42”

untuk marketing offline.

Jangan membuat seolah posisi offline masih diperbarui secara real-time.

Tidak perlu update marker setiap beberapa detik. Gunakan tombol Refresh sebagai prototype.

==================================================
10. DATA MARKETING
==================================================

Buat halaman:

04 Data Marketing

Tampilkan header:

- judul “Data Marketing”;
- jumlah akun;
- tombol “Tambah Marketing”;
- kolom pencarian;
- filter resort;
- filter status akun;
- filter status tracking.

Gunakan tabel dengan kolom:

- No.
- Marketing
- Username atau ID
- Resort
- Nomor Telepon
- Status Akun
- Status Tracking
- Terakhir Aktif
- Aksi

Gunakan badge:

- Aktif;
- Nonaktif;
- Tracking Aktif;
- Offline;
- GPS Mati;
- Belum Tracking.

Aksi:

- Lihat Detail;
- Edit Data;
- Reset Password;
- Aktifkan atau Nonaktifkan.

Jangan gunakan tombol hapus permanen.

Buat halaman atau modal:

05 Tambah Marketing
06 Detail Marketing
07 Edit Marketing
08 Reset Password

==================================================
11. FORM TAMBAH DAN EDIT MARKETING
==================================================

Field:

- nama lengkap;
- username;
- ID marketing;
- nomor telepon;
- resort;
- password awal;
- konfirmasi password;
- status akun.

Pilihan resort:

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

Tombol:

- Simpan;
- Batal.

Tambahkan validasi visual:
- nama wajib diisi;
- username wajib diisi;
- resort wajib dipilih;
- password minimal delapan karakter.

Detail marketing menampilkan:

- identitas marketing;
- resort;
- status akun;
- status tracking;
- lokasi terakhir;
- terakhir aktif;
- perjalanan hari ini;
- laporan hari ini;
- konsumen terakhir.

Gunakan tab sederhana:

- Ringkasan;
- Perjalanan;
- Konsumen;
- Laporan.

==================================================
12. RIWAYAT PERJALANAN
==================================================

Buat halaman:

09 Riwayat Perjalanan

Filter:

- resort;
- marketing;
- tanggal mulai;
- tanggal selesai;
- status sinkronisasi;
- tombol Terapkan Filter;
- tombol Reset.

Gunakan tabel:

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

- Tersinkron;
- Menunggu Sinkronisasi;
- Sedang Berjalan.

Buat halaman:

10 Detail Perjalanan

Tampilkan:

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
- daftar konsumen yang dikunjungi;
- laporan terkait.

Peta hanya visual prototype.

Jangan menampilkan:
- analisis kecepatan;
- waktu berhenti;
- produktivitas;
- performa;
- ranking;
- analisis rute lanjutan.

==================================================
13. DATA KONSUMEN
==================================================

Buat halaman:

11 Data Konsumen

Tampilkan:

- kolom pencarian;
- filter resort;
- filter marketing;
- filter status;
- filter tanggal kunjungan.

Gunakan status:

- Baru;
- Tertarik;
- Follow Up;
- Selesai.

Tabel:

- Nama Konsumen
- Nomor Kontak
- Alamat
- Status
- Marketing
- Resort
- Kunjungan Terakhir
- Aksi

Aksi hanya:

- Lihat Detail.

Buat halaman:

12 Detail Konsumen

Tampilkan:

- nama konsumen;
- nomor kontak;
- alamat;
- lokasi pada peta dummy;
- status;
- marketing yang menangani;
- resort;
- tanggal kunjungan terakhir;
- catatan;
- riwayat kunjungan;
- laporan terakhir;
- satu foto laporan.

Admin basic hanya melihat data konsumen.

Jangan tampilkan tombol:
- Edit Konsumen;
- Hapus Konsumen;
- Hubungi melalui WhatsApp;
- Setujui Konsumen.

==================================================
14. LAPORAN KUNJUNGAN
==================================================

Buat halaman:

13 Laporan Kunjungan

Bagian ringkasan:

- Total Hari Ini;
- Terkirim;
- Menunggu Sinkronisasi;
- Gagal.

Jangan tampilkan status Draft pada dashboard admin basic.

Filter:

- resort;
- marketing;
- tanggal;
- hasil kunjungan;
- status konsumen;
- status pengiriman.

Gunakan tabel:

- Foto
- Konsumen
- Marketing
- Resort
- Hasil Kunjungan
- Tanggal dan Waktu
- Lokasi
- Status
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

Buat halaman:

14 Detail Laporan

Tampilkan:

- foto laporan;
- nama konsumen;
- marketing;
- resort;
- tujuan kunjungan;
- hasil kunjungan;
- status konsumen;
- catatan;
- tanggal dan waktu;
- lokasi;
- peta dummy;
- status pengiriman.

Admin basic hanya melihat laporan.

Jangan tampilkan:
- Setujui;
- Tolak;
- Minta Revisi;
- Ekspor PDF;
- Ekspor Excel;
- Cetak;
- Kirim WhatsApp.

==================================================
15. PROFIL ADMIN
==================================================

Buat modal atau halaman sederhana:

15 Profil Admin

Isi:

- nama admin;
- username;
- status akun;
- ubah password;
- logout.

Buat halaman:

16 Ubah Password Admin

Field:
- password lama;
- password baru;
- konfirmasi password;
- tombol Simpan.

Jangan membuat profil admin kompleks.

==================================================
16. EMPTY, LOADING, DAN ERROR STATE
==================================================

Buat state sederhana untuk:

- belum ada marketing;
- belum ada perjalanan;
- belum ada konsumen;
- belum ada laporan;
- hasil pencarian kosong;
- data sedang dimuat;
- gagal memuat data;
- server tidak merespons;
- sesi login berakhir.

Gunakan:
- skeleton loading;
- ikon sederhana;
- teks natural;
- tombol “Coba Lagi”.

==================================================
17. MODAL KONFIRMASI
==================================================

Buat modal untuk:

- menonaktifkan marketing;
- mengaktifkan marketing;
- reset password;
- logout admin.

Contoh logout:

Judul:
“Keluar dari dashboard?”

Keterangan:
“Pastikan semua aktivitas yang diperlukan telah selesai.”

Tombol:
- Batal
- Ya, Keluar

==================================================
18. KOMPONEN REUSABLE
==================================================

Buat komponen yang konsisten:

- sidebar;
- header;
- breadcrumb;
- summary card;
- status badge;
- button;
- input;
- select;
- date picker;
- search field;
- filter bar;
- data table;
- pagination;
- marketing card;
- map marker;
- detail side panel;
- confirmation modal;
- empty state;
- loading skeleton;
- error state.

Gunakan spacing:

- 4 px;
- 8 px;
- 12 px;
- 16 px;
- 24 px;
- 32 px.

Pastikan tabel memiliki padding cukup dan teks tidak terlalu dekat dengan garis.

==================================================
19. DATA DUMMY
==================================================

Gunakan data realistis:

Admin:
- Admin Utama

Marketing:
- Budi Santoso — Resort 3 — Aktif
- Dedi Kurniawan — Resort 2 — Offline
- Siti Rahma — Resort 5 — Aktif
- Ahmad Hidayat — Resort 4 — GPS Tidak Aktif
- Rina Marlina — Resort 1 — Belum Tracking

Konsumen:
- Toko Berkah Jaya
- Ahmad Hidayat
- Siti Nurjanah
- Dedi Saputra
- Warung Sumber Rezeki

Lokasi:
- Gedebage, Kota Bandung
- Rancasari, Kota Bandung
- Buahbatu, Kota Bandung
- Ujungberung, Kota Bandung
- Cibiru, Kota Bandung

Gunakan tanggal dan jam yang konsisten di seluruh halaman.

==================================================
20. INTERAKSI PROTOTYPE
==================================================

Buat prototype dapat diklik:

Login
→ Dashboard

Dashboard
→ Monitoring Lokasi
→ Pilih Marketing
→ Detail Marketing
→ Riwayat Perjalanan
→ Detail Perjalanan

Dashboard
→ Data Marketing
→ Tambah Marketing
→ Simpan
→ Detail Marketing
→ Edit Marketing
→ Reset Password

Dashboard
→ Data Konsumen
→ Detail Konsumen

Dashboard
→ Laporan Kunjungan
→ Detail Laporan

Header Admin
→ Profil
→ Ubah Password
→ Logout
→ Login

Tombol Refresh cukup menampilkan loading singkat dan teks:
“Data berhasil diperbarui.”

Tidak perlu membuat fungsi nyata.

==================================================
21. FITUR YANG TIDAK BOLEH DITAMBAHKAN
==================================================

Jangan menambahkan:

- halaman Data Resort;
- halaman Detail Resort;
- CRUD Resort;
- admin per resort;
- banyak role;
- kelola permission;
- grafik kompleks;
- ranking;
- ekspor PDF;
- ekspor Excel;
- cetak laporan;
- approval laporan;
- audit log;
- pengaturan sistem;
- notifikasi WhatsApp;
- notifikasi email;
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
- chat;
- aplikasi Android admin.

==================================================
22. HASIL AKHIR
==================================================

Hasil akhir harus berupa web dashboard admin Basic Version yang:

- terlihat profesional;
- mudah dipahami customer non-IT;
- menggunakan tema hitam, putih, dan emas;
- konsisten dengan UI aplikasi marketing;
- memiliki sidebar sederhana;
- memiliki dashboard ringkas;
- memiliki monitoring lokasi;
- memiliki pengelolaan akun marketing;
- memiliki riwayat perjalanan;
- memiliki data konsumen;
- memiliki laporan kunjungan;
- memiliki prototype yang dapat diklik;
- responsive;
- tidak terlalu ramai;
- tidak memiliki fitur di luar budget;
- tidak terlihat seperti template AI generik;
- tetap realistis untuk dikembangkan menjadi Laravel atau React;
- berhasil dijalankan tanpa error build.