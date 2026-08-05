Buat rancangan UI/UX aplikasi Android bernama:

“MMS Marketing”
KSP Manunggal Makmur Sejahtera

Aplikasi ini digunakan oleh staf marketing MMS Group untuk menjalankan tracking perjalanan, mencatat data konsumen, membuat laporan kunjungan, mengunggah foto, dan melihat riwayat aktivitas sendiri.

Gunakan gambar referensi yang saya lampirkan sebagai inspirasi utama untuk arah visual. Ambil nuansa profesional, premium, formal, bersih, dan modern dari referensi tersebut, terutama penggunaan warna hitam, putih, dan emas.

Jangan menyalin isi data, susunan dashboard admin, menu resort, grafik keuangan, anggota, target, drop, storting, sirkulasi, atau informasi keuangan dari gambar referensi. Referensi hanya digunakan untuk menentukan gaya visual, warna, bentuk kartu, garis dekoratif, header, ikon, dan kesan premium.

Fokus hanya membuat aplikasi Android untuk pengguna marketing. Jangan membuat dashboard admin dalam project ini.

==================================================
1. TUJUAN DESAIN
==================================================

Buat desain aplikasi yang:

- terlihat profesional dan dapat digunakan oleh perusahaan;
- mudah dipahami oleh pengguna nonteknis;
- memiliki tampilan premium tetapi tidak berlebihan;
- nyaman digunakan oleh marketing saat berada di lapangan;
- memiliki tombol dan teks yang jelas;
- tetap mudah digunakan dalam kondisi cahaya luar ruangan;
- menampilkan status tracking dengan sangat jelas;
- menampilkan status internet dan sinkronisasi data;
- mendukung kondisi online dan offline;
- menggunakan bahasa Indonesia;
- tidak menggunakan istilah teknis yang sulit dipahami pengguna.

Gunakan pendekatan mobile-first dengan ukuran frame Android sekitar 390 × 844 px.

Gunakan layout yang rapi, jarak antarelemen konsisten, teks tidak terlalu kecil, serta area tombol cukup besar untuk ditekan menggunakan satu tangan.

==================================================
2. ARAH VISUAL DAN DESIGN SYSTEM
==================================================

Gunakan identitas visual berikut:

Warna utama:
- hitam pekat: #080A0D;
- hitam sekunder: #11151A;
- emas utama: #D4AF37;
- emas terang: #E7C765;
- putih: #FFFFFF;
- abu-abu latar: #F5F5F5;
- abu-abu teks sekunder: #6B7280;
- abu-abu garis: #E5E7EB;
- hijau status aktif: #22A65A;
- merah status gagal: #D64545;
- oranye status menunggu: #F59E0B;
- biru informasi: #2563EB.

Gaya desain:
- header dominan hitam dengan aksen emas;
- halaman konten dominan putih atau abu-abu sangat muda;
- kartu menggunakan sudut membulat sekitar 14–18 px;
- gunakan border tipis dan bayangan lembut;
- gunakan garis dekoratif emas yang halus seperti referensi;
- hindari penggunaan terlalu banyak warna cerah;
- gunakan warna status hanya untuk informasi penting;
- gunakan ikon outline yang sederhana dan konsisten;
- hindari efek 3D berlebihan;
- hindari glassmorphism yang terlalu transparan;
- tampilan harus realistis dan siap dikembangkan menjadi aplikasi Flutter.

Tipografi:
- gunakan font modern dan mudah dibaca seperti Inter, Poppins, atau Manrope;
- judul utama menggunakan bobot semibold atau bold;
- isi teks menggunakan regular;
- ukuran judul halaman sekitar 22–26 px;
- ukuran judul kartu sekitar 15–17 px;
- ukuran isi sekitar 13–15 px;
- ukuran keterangan minimal 12 px;
- jangan menggunakan teks terlalu rapat.

Logo:
- jangan membuat atau mengarang logo baru;
- sediakan area placeholder logo MMS Group;
- tampilkan teks “MMS GROUP”;
- tampilkan subjudul “KSP Manunggal Makmur Sejahtera”;
- jika file logo resmi diberikan nanti, area tersebut akan diganti dengan logo asli.

==================================================
3. STRUKTUR NAVIGASI
==================================================

Gunakan bottom navigation bar dengan lima menu:

1. Beranda
2. Konsumen
3. Laporan
4. Riwayat
5. Profil

Gunakan ikon yang mudah dipahami:
- Beranda: home;
- Konsumen: users atau contact;
- Laporan: clipboard atau document;
- Riwayat: clock atau route;
- Profil: user.

