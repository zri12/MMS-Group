Lanjutkan revisi project UI web admin “KSP Marketing Monitoring” yang sudah ada.

Project ini hanya berupa front-end dan prototype UI untuk memberikan gambaran tampilan serta alur sistem kepada customer.

Tidak perlu membuat:
- backend nyata;
- database nyata;
- autentikasi nyata;
- tracking GPS nyata;
- Maps API nyata;
- perhitungan keuangan nyata;
- upload file nyata;
- sinkronisasi server nyata.

Gunakan data dummy yang realistis dan konsisten.

PENTING:
Jangan membuat ulang project dari awal.
Jangan menghapus permanen halaman, route, komponen, aset, dan desain lama.
Halaman lama yang sudah tidak digunakan cukup disembunyikan dari navigasi utama agar dapat digunakan kembali apabila diperlukan.

Pertahankan project tetap dapat dijalankan tanpa error build.

==================================================
1. TUJUAN REVISI
==================================================

Ubah web admin menjadi web mobile-first yang:

- terlihat seperti aplikasi saat dibuka melalui HP;
- memiliki bottom navigation di bagian bawah;
- tetap responsive saat dibuka melalui tablet, laptop, PC, dan desktop;
- menggunakan tema black dan gold;
- menggunakan logo resmi KSP Manunggal Makmur Sejahtera;
- menampilkan data operasional marketing dari Senin sampai Sabtu;
- menampilkan total anggota, target, drop, dan storting;
- menampilkan rekap target harian seperti tabel spreadsheet;
- menampilkan status marketing secara horizontal;
- menampilkan jadwal masing-masing marketing;
- memungkinkan admin memilih marketing dan melihat lokasi;
- menampilkan data anggota atau nasabah;
- menampilkan laporan harian sederhana;
- menampilkan foto anggota, foto bukti transfer, dan data lokasi;
- menyederhanakan fitur laporan lama.

Fokus utama sistem:

1. Dashboard operasional.
2. Data per hari Senin sampai Sabtu.
3. Status dan jadwal marketing.
4. Tracking lokasi marketing.
5. Data anggota atau nasabah.
6. Laporan harian.
7. Bukti foto dan lokasi.
8. Pengelolaan akun marketing.

==================================================
2. ATURAN PERUBAHAN PROJECT
==================================================

Jangan menghapus permanen:

- halaman laporan lama;
- detail laporan lama;
- komponen tabel lama;
- komponen peta lama;
- route lama;
- modal lama;
- data dummy lama;
- form lama.

Sembunyikan halaman lama yang tidak digunakan dari:

- sidebar;
- bottom navigation;
- dashboard;
- tombol menu cepat;
- alur prototype customer.

Jangan menampilkan kepada customer:

- tulisan “fitur lama”;
- tulisan “fitur disembunyikan”;
- Basic Version;
- Full Version;
- prototype;
- premium;
- upgrade;
- developer mode;
- feature flag.

==================================================
3. LOGO DAN IDENTITAS
==================================================

Ganti logo lama dengan logo resmi KSP Manunggal Makmur Sejahtera yang saya lampirkan.

Gunakan logo tersebut tanpa mengubah:

- bentuk daun;
- warna hijau;
- warna krem;
- lingkaran;
- proporsi logo.

Jangan mengubah logo menjadi warna emas.

Logo asli tetap menggunakan warna perusahaan.

Tampilkan teks:

KSP MANUNGGAL MAKMUR SEJAHTERA

Gunakan logo pada:

- halaman login;
- header mobile;
- sidebar desktop;
- profil admin;
- splash atau loading state jika tersedia.

Pada background gelap, tempatkan logo dalam area putih atau krem agar tetap jelas.

==================================================
4. DESIGN SYSTEM
==================================================

Gunakan gaya visual premium, rapi, dan profesional.

Palet warna utama:

- Black utama: #07090C
- Black sekunder: #101419
- Black card: #171C22
- Gold utama: #D4AF37
- Gold terang: #E6C45A
- White: #FFFFFF
- Background terang: #F5F6F8
- Border: #E4E7EC
- Teks utama: #101828
- Teks sekunder: #667085
- Hijau aktif: #16A34A
- Background hijau: #DCFCE7
- Merah offline: #DC2626
- Background merah: #FEE2E2
- Oranye peringatan: #F59E0B
- Abu-abu belum mulai: #98A2B3
- Biru informasi: #2563EB

