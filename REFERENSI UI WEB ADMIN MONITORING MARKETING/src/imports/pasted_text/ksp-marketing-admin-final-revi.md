Lanjutkan revisi FINAL project web admin “KSP Marketing Monitoring” yang sudah ada.

Project ini adalah front-end dan prototype UI web admin yang nantinya terhubung dengan APK marketing.

JANGAN membuat ulang project dari awal.

JANGAN menghapus permanen:
- route lama;
- halaman lama;
- komponen lama;
- data dummy lama;
- aset lama;
- modal lama;
- peta lama.

Halaman lama yang tidak lagi digunakan cukup disembunyikan dari navigasi utama.

Project ini hanya prototype UI. Tidak perlu membuat:
- backend nyata;
- database nyata;
- autentikasi nyata;
- GPS nyata;
- Maps API nyata;
- upload nyata;
- sinkronisasi nyata;
- perhitungan keuangan nyata.

Namun, seluruh alur UI harus menggambarkan dengan jelas bagaimana data dari APK marketing masuk dan ditampilkan pada web admin.

HASIL REVISI INI HARUS FINAL.

Jangan hanya memperbaiki sebagian halaman.
Jangan meninggalkan placeholder rusak.
Jangan meninggalkan tombol tanpa fungsi.
Jangan meninggalkan tampilan putih yang bertentangan dengan tema.
Jangan menghasilkan beberapa scrollbar pada satu halaman.

==================================================
1. MASALAH YANG WAJIB DIPERBAIKI
==================================================

Perbaiki seluruh masalah berikut:

1. Logo resmi tidak muncul dan menampilkan ikon gambar rusak.
2. Tema masih dominan putih.
3. Modal Rekap Operasional terlalu kecil untuk tabel yang sangat lebar.
4. Tabel terpotong pada layar HP.
5. Muncul beberapa scrollbar vertikal dan horizontal sekaligus.
6. Terdapat nested scroll atau scroll di dalam scroll.
7. Beberapa tombol masih hanya berupa tampilan tanpa navigasi.
8. Detail Total Anggota, Target, Drop, dan Storting masih bercampur.
9. Data Anggota dari APK belum memiliki halaman dan detail yang lengkap.
10. Laporan Harian dari APK belum memiliki halaman yang sesuai.
11. Dua foto dari APK belum ditampilkan dengan benar.
12. Data lokasi dari APK belum ditampilkan dengan jelas.
13. Tracking marketing belum memiliki status aktif, offline, belum mulai, dan riwayat yang lengkap.
14. Jadwal tiap marketing belum memiliki halaman pengelolaan.
15. Detail marketing belum menampilkan data yang masuk dari APK.
16. Tema modal, tabel, form, drawer, dan detail belum sepenuhnya black–gold.
17. Mobile masih terasa seperti dashboard desktop yang diperkecil.
18. Beberapa halaman masih menggunakan white surface terlalu besar.
19. Header hari belum selalu mengikuti hari yang dipilih.
20. Belum ada alur data masuk dari APK menuju dashboard admin.

==================================================
2. JANGAN GUNAKAN MODAL UNTUK HALAMAN BESAR
==================================================

Jangan tampilkan Rekap Operasional, Detail Total Anggota, Detail Target, Detail Drop, Detail Storting, Data Anggota, Laporan Harian, atau tabel besar di dalam modal kecil.

Gunakan halaman penuh atau dedicated route.

Contoh:

/dashboard
/rekap-operasional
/rekap/anggota
/rekap/target
/rekap/drop
/rekap/storting
/marketing
/marketing/:id
/jadwal
/tracking
/tracking/:id
/riwayat/:id
/anggota
/anggota/:id
/laporan-harian
/laporan-harian/:id
/bukti-kunjungan
/bukti-kunjungan/:id
/profile

Pada mobile:
- detail besar dibuka sebagai halaman penuh;
- gunakan tombol kembali;
- bottom navigation hanya tampil pada halaman utama;
- halaman detail tidak perlu menggunakan bottom navigation.

