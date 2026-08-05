Lanjutkan revisi project “MMS Marketing” yang sudah ada.

Jangan membuat ulang project dari awal dan jangan mengganti arah visual utama. Pertahankan tema hitam, putih, abu-abu muda, dan emas karena tampilannya sudah sesuai dengan referensi customer.

Pertahankan:
- splash screen;
- halaman login;
- alur permintaan izin;
- dashboard marketing;
- tracking detail;
- data konsumen;
- laporan kunjungan;
- riwayat perjalanan;
- status sinkronisasi;
- profil;
- bottom navigation Beranda, Konsumen, Laporan, Riwayat, dan Profil;
- gaya kartu, ikon, border, radius, dan warna yang sudah digunakan.

Fokus revisi pada layout mobile, konsistensi data, kelengkapan interaksi, kondisi offline, dan detail form.

==================================================
1. PERBAIKI TINGGI APLIKASI MOBILE
==================================================

Perbaiki layout agar aplikasi selalu memenuhi tinggi layar Android.

Saat ini pada ukuran 390 × 844 px:
- halaman tidak memenuhi seluruh tinggi layar;
- terdapat area kosong di bawah splash, login, dan halaman izin;
- bottom navigation berada di bawah viewport;
- pengguna harus scroll untuk menemukan navigasi;
- menu cepat dashboard terpotong.

Gunakan struktur mobile dengan tinggi penuh berdasarkan dynamic viewport:
- aplikasi utama harus menggunakan tinggi 100dvh;
- layar mobile tidak boleh menggunakan tinggi berdasarkan isi konten;
- area konten utama harus dapat di-scroll secara internal;
- bottom navigation selalu berada di bawah layar;
- bottom navigation tidak ikut terdorong ke bawah oleh panjang konten;
- bottom navigation tidak menutupi konten;
- berikan padding bawah yang sesuai;
- dukung safe area Android;
- pada desktop, tetap tampilkan preview phone frame 390 × 844 px.

Pastikan splash, login, permission, dashboard, konsumen, laporan, riwayat, dan profil memenuhi tinggi layar.

==================================================
2. KONSISTENSIKAN DATA DI SELURUH HALAMAN
==================================================

Gunakan satu kondisi data yang konsisten di semua halaman.

Gunakan data awal berikut:
- 5 konsumen;
- 3 kunjungan hari ini;
- 2 laporan berhasil terkirim;
- 1 laporan menunggu sinkronisasi;
- 247 titik GPS menunggu sinkronisasi;
- 1 foto menunggu sinkronisasi;
- perjalanan dimulai pukul 08.05;
- jarak perjalanan 8,4 km.

Dashboard harus menampilkan:
- Kunjungan: 03;
- Laporan Terkirim: 02;
- Data Konsumen: 05;
- Belum Tersinkron: 01.

Tambahkan keterangan kecil:
“2 laporan terkirim, 1 menunggu sinkronisasi.”

Pastikan data pada dashboard, daftar laporan, riwayat perjalanan, dan status sinkronisasi tidak saling bertentangan.

==================================================
3. PERBAIKI BOTTOM NAVIGATION
==================================================

Bottom navigation harus:
- selalu terlihat pada halaman Beranda, Konsumen, Laporan, Riwayat, dan Profil;
- menempel di bagian bawah layar;
- tidak berada di bawah seluruh panjang halaman;
- tidak menutupi kartu atau tombol;
- memiliki label minimal 11 px;
- memiliki area tekan minimal 44 × 44 px;
- menampilkan indikator menu aktif secara jelas;
- tetap menggunakan tema hitam dan emas.

Halaman detail tidak perlu menampilkan bottom navigation.

==================================================
4. PERBAIKI DASHBOARD
==================================================

Pertahankan struktur dashboard saat ini.

Lakukan penyesuaian:
- tampilkan 5 data konsumen;
- tampilkan 1 data belum tersinkron;
- tambahkan keterangan “2 laporan terkirim, 1 menunggu sinkronisasi”;
- pastikan menu cepat tidak terpotong;
- buat seluruh kartu tracking dapat diklik;
- tampilkan waktu lokasi terakhir diperbarui;
- tampilkan GPS aktif, internet tersambung, dan status sinkronisasi;
- besarkan teks kecil yang masih sulit dibaca.

Hapus ikon lonceng pada header karena aplikasi tidak memiliki fitur notifikasi internal. Izin notifikasi hanya digunakan untuk notifikasi tracking background.

Pertahankan avatar, nama marketing, nama resort, dan tombol menuju profil.

==================================================
5. BUAT KONDISI ONLINE DAN OFFLINE DAPAT DILIHAT
==================================================