Menu aktif menggunakan warna emas dan latar gelap atau highlight emas lembut.

Bottom navigation tetap terlihat pada halaman utama, konsumen, laporan, riwayat, dan profil.

Pada halaman form detail, gunakan header dengan tombol kembali.

==================================================
4. ALUR UTAMA APLIKASI
==================================================

Alur pengguna:

Splash Screen
→ Login
→ Permintaan Izin Lokasi
→ Permintaan Izin Notifikasi
→ Permintaan Izin Kamera
→ Dashboard Marketing
→ Tracking otomatis aktif
→ Marketing menambahkan data konsumen
→ Marketing membuat laporan kunjungan
→ Marketing mengambil atau memilih foto
→ Marketing mengirim laporan
→ Data tersimpan atau masuk antrean jika offline
→ Marketing melihat riwayat
→ Marketing logout dari halaman profil

Tracking dimulai otomatis setelah pengguna berhasil login dan memberikan izin lokasi.

Tracking tetap berjalan saat pengguna berpindah halaman atau aplikasi berada di background.

Ketika internet tidak tersedia:
- koordinat tetap disimpan di perangkat;
- laporan dan foto masuk ke antrean sinkronisasi;
- tampilkan status bahwa data belum terkirim;
- data dikirim otomatis saat internet kembali aktif.

Jangan membuat tombol “Mulai Tracking” sebagai tombol utama karena tracking direncanakan langsung aktif setelah login.

Tetap sediakan indikator status tracking yang sangat jelas.

==================================================
5. HALAMAN YANG HARUS DIBUAT
==================================================

Buat seluruh halaman dan state berikut:

1. Splash screen
2. Login marketing
3. Izin lokasi
4. Izin lokasi background
5. Izin notifikasi
6. Izin kamera
7. Dashboard marketing
8. Detail status tracking
9. Daftar konsumen
10. Tambah data konsumen
11. Detail konsumen
12. Edit data konsumen
13. Form laporan kunjungan
14. Pilih konsumen
15. Kamera atau pilih foto
16. Preview foto laporan
17. Konfirmasi laporan
18. Laporan berhasil dikirim
19. Laporan disimpan offline
20. Daftar laporan
21. Detail laporan
22. Riwayat perjalanan
23. Detail perjalanan
24. Status sinkronisasi
25. Profil marketing
26. Ubah password
27. Konfirmasi logout
28. Kondisi GPS mati
29. Kondisi izin lokasi ditolak
30. Kondisi internet terputus
31. Kondisi gagal sinkronisasi
32. Empty state
33. Loading state
34. Error state

Pastikan seluruh halaman memiliki gaya visual yang konsisten.

==================================================
6. DETAIL HALAMAN SPLASH SCREEN
==================================================

Buat splash screen sederhana dan premium.

Isi:
- latar hitam;
- area logo MMS Group di tengah;
- teks besar “MMS GROUP”;
- teks kecil “Marketing Monitoring”;
- subjudul “KSP Manunggal Makmur Sejahtera”;
- aksen garis atau gelombang emas yang tipis;
- loading indicator kecil di bagian bawah;
- versi aplikasi dalam teks kecil.

Jangan terlalu ramai.

==================================================
7. DETAIL HALAMAN LOGIN
==================================================

Buat halaman login profesional.

Bagian atas:
- latar hitam;
- placeholder logo MMS Group;
- judul “Selamat Datang”;
- teks “Silakan masuk menggunakan akun marketing Anda.”

Bagian form:
- field username;
- field password;
- ikon tampilkan/sembunyikan password;
- checkbox “Ingat saya”;
- tombol utama “Masuk”;
- teks bantuan “Hubungi admin apabila lupa akun atau password.”

Gunakan tombol utama berwarna emas dengan teks hitam.

Tambahkan state:
- username kosong;
- password kosong;
- akun tidak ditemukan;
- password salah;
- akun tidak aktif;
- proses login;
- tidak ada koneksi internet.

Pesan error harus singkat dan mudah dipahami.

==================================================
8. HALAMAN PERMINTAAN IZIN
==================================================

Buat halaman izin yang ramah dan tidak menakutkan.

A. Izin lokasi

Judul:
“Aktifkan Akses Lokasi”

Penjelasan:
“Aplikasi membutuhkan akses lokasi untuk mencatat perjalanan marketing selama bekerja.”