Gunakan font:

Manrope

atau:

Inter

Gunakan satu font secara konsisten.

Ukuran teks:

- judul utama desktop: 26–30 px;
- judul utama mobile: 22–24 px;
- judul kartu: 15–17 px;
- isi utama: 13–15 px;
- isi tabel: minimal 13 px;
- keterangan: minimal 12 px;
- teks tombol: minimal 14 px.

Gunakan:

- radius kartu 14–18 px;
- border tipis;
- shadow lembut;
- ikon outline konsisten;
- spacing 4, 8, 12, 16, 24, dan 32 px.

Hindari:

- glassmorphism;
- neon;
- gradient berlebihan;
- ilustrasi 3D;
- dekorasi futuristik;
- kartu terlalu banyak;
- ikon warna-warni tanpa fungsi;
- layout seperti template AI generik.

==================================================
5. RESPONSIVE DAN MOBILE-FIRST
==================================================

Gunakan mobile sebagai ukuran desain utama:

390 × 844 px

Buat responsive untuk:

- mobile: 390 px;
- tablet: 768 px;
- laptop: 1280 px;
- desktop: 1440 px.

Pada mobile:

- header berada di atas;
- bottom navigation selalu terlihat;
- konten dapat di-scroll secara vertikal;
- bottom navigation tidak menutupi konten;
- gunakan safe area;
- kartu ringkasan dua kolom;
- daftar marketing dapat digeser horizontal;
- tombol hari dapat digeser horizontal jika ruang tidak cukup;
- tabel dapat digeser horizontal;
- kolom penting dibuat sticky;
- detail dapat dibuka sebagai halaman penuh atau bottom sheet;
- tombol minimal 44 × 44 px.

Pada desktop:

- gunakan sidebar kiri;
- bottom navigation disembunyikan;
- kartu ringkasan tampil sejajar;
- tabel menggunakan lebar penuh;
- peta dan detail dapat ditampilkan berdampingan;
- sidebar dapat diperkecil menjadi ikon.

==================================================
6. NAVIGASI UTAMA
==================================================

Bottom navigation mobile:

1. Dashboard
2. Marketing
3. Harian
4. Tracking
5. Profil

Gunakan ikon:

- Dashboard: home atau grid;
- Marketing: users;
- Harian: calendar atau clipboard;
- Tracking: map pin atau route;
- Profil: user.

Menu aktif menggunakan warna gold.

Sidebar desktop:

- Dashboard
- Data Marketing
- Data Harian
- Tracking Lokasi
- Bukti Kunjungan
- Profil Admin

Menu laporan lama tidak ditampilkan di navigasi utama.

==================================================
7. HALAMAN LOGIN ADMIN
==================================================

Pertahankan dan revisi halaman Login Admin.

Tampilkan:

- logo resmi perusahaan;
- nama KSP Manunggal Makmur Sejahtera;
- judul “Dashboard Monitoring Marketing”;
- input username;
- input password;
- ikon tampilkan atau sembunyikan password;
- checkbox “Ingat saya”;
- tombol “Masuk”;
- bantuan jika lupa akun.

Gunakan background black dengan aksen gold.

Jangan tampilkan tombol contoh error.

Alur prototype:

Login Admin
→ Dashboard Utama

==================================================
8. DASHBOARD UTAMA
==================================================

Buat Dashboard Utama sebagai pusat informasi operasional.

Header mobile menampilkan:

- logo perusahaan;
- nama perusahaan;
- sapaan “Selamat pagi”;
- nama admin;
- hari dan tanggal;
- tombol refresh;
- avatar admin.

Kartu ringkasan utama hanya empat:

1. Total Anggota
2. Total Target
3. Total Drop
4. Total Storting

Contoh data dummy:

- Total Anggota: 13
- Total Target: Rp125.000.000
- Total Drop: Rp92.500.000
- Total Storting: Rp38.750.000

Pastikan data pada kartu konsisten dengan tabel target harian.

Setiap kartu dapat diklik.

Alur:

Total Anggota
→ Detail Total Anggota

Total Target
→ Detail Total Target

Total Drop
→ Detail Total Drop