Saat ini kondisi offline sudah tersedia secara visual, tetapi tidak dapat dibuka dari prototype.

Buat state atau frame demo terpisah untuk:
- Dashboard Online;
- Dashboard Offline;
- GPS Mati;
- Izin Lokasi Ditolak;
- Sinkronisasi Gagal;
- Laporan Disimpan Offline;
- Semua Data Tersinkron;
- Server Tidak Merespons;
- Sesi Login Berakhir.

Jangan menampilkan pengaturan mode demo kepada customer pada tampilan produksi.

State offline harus menampilkan banner:
“Tidak ada koneksi internet. Data tetap disimpan di perangkat.”

Saat offline:
- tracking tetap tercatat;
- laporan dapat dibuat;
- foto dapat disimpan;
- data mendapat badge “Belum Tersinkron”;
- admin belum dapat melihat pergerakan terbaru sampai internet kembali aktif.

==================================================
6. PERBAIKI ALUR IZIN
==================================================

Untuk izin lokasi dan lokasi background:

Jika pengguna memilih “Nanti Saja”, jangan langsung menampilkan Tracking Aktif.

Tampilkan modal:
“Tracking tidak dapat berjalan tanpa izin lokasi.”

Tombol:
- “Izinkan Sekarang”;
- “Lanjutkan Tanpa Tracking”.

Jika pengguna melanjutkan tanpa izin:
- dashboard tetap dapat dibuka;
- kartu tracking menampilkan “Tracking Tidak Aktif”;
- indikator berwarna merah atau oranye;
- tampilkan tombol “Aktifkan Izin Lokasi”.

Untuk izin kamera dan notifikasi, pengguna boleh melewati proses tanpa memblokir aplikasi.

==================================================
7. BUAT HALAMAN STATUS IZIN APLIKASI
==================================================

Menu “Status Izin Aplikasi” pada profil jangan membuka ulang onboarding permission.

Buat halaman khusus:

Judul:
“Status Izin Aplikasi”

Tampilkan daftar:
- Akses Lokasi — Diizinkan;
- Lokasi Latar Belakang — Diizinkan;
- Notifikasi Tracking — Diizinkan;
- Kamera — Diizinkan;
- Penyimpanan Foto — Diizinkan.

Setiap bagian memiliki:
- ikon;
- status;
- penjelasan singkat;
- tombol “Buka Pengaturan” jika izin belum diberikan.

==================================================
8. LENGKAPI INTERAKSI MENU PROFIL
==================================================

Buat halaman sederhana untuk:

A. Informasi Akun
- nama marketing;
- username;
- ID marketing;
- resort;
- status akun;
- nomor telepon jika tersedia.

B. Panduan Penggunaan
- cara login;
- cara memastikan tracking aktif;
- cara menambah konsumen;
- cara membuat laporan;
- cara melihat sinkronisasi;
- informasi kondisi offline.

C. Tentang Aplikasi
- nama aplikasi;
- versi;
- fungsi singkat;
- dibuat untuk MMS Group;
- informasi Bandung Coding sebagai pengembang.

Pertahankan halaman Ubah Password yang sudah ada.

==================================================
9. PERBAIKI DATA KONSUMEN
==================================================

Pertahankan desain daftar dan form konsumen.

Tambahkan pada form:
- lokasi saat ini;
- alamat hasil GPS;
- koordinat dalam teks kecil;
- tombol “Perbarui Lokasi”;
- tanggal dan waktu otomatis;
- validasi status konsumen;
- pesan error di bawah dropdown jika status belum dipilih.

Tombol filter pada halaman konsumen harus:
- membuka bottom sheet filter lanjutan; atau
- dihapus jika filter chip sudah cukup.

Tombol “Hubungi Konsumen” harus membuka pilihan:
- Telepon Konsumen;
- Salin Nomor;
- Batal.

Jangan menambahkan integrasi WhatsApp otomatis.

==================================================
10. LENGKAPI FORM LAPORAN
==================================================

Pertahankan alur laporan beberapa langkah.

Perbaiki bagian berikut:

Tahap hasil kunjungan:
- tujuan kunjungan;
- hasil kunjungan;
- status konsumen;
- catatan;
- rencana tindak lanjut;
- tanggal tindak lanjut menggunakan date picker.

Tahap foto:
- ambil foto;
- pilih dari galeri;
- tampilkan thumbnail atau preview foto;
- ganti foto;
- hapus foto;
- tambahkan field “Keterangan Foto”;
- tampilkan informasi bahwa foto akan dikompres.