Tampilkan tiga poin manfaat:
- mencatat perjalanan;
- menyimpan riwayat rute;
- membantu admin melihat aktivitas lapangan.

Tombol:
- “Izinkan Lokasi”
- “Nanti Saja”

B. Izin lokasi background

Judul:
“Izinkan Tracking di Latar Belakang”

Penjelasan:
“Agar perjalanan tetap tercatat saat aplikasi tidak sedang dibuka, izinkan akses lokasi sepanjang aplikasi digunakan.”

Tambahkan ilustrasi sederhana berupa smartphone dan pin lokasi.

C. Izin notifikasi

Penjelasan bahwa notifikasi digunakan untuk menunjukkan tracking sedang aktif.

D. Izin kamera

Penjelasan bahwa kamera digunakan untuk mengambil foto laporan kunjungan.

Gunakan bahasa sederhana dan jangan menggunakan istilah “foreground service”.

==================================================
9. DASHBOARD MARKETING
==================================================

Dashboard merupakan halaman utama setelah login.

Header:
- latar hitam dengan aksen emas;
- placeholder logo kecil;
- teks “Selamat pagi,”;
- nama pengguna, contoh “Budi Santoso”;
- nama resort, contoh “Resort 3”;
- ikon notifikasi;
- foto profil atau avatar.

Bagian status utama:
buat satu kartu besar yang sangat jelas.

Judul:
“Tracking Perjalanan”

Status aktif:
- indikator hijau;
- teks “Tracking Aktif”;
- waktu mulai, contoh “Aktif sejak 08.05”;
- ikon GPS;
- status internet;
- status sinkronisasi;
- informasi “Lokasi dicatat otomatis.”

Status offline:
- indikator oranye;
- teks “Sedang Offline”;
- informasi “Lokasi tetap disimpan di perangkat.”
- informasi jumlah data belum terkirim.

Status GPS mati:
- indikator merah;
- teks “GPS Tidak Aktif”;
- tombol “Aktifkan GPS”.

Statistik hari ini:
buat empat kartu ringkas dalam grid 2 × 2:

- Kunjungan Hari Ini;
- Laporan Terkirim;
- Data Konsumen;
- Belum Tersinkron.

Gunakan ikon sederhana dan angka yang mudah dibaca.

Menu cepat:
- Tambah Konsumen;
- Buat Laporan;
- Lihat Riwayat;
- Status Sinkronisasi.

Aktivitas terbaru:
tampilkan tiga aktivitas terakhir, misalnya:
- laporan kunjungan dikirim;
- data konsumen ditambahkan;
- perjalanan tersinkron.

Tambahkan tautan “Lihat Semua”.

Jangan menampilkan grafik keuangan, target, drop, storting, sirkulasi, atau data resort lain pada dashboard marketing.

==================================================
10. HALAMAN DETAIL STATUS TRACKING
==================================================

Buat halaman yang menjelaskan kondisi tracking.

Tampilkan:
- status tracking;
- status GPS;
- status koneksi;
- status izin lokasi;
- waktu mulai tracking;
- titik lokasi terakhir;
- waktu lokasi terakhir diperbarui;
- jumlah titik tersimpan;
- jumlah titik menunggu sinkronisasi;
- penggunaan baterai dalam bentuk informasi sederhana;
- peta kecil atau placeholder peta.

Gunakan kartu status:
- hijau untuk aktif;
- oranye untuk menunggu sinkronisasi;
- merah untuk masalah izin atau GPS.

Sediakan tombol:
- “Perbarui Lokasi”;
- “Buka Pengaturan GPS”;
- “Coba Sinkronkan”.

Jangan menyediakan tombol untuk menghapus riwayat lokasi.

==================================================
11. HALAMAN DAFTAR KONSUMEN
==================================================

Header:
- judul “Data Konsumen”;
- tombol tambah berbentuk ikon plus;
- kolom pencarian;
- filter sederhana.

Filter:
- semua;
- dikunjungi hari ini;
- perlu tindak lanjut;
- sudah selesai.

Setiap kartu konsumen menampilkan:
- nama konsumen;
- nomor kontak;
- alamat singkat;
- status kunjungan;
- tanggal kunjungan terakhir;
- nama resort;
- tombol lihat detail.

Gunakan badge status:
- Baru;
- Tertarik;
- Perlu Follow Up;
- Tidak Tertarik;
- Selesai.

Tambahkan empty state:
“Belum ada data konsumen.”

Tombol:
“Tambah Konsumen Pertama”

==================================================
12. FORM TAMBAH DATA KONSUMEN
==================================================