Total Storting
→ Detail Total Storting

Gunakan kartu gelap dengan:

- ikon gold;
- angka besar;
- label jelas;
- keterangan singkat;
- indikator dapat diklik.

Sirkulasi tidak perlu menjadi kartu utama.

Sirkulasi tetap dapat tampil pada tabel rekap harian jika dibutuhkan.

==================================================
9. PILIHAN HARI SENIN SAMPAI SABTU
==================================================

Tambahkan section pada Dashboard:

“Pilih Hari Operasional”

Buat enam tombol:

- Senin
- Selasa
- Rabu
- Kamis
- Jumat
- Sabtu

Hari aktif menggunakan:

- background gold;
- teks black;
- border gold.

Hari tidak aktif menggunakan:

- background black atau putih;
- border abu-abu;
- teks menyesuaikan tema.

Setelah salah satu hari dipilih, tampil tiga tombol utama:

1. Tracking
2. Data Anggota
3. Laporan Harian

Contoh alur:

Dashboard
→ pilih Senin
→ Tracking

Dashboard
→ pilih Selasa
→ Data Anggota

Dashboard
→ pilih Jumat
→ Laporan Harian

Data pada ketiga halaman mengikuti hari yang dipilih.

Tampilkan hari aktif pada header halaman berikutnya.

Contoh:

Data Anggota — Senin

Laporan Harian — Jumat

Tracking Marketing — Sabtu

==================================================
10. STATUS MARKETING HORIZONTAL
==================================================

Tambahkan section:

“Status Marketing Hari Ini”

Kartu marketing dibuat horizontal dan dapat digeser ke kanan.

Gunakan data marketing berikut:

- M01 — Deden
- M02 — Angil
- M03 — Ari
- M04 — Feri
- M05 — Sukma
- M06 — Sandi
- M07 — Vikri
- M08 — Farhad
- M09 — Doni
- M10 — Faiz
- M11 — Agung
- M12 — Faisal
- M13 — Agnes

Setiap kartu menampilkan:

- foto atau avatar;
- kode marketing;
- nama marketing;
- jadwal hari ini;
- status tracking;
- lokasi terakhir;
- waktu terakhir aktif.

Status:

A. Aktif

- titik hijau;
- badge hijau;
- teks “Aktif”;
- tracking berjalan;
- lokasi baru diperbarui.

B. Offline

- titik merah;
- badge merah;
- border merah;
- teks “Offline”;
- tampilkan waktu terakhir aktif.

C. Belum Mulai

- titik abu-abu;
- badge abu-abu;
- teks “Belum Mulai”.

Contoh informasi offline:

“Terakhir aktif pukul 14.20”

Ketika kartu diklik:

→ Detail Marketing

==================================================
11. REKAP TARGET HARIAN SEPERTI SPREADSHEET
==================================================

Tambahkan section pada Dashboard:

“Rekap Target Harian”

Gunakan struktur tabel sesuai gambar rekap manual yang saya lampirkan.

Gunakan grouped table header.

Kelompok kolom:

A. Identitas

- No.
- Marketing
- Kode

B. Anggota

- MG
- L
- M
- K
- S

C. Target

- Lalu
- MSK
- KLR
- S

D. Drop

- Lalu
- Kini
- Total

E. Storting

- Lalu
- Kini
- Total

F. Kolom tambahan

- %
- Sirkulasi Lalu
- Sirkulasi Sekarang
- Dibuat Oleh
- Keterangan

Jangan mengubah arti singkatan berikut:

- MG
- L
- M
- K
- S
- MSK
- KLR

Gunakan singkatan apa adanya karena arti final belum dikonfirmasi.

Pada mobile:

- tabel horizontal scroll;
- kolom Marketing dan Kode dibuat sticky;
- grouped header tetap jelas;
- header tetap terlihat saat scroll;
- tombol “Lihat Tabel Lengkap”;
- filter hari;
- filter tanggal;
- pencarian marketing.

Pada desktop:

- tabel tampil penuh;
- pagination;
- grouped header;
- filter tanggal;
- filter marketing.

Tabel ini hanya prototype visual seperti spreadsheet.

Tidak perlu ekspor Excel nyata.

==================================================
12. DETAIL TOTAL ANGGOTA
==================================================