Pada desktop:
- detail dapat menggunakan halaman penuh;
- panel samping hanya digunakan untuk informasi pendek;
- jangan menggunakan drawer sempit untuk tabel besar.

==================================================
3. PERBAIKI SISTEM SCROLL
==================================================

Hilangkan tampilan banyak scrollbar seperti pada screenshot.

Gunakan aturan:

- body menggunakan overflow-x-hidden;
- hanya ada satu scroll vertikal utama pada halaman;
- jangan membuat nested vertical scroll;
- jangan membuat modal dengan scroll di dalam halaman yang juga dapat scroll;
- jangan menggunakan fixed height pada tabel besar;
- jangan menggunakan container sempit yang memaksa horizontal scroll;
- jangan menampilkan scrollbar browser pada carousel horizontal;
- scrollbar carousel boleh disembunyikan secara visual;
- bottom navigation tidak boleh menyebabkan overflow;
- sidebar tidak boleh membuat scrollbar tambahan;
- modal kecil seperti konfirmasi boleh memiliki konten tetap tanpa scroll.

Struktur mobile:

root:
- min-height: 100dvh;
- overflow-x: hidden;

main:
- width: 100%;
- padding-bottom sesuai tinggi bottom navigation;
- satu vertical page scroll.

Bottom navigation:
- position fixed;
- bottom 0;
- tidak menutupi konten;
- gunakan safe-area-inset-bottom.

Untuk daftar marketing horizontal:
- gunakan swipeable carousel;
- scrollbar disembunyikan;
- gunakan scroll snap;
- kartu tidak terpotong.

Untuk tabel rekap pada mobile:
JANGAN memaksakan seluruh kolom tampil sekaligus.

Gunakan tab data:

- Ringkasan
- Anggota
- Target
- Drop
- Storting
- Lainnya

Setiap tab hanya menampilkan kolom yang relevan.

Contoh:

Tab Anggota:
- Marketing
- Kode
- MG
- L
- M
- K
- S

Tab Target:
- Marketing
- Kode
- Target Lalu
- MSK
- KLR
- S

Tab Drop:
- Marketing
- Kode
- Drop Lalu
- Drop Kini
- Total

Tab Storting:
- Marketing
- Kode
- Storting Lalu
- Storting Kini
- Total

Tab Lainnya:
- Persentase
- Sirkulasi Lalu
- Sirkulasi Sekarang
- Dibuat Oleh
- Keterangan

Pada desktop:
- tampilkan grouped spreadsheet table lengkap;
- gunakan seluruh lebar halaman;
- header bertingkat;
- tidak berada dalam modal.

==================================================
4. PERBAIKI LOGO RESMI
==================================================

Gunakan logo resmi KSP Manunggal Makmur Sejahtera yang sudah diunggah.

Jangan mengambil logo dari URL eksternal.

Simpan logo sebagai local asset project, misalnya:

src/assets/logo-ksp.png

Import logo dengan benar pada project React/Vite.

Contoh konsep:

import logoKsp from "./assets/logo-ksp.png";

Gunakan:

<img
  src={logoKsp}
  alt="Logo KSP Manunggal Makmur Sejahtera"
  className="object-contain"
/>

Pastikan:
- logo muncul di halaman login;
- logo muncul di header mobile;
- logo muncul di sidebar desktop;
- logo muncul di profil admin;
- logo tidak gepeng;
- logo tidak terpotong;
- aspect ratio dipertahankan;
- logo menggunakan background putih atau krem ketika berada pada header hitam.

Tambahkan fallback apabila gambar gagal dimuat:
- tampilkan monogram KSP;
- jangan menampilkan ikon gambar rusak.

Jangan mengubah logo resmi menjadi warna gold.
Pertahankan warna hijau dan krem asli.

==================================================
5. TERAPKAN TEMA BLACK–GOLD KE SELURUH HALAMAN
==================================================

Tampilan sekarang terlalu putih. Ubah seluruh aplikasi admin menjadi dark black–gold.

Gunakan warna:

Background utama:
#07090C

Background sekunder:
#0D1117

Sidebar dan bottom navigation:
#0A0D12

Card utama:
#121820

Card sekunder:
#171E27

Input:
#111720

Border normal:
rgba(255,255,255,0.10)

Border gold:
rgba(212,175,55,0.35)

Gold utama:
#D4AF37

Gold terang:
#E8C65A

Teks utama:
#F8FAFC

Teks sekunder:
#A8B0BD

Teks redup:
#7E8794

Hijau aktif:
#22C55E

Merah offline:
#EF4444

Oranye:
#F59E0B

Abu-abu belum mulai:
#6B7280

Biru informasi:
#3B82F6

Terapkan tema tersebut pada:

- body;
- header;
- sidebar;
- bottom navigation;
- dashboard;
- kartu ringkasan;
- card marketing;
- tabel;
- header tabel;
- tab;
- filter;
- input;
- select;
- date picker;
- modal;
- drawer;
- detail page;
- profile;
- loading;
- error;
- empty state.

Jangan menggunakan background putih besar.

Warna putih hanya boleh digunakan untuk:
- teks;
- area kecil pada logo;
- foto;
- kebutuhan kontras tertentu.

Modal harus:
- background hitam gelap;
- border gold tipis;
- teks putih;
- tombol gold;
- jangan menggunakan background putih.

Tabel harus:
- background gelap;
- header black;
- header group gold;
- teks putih;
- baris hover sedikit lebih terang;
- border tipis gelap/gold;
- zebra row sangat halus.

==================================================
6. DESIGN SYSTEM FINAL
==================================================

Gunakan font Manrope secara konsisten.

Ukuran:

Mobile title:
22–24 px

Desktop title:
26–30 px

Card title:
15–17 px

Body:
13–15 px

Table:
13 px

Caption:
12 px

Button:
14 px

Gunakan:

- border radius 14–18 px;
- shadow sangat lembut;
- ikon outline;
- spacing 4, 8, 12, 16, 24, dan 32 px;
- area klik minimal 44 × 44 px.

Jangan gunakan:
- glassmorphism berlebihan;
- warna neon;
- gradient berlebihan;
- ilustrasi 3D;
- efek futuristik;
- kartu terlalu kecil;
- tulisan 9–10 px untuk informasi utama.

==================================================
7. RESPONSIVE FINAL
==================================================

Mobile:
390 × 844 px

Tablet:
768 px

Laptop:
1280 px

Desktop:
1440 px

Pada mobile:

- header ringkas;
- bottom navigation;
- tidak ada sidebar;
- kartu ringkasan dua kolom;
- halaman detail full screen;
- tabel berubah menjadi tab atau card;
- status marketing swipe horizontal tanpa scrollbar terlihat;
- peta full width;
- tombol tidak terpotong;
- tidak ada nested scroll;
- tidak ada horizontal overflow halaman.

Pada desktop:

- sidebar tetap;
- bottom navigation disembunyikan;
- kartu tampil sejajar;
- tabel penuh;
- peta dan detail berdampingan;
- konten menggunakan seluruh area.

==================================================
8. NAVIGASI FINAL
==================================================

Bottom navigation mobile:

1. Dashboard
2. Marketing
3. Harian
4. Tracking
5. Profil

Sidebar desktop:

- Dashboard
- Data Marketing
- Data Harian
- Tracking Lokasi
- Data Anggota
- Bukti Kunjungan
- Jadwal Marketing
- Profil Admin

Pastikan seluruh menu dapat diklik.

Jangan gunakan:
- href="#";
- onClick kosong;
- tombol dekoratif tanpa handler;
- toast sebagai pengganti halaman yang memang dibutuhkan.

==================================================
9. HALAMAN LOGIN ADMIN
==================================================

Tampilkan:

- logo resmi;
- nama KSP Manunggal Makmur Sejahtera;
- judul Dashboard Monitoring Marketing;
- username;
- password;
- tampilkan/sembunyikan password;
- Ingat Saya;
- tombol Masuk;
- bantuan lupa akun.