Buat form yang mudah diisi di lapangan.

Field:
- nama konsumen;
- nomor telepon;
- alamat;
- lokasi saat ini;
- nama usaha atau pekerjaan, opsional;
- jenis konsumen;
- hasil awal kunjungan;
- catatan;
- tanggal kunjungan;
- waktu kunjungan.

Lokasi saat ini:
- isi otomatis dari GPS;
- tampilkan alamat singkat;
- tampilkan koordinat dalam teks kecil;
- tombol “Perbarui Lokasi”.

Status konsumen:
- Baru;
- Tertarik;
- Perlu Follow Up;
- Tidak Tertarik;
- Selesai.

Tombol:
- “Simpan Konsumen”;
- “Batal”.

Berikan validasi pada field wajib.

Gunakan progress indicator bila form terdiri dari lebih dari satu langkah.

==================================================
13. DETAIL KONSUMEN
==================================================

Tampilkan:
- nama;
- nomor telepon;
- alamat;
- status;
- tanggal kunjungan terakhir;
- lokasi;
- catatan;
- riwayat laporan;
- foto terakhir;
- aktivitas terkait.

Tombol:
- “Buat Laporan Kunjungan”;
- “Edit Data”;
- “Hubungi Konsumen”.

Tombol hubungi hanya menjadi desain UI, tidak perlu integrasi WhatsApp dalam versi ini.

==================================================
14. FORM LAPORAN KUNJUNGAN
==================================================

Buat alur laporan yang ringkas.

Tahap 1: Pilih konsumen
- cari konsumen;
- pilih konsumen;
- tambah konsumen baru jika belum ada.

Tahap 2: Isi hasil kunjungan

Field:
- tujuan kunjungan;
- hasil kunjungan;
- status konsumen;
- catatan;
- rencana tindak lanjut;
- tanggal tindak lanjut, opsional.

Pilihan hasil:
- berhasil bertemu;
- tidak bertemu;
- konsumen tertarik;
- perlu follow up;
- tidak tertarik;
- transaksi selesai.

Tahap 3: Foto laporan
- tombol “Ambil Foto”;
- tombol “Pilih dari Galeri”;
- preview foto;
- hapus atau ganti foto;
- kolom keterangan foto.

Tampilkan informasi otomatis:
- tanggal;
- jam;
- lokasi;
- nama marketing;
- resort.

Tahap 4: Konfirmasi
- ringkasan konsumen;
- hasil kunjungan;
- catatan;
- foto;
- lokasi;
- status koneksi.

Tombol:
- “Kirim Laporan”;
- “Simpan Sebagai Draft”;
- “Kembali”.

==================================================
15. FOTO LAPORAN
==================================================

Buat halaman preview foto yang jelas.

Tampilkan:
- foto besar;
- tanggal dan waktu;
- lokasi;
- konsumen;
- kolom keterangan;
- tombol ambil ulang;
- tombol gunakan foto.

Jika foto terlalu besar, tampilkan informasi:
“Foto akan dikompres sebelum dikirim.”

Jika offline:
“Foto disimpan di perangkat dan akan dikirim setelah internet kembali aktif.”

==================================================
16. STATUS LAPORAN
==================================================

Buat tiga kondisi:

A. Berhasil dikirim
- ikon centang hijau;
- teks “Laporan Berhasil Dikirim”;
- nomor atau kode laporan;
- tombol “Lihat Laporan”;
- tombol “Kembali ke Beranda”.

B. Tersimpan offline
- ikon cloud offline;
- teks “Laporan Disimpan di Perangkat”;
- penjelasan “Laporan akan dikirim otomatis saat internet kembali aktif.”

C. Gagal
- ikon peringatan;
- teks “Laporan Belum Berhasil Dikirim”;
- tombol “Coba Lagi”;
- tombol “Simpan untuk Dikirim Nanti”.

==================================================
17. HALAMAN DAFTAR LAPORAN
==================================================

Tampilkan:
- pencarian;
- filter tanggal;
- filter status;
- daftar laporan.

Setiap laporan menampilkan:
- nama konsumen;
- tanggal dan waktu;
- hasil kunjungan;
- status pengiriman;
- thumbnail foto;
- lokasi singkat.

Status:
- Terkirim;
- Menunggu Sinkronisasi;
- Draft;
- Gagal.

==================================================
18. RIWAYAT PERJALANAN
==================================================

Buat halaman daftar perjalanan marketing sendiri.