Buat halaman Detail Total Anggota.

Tampilkan:

- total 13 marketing;
- jumlah anggota per marketing;
- status marketing;
- jadwal;
- lokasi terakhir;
- total target;
- total drop;
- total storting.

Pada mobile gunakan kartu.

Pada desktop gunakan tabel.

Setiap baris atau kartu dapat diklik menuju Detail Marketing.

==================================================
13. DETAIL TOTAL TARGET
==================================================

Buat halaman Detail Total Target.

Tampilkan:

- total target keseluruhan;
- target per marketing;
- target lama;
- target masuk;
- target keluar;
- jumlah target;
- jumlah orang;
- persentase.

Gunakan nilai dummy konsisten.

Jangan membuat perhitungan nyata.

==================================================
14. DETAIL TOTAL DROP
==================================================

Buat halaman Detail Total Drop.

Tampilkan:

- Drop Lalu
- Drop Kini
- Total Drop
- Drop Baru
- Drop Lanjut
- rincian per marketing;
- tanggal;
- marketing;
- status.

==================================================
15. DETAIL TOTAL STORTING
==================================================

Buat halaman Detail Total Storting.

Tampilkan:

- Storting Lalu
- Storting Kini
- Total Storting
- rincian per marketing;
- hari;
- tanggal;
- status.

==================================================
16. DATA MARKETING
==================================================

Buat halaman Data Marketing.

Gunakan data:

M01 Deden
M02 Angil
M03 Ari
M04 Feri
M05 Sukma
M06 Sandi
M07 Vikri
M08 Farhad
M09 Doni
M10 Faiz
M11 Agung
M12 Faisal
M13 Agnes

Pada mobile tampilkan kartu.

Setiap kartu menampilkan:

- foto;
- kode;
- nama;
- jadwal;
- status;
- target hari ini;
- drop hari ini;
- storting hari ini;
- lokasi terakhir;
- tombol Detail.

Pada desktop tampilkan tabel:

- No.
- Marketing
- Kode
- Jadwal
- Status
- Target
- Drop
- Storting
- Lokasi Terakhir
- Aksi

Aksi admin:

- Lihat Detail
- Tambah Marketing
- Edit Marketing
- Reset Password
- Aktifkan Akun
- Nonaktifkan Akun
- Tambah Foto Profil
- Atur Jadwal

Jangan gunakan hapus permanen.

==================================================
17. DETAIL MARKETING
==================================================

Buat halaman Detail Marketing.

Header:

- foto profil;
- kode marketing;
- nama;
- status aktif atau offline;
- jadwal hari ini;
- waktu terakhir aktif.

Kartu ringkasan:

- Jumlah Anggota Hari Ini
- Target Hari Ini
- Drop Hari Ini
- Storting Hari Ini
- Jumlah Kunjungan
- Status Tracking

Section:

- Jadwal Marketing
- Lokasi Terakhir
- Peta Lokasi
- Riwayat Perjalanan
- Data Anggota
- Laporan Harian
- Bukti Kunjungan
- Rincian Target
- Rincian Drop
- Rincian Storting

Tombol:

- Lihat Lokasi
- Lihat Riwayat
- Data Anggota
- Laporan Harian
- Bukti Kunjungan
- Edit Data Marketing

==================================================
18. JADWAL MARKETING
==================================================

Setiap marketing memiliki jadwal masing-masing.

Buat halaman Jadwal Marketing.

Tampilkan:

- marketing;
- kode;
- hari;
- tanggal;
- jam mulai;
- jam selesai;
- area atau tujuan;
- catatan;
- status jadwal.

Status jadwal:

- Belum Mulai
- Sedang Berjalan
- Selesai
- Tidak Aktif

Admin dapat:

- melihat jadwal;
- menambah jadwal;
- mengubah jadwal.

Buat form Tambah atau Edit Jadwal.

Field:

- pilih marketing;
- pilih hari;
- tanggal;
- jam mulai;
- jam selesai;
- area tujuan;
- catatan;
- status.

Tombol:

- Simpan Jadwal
- Batal

Tidak perlu penyimpanan nyata.

==================================================
19. TRACKING BERDASARKAN HARI
==================================================

Saat admin memilih hari lalu menekan Tracking, buka halaman:

“Tracking Marketing — [Nama Hari]”

Tampilkan:

- daftar marketing yang memiliki jadwal pada hari itu;
- kartu marketing horizontal;
- filter status;
- pencarian;
- tombol refresh;
- waktu pembaruan terakhir.

Setiap marketing menampilkan:

- kode;
- nama;
- status;
- jadwal;
- lokasi terakhir;
- waktu terakhir diperbarui.

Ketika marketing dipilih, tampilkan:

- peta;
- status tracking;
- status internet;
- jadwal hari itu;
- lokasi terakhir;
- rute sederhana;
- titik awal;
- titik kunjungan;
- posisi terakhir;
- waktu pembaruan;
- foto kunjungan.

Status marker:

- hijau: aktif;
- merah: offline;
- abu-abu: belum mulai.

Pesan offline:

“Marketing sedang offline. Lokasi terakhir diperbarui pukul 14.20.”

Peta hanya visual dummy.

==================================================
20. RIWAYAT PERJALANAN
==================================================

Riwayat dapat dibuka melalui Tracking atau Detail Marketing.

Tampilkan:

- marketing;
- kode;
- tanggal;
- hari;
- waktu mulai;
- waktu selesai;
- durasi;
- jumlah kunjungan;
- titik awal;
- titik akhir;
- peta rute;
- foto terkait.

Gunakan konsep timeline:

- Titik Awal
- Perjalanan
- Kunjungan Pertama
- Perjalanan
- Kunjungan Berikutnya
- Titik Akhir

Pada mobile:

- peta di atas;
- timeline di bawah.

Pada desktop:

- peta dan timeline berdampingan.

==================================================
21. DATA ANGGOTA BERDASARKAN HARI
==================================================

Saat admin memilih hari lalu menekan Data Anggota, tampilkan:

“Data Anggota — [Nama Hari]”

Tampilkan:

- jumlah anggota pada hari tersebut;
- filter marketing;
- filter tanggal;
- pencarian nama;
- daftar anggota;
- waktu data dibuat;
- marketing pembuat;
- tombol Detail.

Gunakan field data anggota sesuai form referensi:

- Resort
- Tanggal
- Nama
- No. Anggota
- No. Pinjaman
- Alamat
- No. HP
- Usaha
- Pinjaman
- Angsuran
- Asuransi
- Jaminan
- ACC
- Foto Anggota/Nasabah
- Lokasi
- Tanggal dan waktu input
- Marketing pembuat

Pada mobile tampilkan dalam kartu.

Pada desktop tampilkan tabel.

Kolom utama tabel:

- Nama
- No. Anggota
- No. Pinjaman
- No. HP
- Resort
- Marketing
- Tanggal
- Status ACC
- Lokasi
- Aksi

==================================================
22. DETAIL ANGGOTA
==================================================

Buat halaman Detail Anggota.

Tampilkan:

- foto anggota/nasabah;
- nama;
- nomor anggota;
- nomor pinjaman;
- resort;
- alamat;
- nomor HP;
- usaha;
- pinjaman;
- angsuran;
- asuransi;
- jaminan;
- status ACC;
- marketing pembuat;
- tanggal;
- lokasi;
- peta lokasi;
- foto bukti transfer jika tersedia;
- laporan harian terkait.

Status ACC hanya ditampilkan sebagai informasi.

Jangan membuat alur approval kompleks.

==================================================
23. LAPORAN HARIAN BERDASARKAN HARI
==================================================

Saat admin memilih hari lalu menekan Laporan Harian, tampilkan:

“Laporan Harian — [Nama Hari]”

Laporan harian berisi data yang dikirim marketing.

Gunakan field yang tidak dicoret hijau pada gambar referensi.

Untuk prototype, tampilkan field berikut:

- Storting
- Drop
- Tabungan Keluar
- Lain-lain
- Target Lama
- Target Masuk
- Target Keluar
- Jumlah Target
- Drop Baru
- Drop Lanjut

Target menggunakan dua nilai:

- nominal Rupiah;
- jumlah Orang.

Contoh:

Target Lama:
Rp10.000.000 / 15 Orang

Target Masuk:
Rp2.000.000 / 3 Orang

Target Keluar:
Rp1.500.000 / 2 Orang

Jumlah:
Rp10.500.000 / 16 Orang