Tahap konfirmasi:
- konsumen;
- hasil kunjungan;
- status;
- catatan;
- tanggal follow-up;
- preview foto;
- keterangan foto;
- lokasi;
- tanggal dan waktu;
- nama marketing;
- resort;
- status koneksi.

Tombol “Simpan sebagai Draft” harus berfungsi dan menampilkan:
“Laporan berhasil disimpan sebagai draft.”

==================================================
11. PERBAIKI DAFTAR DAN DETAIL LAPORAN
==================================================

Setiap kartu laporan menampilkan:
- thumbnail foto kecil;
- nama konsumen;
- hasil kunjungan;
- tanggal dan waktu;
- lokasi;
- status pengiriman.

Gunakan status:
- Terkirim;
- Menunggu Sinkronisasi;
- Draft;
- Gagal.

Pada detail laporan:
- tampilkan foto contoh yang realistis, bukan kotak kosong abu-abu;
- tampilkan keterangan foto;
- tampilkan data lokasi;
- tampilkan status pengiriman;
- tampilkan tanggal sinkronisasi jika sudah terkirim.

==================================================
12. PERBAIKI DETAIL TRACKING
==================================================

Pertahankan halaman detail tracking.

Ganti tulisan:
“Lokasi dicatat otomatis setiap 30 detik”

menjadi:
“Lokasi dicatat secara berkala selama tracking aktif.”

Ganti tulisan:
“Tracking menggunakan ~3–5% baterai per jam”

menjadi:
“Tracking dapat meningkatkan penggunaan baterai selama aktif.”

Jangan menampilkan angka konsumsi baterai yang pasti.

Tombol berikut harus memiliki feedback:
- Perbarui Lokasi;
- Coba Sinkronkan;
- Buka Pengaturan GPS.

Saat Perbarui Lokasi ditekan:
- tampilkan loading singkat;
- ubah waktu lokasi terakhir;
- tampilkan pesan “Lokasi berhasil diperbarui.”

==================================================
13. PERBAIKI STATUS SINKRONISASI
==================================================

Gunakan data yang konsisten:
- 247 titik lokasi;
- 1 laporan;
- 1 foto.

Saat “Sinkronkan Sekarang” ditekan:
- tampilkan progress;
- ubah status item satu per satu;
- setelah selesai, seluruh jumlah menjadi 0;
- tampilkan pesan “Semua data berhasil dikirim.”

Jika gagal:
- tampilkan item yang gagal;
- tombol “Coba Lagi”;
- data tidak boleh hilang.

==================================================
14. PERBAIKI TIPOGRAFI DAN KETERBACAAN
==================================================

Gunakan ukuran minimal:
- judul halaman: 22–24 px;
- judul kartu: 15–18 px;
- teks isi: 13–14 px;
- teks keterangan: minimal 12 px;
- teks tombol: minimal 14 px;
- label bottom navigation: minimal 11 px.

Kurangi penggunaan teks 10 px.

Pastikan warna abu-abu tetap terbaca pada latar putih.

Gunakan font Manrope secara konsisten dan jangan mencampurkan font serif.

==================================================
15. INTERAKSI YANG HARUS BERFUNGSI
==================================================

Pastikan tombol berikut tidak kosong:

- Tambah Konsumen;
- Edit Konsumen;
- Hubungi Konsumen;
- Buat Laporan;
- Simpan sebagai Draft;
- Perbarui Lokasi;
- Coba Sinkronkan;
- Status Izin Aplikasi;
- Informasi Akun;
- Panduan Penggunaan;
- Tentang Aplikasi;
- Ubah Password;
- Logout.

Jika fitur belum termasuk scope, jangan tampilkan tombol yang tidak memiliki fungsi.

==================================================
16. HASIL AKHIR
==================================================

Hasil revisi harus:
- tetap menggunakan desain yang sekarang;
- tidak membuat ulang keseluruhan UI;
- memenuhi tinggi layar Android;
- bottom navigation selalu terlihat;
- tidak memiliki area kosong yang tidak diperlukan;
- memiliki data konsisten;
- memiliki seluruh tombol utama yang dapat diklik;
- memiliki state online dan offline;
- memiliki state GPS mati dan izin ditolak;
- memiliki form laporan lengkap;
- memiliki tampilan foto yang realistis;
- memiliki teks yang mudah dibaca;
- tetap terlihat profesional dan tidak seperti template AI;
- berhasil dijalankan tanpa error build.

Jangan menambahkan fitur admin, grafik keuangan, target, drop, storting, sirkulasi, simpanan, pinjaman, payroll, absensi wajah, chat, atau WhatsApp blast.