Tema login:
- background black;
- card login dark;
- border gold;
- tombol gold;
- logo tampil jelas.

Login:
→ Dashboard

Logout:
→ Modal konfirmasi
→ Login

==================================================
10. DASHBOARD UTAMA
==================================================

Dashboard menampilkan:

Header:
- logo resmi;
- nama perusahaan;
- nama admin;
- hari aktif;
- tanggal;
- tombol refresh;
- profil.

Kartu ringkasan:

1. Total Anggota
2. Total Target
3. Total Drop
4. Total Storting

Gunakan data konsisten:

Total Anggota:
39 anggota/nasabah

Total Target:
Rp123.500.000

Total Drop:
Rp91.000.000

Total Storting:
Rp37.700.000

Setiap kartu membuka halaman berbeda:

Total Anggota
→ /rekap/anggota

Total Target
→ /rekap/target

Total Drop
→ /rekap/drop

Total Storting
→ /rekap/storting

Jangan membuka modal Rekap Operasional yang sama.

==================================================
11. PILIHAN HARI SENIN–SABTU
==================================================

Dashboard menampilkan tombol:

- Senin
- Selasa
- Rabu
- Kamis
- Jumat
- Sabtu

Hari aktif:
- background gold;
- teks black.

Setelah hari dipilih tampilkan:

- Tracking
- Data Anggota
- Laporan Harian

Data seluruh dashboard harian mengikuti hari aktif.

Jika hari Kamis dipilih:

- header menjadi Kamis;
- Tracking membuka Tracking Kamis;
- Data Anggota membuka Data Anggota Kamis;
- Laporan membuka Laporan Harian Kamis.

Jangan mempertahankan tulisan Senin ketika hari lain dipilih.

==================================================
12. STATUS MARKETING
==================================================

Gunakan 13 marketing:

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

Buat carousel horizontal tanpa scrollbar terlihat.

Setiap kartu:

- foto;
- kode;
- nama;
- jadwal hari aktif;
- status;
- lokasi terakhir;
- waktu terakhir aktif.

Status:

Aktif:
- hijau;
- Tracking Aktif.

Offline:
- merah;
- Marketing Offline;
- waktu terakhir aktif.

Belum mulai:
- abu-abu;
- Belum Memulai Tracking.

Klik kartu:
→ Detail Marketing

==================================================
13. JADWAL MARKETING
==================================================

Setiap marketing memiliki jadwal berbeda.

Buat:

- Daftar Jadwal;
- Detail Jadwal;
- Tambah Jadwal;
- Edit Jadwal.

Field:

- marketing;
- hari;
- tanggal;
- jam mulai;
- jam selesai;
- area;
- tujuan;
- catatan;
- status.

Status:

- Belum Mulai
- Sedang Berjalan
- Selesai
- Tidak Aktif

Semua tombol jadwal harus membuka halaman/form.

Jangan hanya menampilkan toast.

==================================================
14. REKAP OPERASIONAL
==================================================

Jangan menggunakan modal kecil.

Buat halaman penuh:

/rekap-operasional

Di mobile gunakan tab:

- Ringkasan
- Anggota
- Target
- Drop
- Storting
- Lainnya

Di desktop gunakan grouped table lengkap.

Kolom:

Identitas:
- No.
- Marketing.
- Kode.

Anggota:
- MG.
- L.
- M.
- K.
- S.

Target:
- Lalu.
- MSK.
- KLR.
- S.

Drop:
- Lalu.
- Kini.
- Total.

Storting:
- Lalu.
- Kini.
- Total.

Lainnya:
- Persentase.
- Sirkulasi Lalu.
- Sirkulasi Sekarang.
- Dibuat Oleh.
- Keterangan.

Jangan mengubah arti singkatan.

==================================================
15. ALUR DATA DARI APK MARKETING
==================================================

Web admin nantinya menerima data dari APK marketing.

Dalam prototype, buat alur visual sebagai berikut:

APK Marketing
→ Data tersimpan atau dikirim
→ Data masuk ke admin
→ Dashboard dan halaman admin diperbarui

Tampilkan badge:

“Dari APK”

pada data yang berasal dari APK.

Tampilkan informasi:

- waktu diterima;
- marketing pengirim;
- status sinkronisasi;
- hari;
- lokasi;
- data terkait.

Buat section pada Dashboard:

“Data Terbaru dari APK”

Isi contoh:

- Data anggota baru dari M01 Deden.
- Foto anggota diterima dari M03 Ari.
- Bukti transfer diterima dari M05 Sukma.
- Lokasi terbaru diterima dari M02 Angil.
- Laporan harian diterima dari M08 Farhad.

Setiap item dapat diklik menuju detail terkait.

==================================================
16. STRUKTUR DATA DARI APK
==================================================

Gunakan struktur UI berikut sebagai dasar.

Data marketing:

- ID;
- kode;
- nama;
- foto;
- username;
- nomor telepon;
- area atau resort;
- jadwal;
- status akun;
- status tracking;
- lokasi terakhir;
- waktu terakhir aktif.

Data anggota/nasabah dari APK:

- ID;
- resort;
- tanggal;
- nama;
- nomor anggota;
- nomor pinjaman;
- alamat;
- nomor HP;
- usaha;
- jumlah pinjaman;
- angsuran;
- asuransi;
- jaminan;
- status ACC;
- foto anggota/nasabah;
- foto bukti transfer;
- latitude;
- longitude;
- alamat lokasi;
- marketing pembuat;
- waktu input;
- status sinkronisasi.

Laporan harian dari APK:

- marketing;
- kode marketing;
- hari;
- tanggal;
- storting;
- drop;
- tabungan keluar;
- lain-lain;
- target lama nominal;
- target lama jumlah orang;
- target masuk nominal;
- target masuk jumlah orang;
- target keluar nominal;
- target keluar jumlah orang;
- jumlah target nominal;
- jumlah target orang;
- drop baru;
- drop lanjut;
- foto anggota;
- foto bukti transfer;
- lokasi;
- alamat;
- koordinat;
- waktu input;
- status sinkronisasi.

Tracking dari APK:

- marketing;
- jadwal;
- status tracking;
- status internet;
- lokasi terakhir;
- waktu terakhir aktif;
- titik perjalanan;
- titik kunjungan;
- foto kunjungan.

==================================================
17. DATA ANGGOTA
==================================================

Buat halaman:

- Daftar Data Anggota;
- Detail Anggota.

Filter:

- hari;
- tanggal;
- marketing;
- resort;
- status ACC;
- nama;
- nomor anggota;
- nomor pinjaman.

Daftar menampilkan:

- foto;
- nama;
- nomor anggota;
- nomor pinjaman;
- marketing;
- resort;
- tanggal;
- status ACC;
- lokasi;
- badge Dari APK;
- tombol Detail.

Detail Anggota menampilkan:

- foto anggota;
- foto bukti transfer;
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
- lokasi;
- peta;
- koordinat;
- waktu input;
- status sinkronisasi.

Jangan membuka Detail Marketing ketika anggota diklik.

==================================================
18. STATUS ACC
==================================================

Status ACC ditampilkan sederhana:

- Belum Diproses;
- ACC;
- Ditolak.

Marketing mengirim data dengan status default:

Belum Diproses

Admin dapat mengubah status pada Detail Anggota.

Ini hanya prototype visual.

Jangan membuat workflow approval kompleks.

==================================================
19. LAPORAN OPERASIONAL HARIAN
==================================================

Ganti seluruh istilah:

Laporan Kunjungan

menjadi:

Laporan Operasional Harian

Buat:

- Daftar Laporan Harian;
- Detail Laporan Harian.

Daftar:

- marketing;
- kode;
- hari;
- tanggal;
- storting;
- drop;
- target;
- dua thumbnail foto;
- lokasi;
- waktu;
- badge Dari APK;
- status sinkronisasi;
- tombol Detail.