Jangan tampilkan field yang sudah dicoret hijau:

- Kas Bon
- Potongan Admin
- Potongan Tabungan
- Asuransi
- Jumlah Uang Tunai
- Plus
- Min
- Sirkulasi

Gunakan field Lain-lain hanya jika sesuai referensi yang tidak dicoret.

Tampilkan:

- marketing;
- kode;
- hari;
- tanggal;
- Storting;
- Drop;
- Tabungan Keluar;
- Lain-lain;
- Target;
- Drop Baru;
- Drop Lanjut;
- foto anggota;
- foto bukti transfer;
- lokasi;
- waktu input.

==================================================
24. DETAIL LAPORAN HARIAN
==================================================

Buat halaman Detail Laporan Harian.

Header:

- nama marketing;
- kode marketing;
- hari dan tanggal;
- status tracking;
- lokasi.

Bagian data:

- Storting
- Drop
- Tabungan Keluar
- Lain-lain
- Target Lama
- Target Masuk
- Target Keluar
- Jumlah Target
- Drop Baru
- Drop Lanjut

Bagian bukti:

1. Foto Anggota/Nasabah
2. Foto Bukti Transfer
3. Lokasi
4. Alamat
5. Koordinat dummy
6. Tanggal dan waktu

Gunakan dua foto dummy lokal.

Jangan memakai URL gambar eksternal.

==================================================
25. BUKTI KUNJUNGAN
==================================================

Hapus konsep laporan kunjungan lama dari navigasi.

Ganti menjadi:

“Bukti Kunjungan”

Data yang ditampilkan:

- foto anggota/nasabah;
- foto bukti transfer;
- lokasi;
- tanggal;
- waktu;
- kode marketing;
- nama marketing;
- nama anggota;
- catatan singkat jika ada.

Tampilan kartu:

- thumbnail foto;
- marketing;
- anggota;
- lokasi;
- tanggal;
- tombol Detail.

Detail Bukti Kunjungan:

- dua foto ukuran besar;
- lokasi pada peta;
- alamat;
- koordinat dummy;
- marketing;
- anggota;
- tanggal dan waktu.

Jangan tampilkan:

- approval;
- tolak;
- minta revisi;
- ekspor;
- cetak;
- status laporan rumit;
- form laporan panjang.

==================================================
26. PROFIL ADMIN
==================================================

Buat Profil Admin sederhana.

Tampilkan:

- logo perusahaan;
- nama admin;
- username;
- ubah password;
- informasi sistem;
- logout.

Logout menggunakan modal konfirmasi.

==================================================
27. DATA DUMMY MARKETING
==================================================

Gunakan seluruh nama berikut secara konsisten:

M01 — Deden
M02 — Angil
M03 — Ari
M04 — Feri
M05 — Sukma
M06 — Sandi
M07 — Vikri
M08 — Farhad
M09 — Doni
M10 — Faiz
M11 — Agung
M12 — Faisal
M13 — Agnes

Berikan jadwal berbeda untuk setiap marketing.

Contoh:

M01 Deden:
Senin, Rabu, Jumat
08.00–16.00
Area Gedebage

M02 Angil:
Selasa, Kamis, Sabtu
08.00–16.00
Area Buahbatu

Lanjutkan jadwal dummy untuk M03 sampai M13.

Pastikan jadwal pada:

- Dashboard;
- Data Marketing;
- Detail Marketing;
- Tracking;
- Laporan Harian

selalu konsisten.

==================================================
28. DATA DUMMY KEUANGAN
==================================================

Gunakan nilai contoh yang realistis.

Pastikan total kartu sama dengan jumlah tabel.

Total:

- Total Anggota: 13
- Total Target: Rp125.000.000
- Total Drop: Rp92.500.000
- Total Storting: Rp38.750.000

Contoh M01 Deden:

- Target: Rp10.000.000
- Drop: Rp7.500.000
- Storting: Rp3.000.000

Contoh M02 Angil:

- Target: Rp9.500.000
- Drop: Rp7.000.000
- Storting: Rp2.800.000

Lanjutkan data M03 sampai M13 dan pastikan totalnya konsisten dengan kartu dashboard.

==================================================
29. INTERAKSI PROTOTYPE
==================================================