Tampilkan per kartu:
- tanggal;
- waktu mulai;
- waktu terakhir tercatat;
- durasi;
- jumlah titik lokasi;
- jumlah kunjungan;
- status sinkronisasi;
- tombol “Lihat Detail”.

Gunakan filter:
- hari ini;
- minggu ini;
- bulan ini;
- pilih tanggal.

Tambahkan empty state:
“Belum ada riwayat perjalanan.”

==================================================
19. DETAIL PERJALANAN
==================================================

Tampilkan:
- tanggal;
- waktu mulai;
- durasi;
- status tracking;
- peta rute;
- titik awal;
- titik akhir;
- daftar konsumen yang dikunjungi;
- jumlah laporan;
- status sinkronisasi.

Gunakan placeholder peta yang realistis.

Jangan membuat fitur edit rute.

==================================================
20. STATUS SINKRONISASI
==================================================

Buat halaman khusus antrean data.

Bagian ringkasan:
- data lokasi menunggu;
- laporan menunggu;
- foto menunggu;
- sinkronisasi terakhir.

Daftar antrean:
- jenis data;
- tanggal;
- waktu;
- status;
- progress.

Tombol:
- “Sinkronkan Sekarang”.

Kondisi:
- semua data sudah tersinkron;
- sedang mengirim;
- gagal sebagian;
- tidak ada internet.

Microcopy:
“Data akan dikirim otomatis ketika koneksi internet tersedia.”

==================================================
21. PROFIL MARKETING
==================================================

Tampilkan:
- foto atau avatar;
- nama marketing;
- username;
- resort;
- nomor identitas;
- status akun.

Menu:
- Informasi Akun;
- Ubah Password;
- Status Izin Aplikasi;
- Panduan Singkat;
- Tentang Aplikasi;
- Keluar.

Tambahkan kartu status tracking:
“Tracking akan berhenti setelah Anda keluar dari akun.”

Tombol logout berwarna merah lembut.

Saat logout dipilih, tampilkan modal konfirmasi:

“Keluar dari aplikasi?”

“Tracking perjalanan akan dihentikan dan Anda harus login kembali untuk mengaktifkannya.”

Tombol:
- “Batal”;
- “Ya, Keluar”.

==================================================
22. KONDISI OFFLINE
==================================================

Buat banner offline yang muncul di bagian atas aplikasi:

“Tidak ada koneksi internet. Data tetap disimpan di perangkat.”

Gunakan warna oranye lembut.

Jangan memblokir seluruh aplikasi saat offline.

Pengguna tetap dapat:
- melihat data yang sudah tersimpan;
- menambah konsumen;
- membuat laporan;
- mengambil foto;
- melihat antrean sinkronisasi.

Tandai data baru dengan badge:
“Belum Tersinkron”.

==================================================
23. KONDISI GPS DAN IZIN LOKASI
==================================================

A. GPS mati

Tampilkan modal:
“GPS Tidak Aktif”

Penjelasan:
“Aktifkan GPS agar perjalanan dapat tercatat.”

Tombol:
- “Buka Pengaturan GPS”;
- “Nanti”.

B. Izin lokasi ditolak

Tampilkan halaman:
“Akses Lokasi Diperlukan”

Penjelasan:
“Tracking tidak dapat berjalan tanpa izin lokasi.”

Tombol:
- “Buka Pengaturan Aplikasi”;
- “Coba Lagi”.

C. Penghemat baterai membatasi aplikasi

Tampilkan informasi:
“Penghemat baterai dapat menghentikan tracking di latar belakang.”

Tombol:
“Buka Pengaturan Baterai”

Gunakan bahasa sederhana.

==================================================
24. EMPTY, LOADING, DAN ERROR STATE
==================================================

Buat empty state untuk:
- belum ada konsumen;
- belum ada laporan;
- belum ada perjalanan;
- belum ada aktivitas;
- antrean sinkronisasi kosong.

Buat loading state:
- skeleton card;
- progress indicator;
- teks “Memuat data...”.

Buat error state:
- gagal memuat data;
- server tidak merespons;
- gagal mengirim foto;
- sesi login berakhir.

Tombol error:
- “Coba Lagi”;
- “Kembali ke Beranda”.

==================================================
25. KOMPONEN YANG HARUS KONSISTEN
==================================================

Buat komponen reusable:

- app bar;
- bottom navigation;
- primary button;
- secondary button;
- destructive button;
- input field;
- dropdown;
- date picker;
- search field;
- filter chip;
- status badge;
- customer card;
- report card;
- journey card;
- sync card;
- notification banner;
- confirmation modal;
- empty state;
- loading state;
- error state;
- photo uploader;
- location status card.