Detail:

- marketing;
- kode;
- jadwal;
- hari;
- tanggal;
- storting;
- drop;
- tabungan keluar;
- lain-lain;
- target lama nominal dan jumlah orang;
- target masuk nominal dan jumlah orang;
- target keluar nominal dan jumlah orang;
- jumlah target nominal dan jumlah orang;
- drop baru;
- drop lanjut;
- foto anggota;
- foto bukti transfer;
- lokasi;
- peta;
- alamat;
- koordinat;
- waktu input;
- status sinkronisasi.

Jangan tampilkan:

- Kas Bon;
- Potongan Admin;
- Potongan Tabungan;
- Asuransi pada laporan setoran;
- Jumlah Uang Tunai;
- Plus;
- Min;
- Sirkulasi pada form laporan.

==================================================
20. DUA FOTO DARI APK
==================================================

Setiap data kunjungan dapat memiliki:

1. Foto Anggota/Nasabah
2. Foto Bukti Transfer

Gunakan dua aset foto dummy lokal.

Jangan gunakan URL eksternal.

Tampilkan:

- thumbnail pada daftar;
- preview besar pada detail;
- label foto;
- waktu pengambilan;
- marketing pengirim.

Jika salah satu foto belum tersedia:
- tampilkan placeholder dark;
- teks Foto belum tersedia;
- jangan tampilkan ikon rusak.

==================================================
21. BUKTI KUNJUNGAN
==================================================

Buat:

- Daftar Bukti Kunjungan;
- Detail Bukti Kunjungan.

Daftar menampilkan:

- foto anggota;
- foto transfer;
- nama anggota;
- marketing;
- kode marketing;
- lokasi;
- tanggal;
- waktu;
- status sinkronisasi.

Detail menampilkan:

- dua foto;
- data anggota;
- data marketing;
- alamat;
- lokasi;
- peta;
- koordinat;
- waktu;
- status data;
- sumber Dari APK.

==================================================
22. TRACKING LOKASI
==================================================

Tampilkan seluruh 13 marketing.

Filter:

- hari;
- marketing;
- status;
- area;
- pencarian;
- reset;
- refresh.

Tampilkan:

- peta;
- daftar marketing;
- marker;
- jadwal;
- status tracking;
- status internet;
- lokasi terakhir;
- waktu pembaruan.

Status marker:

- hijau: aktif;
- merah: offline;
- abu-abu: belum mulai.

Saat offline tampilkan:

“Marketing sedang offline. Lokasi terakhir diperbarui pukul 14.20.”

Saat belum mulai:

“Marketing belum memulai tracking hari ini.”

Refresh:
- tampilkan loading singkat;
- tampilkan toast Data berhasil diperbarui;
- ubah waktu pembaruan dummy.

==================================================
23. RIWAYAT PERJALANAN
==================================================

Buat:

- Daftar Riwayat;
- Detail Riwayat;
- Timeline.

Detail:

- marketing;
- kode;
- hari;
- tanggal;
- waktu mulai;
- waktu selesai;
- durasi;
- jumlah kunjungan;
- peta;
- titik awal;
- perjalanan;
- kunjungan pertama;
- perjalanan berikutnya;
- titik akhir;
- foto terkait.

Mobile:
- peta di atas;
- timeline di bawah;
- satu page scroll.

Desktop:
- peta dan timeline berdampingan.

==================================================
24. DATA MARKETING
==================================================

Buat:

- Daftar Marketing;
- Tambah Marketing;
- Edit Marketing;
- Detail Marketing;
- Reset Password;
- Aktifkan;
- Nonaktifkan.

Form:

- kode;
- nama;
- username;
- nomor telepon;
- area atau resort;
- foto;
- status;
- password awal;
- jadwal.

Jangan hanya menampilkan toast.

==================================================
25. DETAIL MARKETING
==================================================

Tampilkan:

- foto;
- kode;
- nama;
- jadwal;
- status akun;
- status tracking;
- lokasi terakhir;
- waktu terakhir aktif.