Buat prototype dapat diklik.

Login
→ Dashboard

Dashboard
→ Total Anggota
→ Detail Total Anggota
→ Detail Marketing

Dashboard
→ Total Target
→ Detail Total Target

Dashboard
→ Total Drop
→ Detail Total Drop

Dashboard
→ Total Storting
→ Detail Total Storting

Dashboard
→ pilih hari Senin–Sabtu
→ Tracking
→ pilih marketing
→ Detail Tracking
→ Riwayat Perjalanan
→ Bukti Kunjungan

Dashboard
→ pilih hari
→ Data Anggota
→ Detail Anggota

Dashboard
→ pilih hari
→ Laporan Harian
→ Detail Laporan Harian

Marketing
→ Data Marketing
→ Detail Marketing
→ Jadwal
→ Tracking
→ Data Anggota
→ Bukti Kunjungan

Profil
→ Ubah Password
→ Logout
→ Login

Tombol hanya perlu:

- perpindahan halaman;
- modal;
- bottom sheet;
- toast;
- loading singkat;
- feedback visual.

Tidak perlu fungsi nyata.

==================================================
30. EMPTY, LOADING, ERROR, DAN STATUS STATE
==================================================

Buat state visual:

- belum ada jadwal;
- belum ada anggota;
- belum ada laporan harian;
- belum ada bukti kunjungan;
- marketing belum mulai tracking;
- marketing offline;
- GPS tidak aktif;
- hasil pencarian kosong;
- data sedang dimuat;
- gagal memuat data;
- server tidak merespons;
- sesi login berakhir.

Gunakan skeleton loading.

==================================================
31. KOMPONEN REUSABLE
==================================================

Buat komponen reusable:

- Header mobile
- Sidebar desktop
- Bottom navigation
- Summary card
- Day selector
- Daily action button
- Marketing horizontal card
- Marketing status badge
- Grouped spreadsheet table
- Data anggota card
- Laporan harian card
- Schedule card
- Tracking map
- Map marker
- Route timeline
- Two-photo uploader display
- Visit evidence card
- Detail modal
- Bottom sheet
- Confirmation modal
- Search field
- Filter chip
- Date picker
- Select
- Button
- Empty state
- Loading skeleton
- Error state

Gunakan Auto Layout.

==================================================
32. FITUR YANG TIDAK BOLEH DITAMBAHKAN
==================================================

Jangan menambahkan:

- backend;
- database nyata;
- API nyata;
- Maps API nyata;
- perhitungan otomatis nyata;
- ekspor Excel nyata;
- ekspor PDF;
- cetak;
- approval laporan;
- audit log;
- banyak role admin;
- permission kompleks;
- WhatsApp otomatis;
- email otomatis;
- chat;
- payroll;
- absensi wajah;
- pinjaman dan simpanan sebagai modul terpisah;
- transaksi pembayaran nyata;
- AI;
- ranking otomatis;
- grafik dekoratif berlebihan.

==================================================
33. HASIL AKHIR
==================================================

Hasil akhir harus:

- merevisi project yang sudah ada;
- tidak membuat ulang seluruh project;
- menggunakan logo resmi perusahaan;
- menggunakan tema black dan gold;
- mobile-first;
- memiliki bottom navigation pada HP;
- responsive pada laptop dan desktop;
- menampilkan empat kartu utama;
- memiliki tombol Senin sampai Sabtu;
- setiap hari memiliki Tracking, Data Anggota, dan Laporan Harian;
- menampilkan status marketing horizontal;
- menggunakan 13 marketing sesuai daftar;
- memiliki jadwal masing-masing marketing;
- admin dapat memilih marketing dan melihat lokasi;
- memiliki rekap target harian seperti spreadsheet;
- memiliki data anggota sesuai form referensi;
- memiliki laporan harian sesuai field yang tidak dicoret hijau;
- menampilkan dua foto;
- menampilkan data lokasi;
- menyederhanakan laporan menjadi bukti foto dan lokasi;
- mempertahankan halaman lama tanpa menampilkannya di navigasi;
- mudah dipahami pengguna non-IT;
- tidak terlihat seperti template AI generik;
- tidak memiliki tombol kosong;
- seluruh alur prototype utama dapat diklik;
- project berhasil dijalankan tanpa error build.