Gunakan Auto Layout.

Gunakan spacing system:
- 4 px;
- 8 px;
- 12 px;
- 16 px;
- 24 px;
- 32 px.

Gunakan grid yang konsisten.

==================================================
26. MICROCOPY
==================================================

Gunakan bahasa Indonesia yang natural dan mudah dipahami.

Contoh:
- “Tracking aktif”
- “Lokasi dicatat otomatis”
- “Data belum tersinkron”
- “Internet kembali aktif”
- “Data sedang dikirim”
- “Laporan berhasil disimpan”
- “GPS tidak aktif”
- “Aktifkan lokasi untuk melanjutkan”
- “Belum ada konsumen”
- “Tambah konsumen pertama”
- “Laporan akan dikirim saat internet kembali aktif”

Hindari istilah:
- API;
- endpoint;
- local database;
- foreground service;
- background task;
- cache;
- server timeout.

==================================================
27. INTERAKSI PROTOTYPE
==================================================

Buat prototype yang dapat diklik.

Interaksi minimal:

- Splash → Login;
- Login → Izin Lokasi;
- Izin Lokasi → Dashboard;
- Dashboard → Data Konsumen;
- Dashboard → Buat Laporan;
- Dashboard → Riwayat;
- Dashboard → Status Tracking;
- Tambah Konsumen → Simpan;
- Pilih Konsumen → Form Laporan;
- Form Laporan → Foto;
- Foto → Konfirmasi;
- Konfirmasi → Berhasil atau Tersimpan Offline;
- Riwayat → Detail Perjalanan;
- Profil → Logout;
- Logout → Login.

Gunakan transisi halus dan sederhana.

Jangan menggunakan animasi berlebihan.

==================================================
28. KONTEN DUMMY
==================================================

Gunakan data contoh yang realistis:

Nama marketing:
- Budi Santoso

Resort:
- Resort 3

Konsumen:
- Ahmad Hidayat
- Siti Nurjanah
- Dedi Kurniawan
- Toko Berkah Jaya

Status:
- Tertarik
- Perlu Follow Up
- Selesai

Contoh alamat:
- Gedebage, Kota Bandung
- Rancasari, Kota Bandung
- Buahbatu, Kota Bandung

Contoh aktivitas:
- “Laporan kunjungan berhasil dikirim”
- “Data konsumen baru ditambahkan”
- “12 titik lokasi berhasil disinkronkan”

Gunakan angka yang wajar dan tidak berlebihan.

==================================================
29. HAL YANG TIDAK BOLEH DIBUAT
==================================================

Jangan membuat:

- dashboard admin;
- menu Resort 1 sampai Resort 10;
- data marketing lain;
- monitoring seluruh pegawai;
- grafik keuangan;
- target nominal;
- drop;
- storting;
- sirkulasi;
- data anggota koperasi;
- transaksi;
- pinjaman;
- simpanan;
- payroll;
- absensi wajah;
- WhatsApp blast;
- publikasi Play Store;
- fitur iOS;
- fitur chat;
- fitur edit rute;
- tombol mematikan tracking tanpa logout.

==================================================
30. HASIL AKHIR
==================================================

Hasil akhir harus berupa rancangan aplikasi mobile Android yang lengkap dan konsisten, meliputi:

- design system;
- komponen reusable;
- seluruh halaman utama;
- kondisi online dan offline;
- kondisi izin ditolak;
- kondisi GPS mati;
- status tracking;
- status sinkronisasi;
- empty state;
- loading state;
- error state;
- prototype alur utama.

Susun halaman secara berurutan dan beri nama frame yang jelas.

Gunakan penamaan frame:

01 Splash
02 Login
03 Permission Location
04 Permission Background
05 Dashboard Active
06 Dashboard Offline
07 Tracking Detail
08 Customer List
09 Add Customer
10 Customer Detail
11 Report Form
12 Photo Capture
13 Report Confirmation
14 Report Success
15 Report Offline
16 Report List
17 Report Detail
18 Journey History
19 Journey Detail
20 Sync Status
21 Profile
22 Change Password
23 GPS Disabled
24 Permission Denied
25 Empty State
26 Loading State
27 Error State

Pastikan desain terlihat seperti aplikasi perusahaan sungguhan, bukan template generik dan bukan dashboard admin yang diperkecil menjadi tampilan mobile.