Ringkasan:

- jumlah anggota;
- target;
- drop;
- storting;
- kunjungan;
- data dari APK terbaru.

Tab:

- Ringkasan;
- Jadwal;
- Tracking;
- Anggota;
- Laporan Harian;
- Bukti Kunjungan;
- Riwayat.

==================================================
26. PROFIL ADMIN
==================================================

Buat:

- profil;
- ubah password;
- logout.

Form ubah password:

- password lama;
- password baru;
- konfirmasi;
- simpan.

Logout:

- modal konfirmasi;
- Batal;
- Ya, Keluar;
- kembali ke Login.

==================================================
27. SELURUH TOMBOL HARUS BERFUNGSI
==================================================

Audit semua button.

Pastikan tombol berikut dapat diklik:

- Masuk;
- Refresh;
- setiap kartu dashboard;
- Senin–Sabtu;
- Tracking;
- Data Anggota;
- Laporan Harian;
- semua kartu marketing;
- Lihat Semua;
- Lihat Detail;
- Tambah Marketing;
- Edit Marketing;
- Reset Password;
- Aktifkan;
- Nonaktifkan;
- Tambah Jadwal;
- Edit Jadwal;
- Detail Anggota;
- Detail Laporan;
- Bukti Kunjungan;
- Riwayat;
- Ubah Password;
- Logout;
- Kembali;
- Tutup modal.

Jangan gunakan empty handler.

Setiap button harus memiliki salah satu:

- navigasi;
- modal;
- bottom sheet;
- tab change;
- loading;
- toast;
- form submission dummy.

==================================================
28. STATE YANG WAJIB ADA
==================================================

Buat state:

- loading;
- error;
- data kosong;
- pencarian kosong;
- logo gagal dimuat;
- foto belum tersedia;
- marketing offline;
- marketing belum mulai;
- GPS mati;
- data menunggu sinkronisasi;
- data dari APK baru;
- sesi login habis.

==================================================
29. DATA DUMMY KONSISTEN
==================================================

Gunakan:

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

Total:

- Total Anggota: 39
- Total Target: Rp123.500.000
- Total Drop: Rp91.000.000
- Total Storting: Rp37.700.000

Pastikan nilai tabel sesuai total kartu.

Jangan menampilkan angka yang berbeda antarhalaman.

==================================================
30. JANGAN TAMBAHKAN
==================================================

Jangan menambahkan:

- backend;
- database nyata;
- Maps API nyata;
- ekspor Excel nyata;
- ekspor PDF;
- cetak;
- audit log;
- WhatsApp otomatis;
- email otomatis;
- payroll;
- absensi wajah;
- chat;
- AI;
- grafik dekoratif;
- dashboard keuangan selain field yang diminta;
- transaksi nyata.

==================================================
31. FINAL QUALITY CHECK
==================================================

Sebelum menyelesaikan revisi, lakukan pemeriksaan seluruh project.

Periksa pada ukuran:

- 390 × 844 px;
- 430 × 932 px;
- 768 × 1024 px;
- 1366 × 768 px;
- 1440 × 900 px.

Pastikan:

- logo muncul;
- tidak ada broken image;
- tidak ada background putih besar;
- tema black–gold konsisten;
- tidak ada beberapa scrollbar;
- tidak ada horizontal overflow halaman;
- tidak ada teks terpotong;
- tidak ada modal tabel yang terlalu kecil;
- tabel mobile menggunakan tab/card;
- semua button dapat diklik;
- seluruh route dapat dibuka;
- bottom navigation tidak menutupi konten;
- sidebar tidak membuat overflow;
- data APK terlihat pada admin;
- dua foto terlihat;
- lokasi terlihat;
- tracking terlihat;
- data dummy konsisten;
- project berhasil build tanpa error.

Setelah audit, perbaiki otomatis semua masalah yang ditemukan sebelum menyelesaikan jawaban.

Jangan berhenti hanya setelah membuat komponen.
Pastikan seluruh prototype benar-benar dapat digunakan dari Login sampai Logout.