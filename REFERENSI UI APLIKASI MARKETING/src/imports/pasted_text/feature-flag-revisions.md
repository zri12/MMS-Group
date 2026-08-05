Lanjutkan revisi project UI “MMS Marketing” yang sudah ada.

Jangan membuat ulang project. Jangan mengubah tema, warna, layout utama, komponen, aset, data dummy, route, atau desain yang sudah dibuat.

Project ini hanya berupa front-end prototype. Fokus revisi hanya menyempurnakan penerapan feature flag agar Basic Version benar-benar menampilkan fitur sesuai paket Rp2.000.000, sedangkan seluruh UI Full Version tetap tersimpan dan dapat dimunculkan kembali.

Pastikan project tetap berhasil di-build tanpa error.

==================================================
1. PERTAHANKAN FEATURE FLAG YANG SUDAH ADA
==================================================

Pertahankan file:

src/config/featureFlags.ts
FEATURE_FLAGS.md

Pertahankan nilai default:

fullVersion: false

Jangan menghapus flag, route, halaman, komponen, aset, dan data dummy versi lengkap.

Ketika fitur bernilai false:
- menu tidak tampil;
- tombol tidak tampil;
- card tidak tampil;
- data terkait tidak tampil pada prototype basic;
- route dan komponen tetap tersimpan.

Ketika fitur bernilai true atau fullVersion bernilai true:
- fitur kembali tampil;
- tombol kembali tersedia;
- halaman dapat dibuka;
- data lengkap kembali ditampilkan.

==================================================
2. PERBAIKI MENU CEPAT DASHBOARD
==================================================

Saat detailedSyncStatus bernilai false, menu cepat hanya berisi:

- Tambah Konsumen
- Buat Laporan
- Lihat Riwayat

Gunakan layout grid tiga kolom agar ketiga menu memenuhi lebar halaman secara seimbang.

Saat detailedSyncStatus bernilai true atau fullVersion bernilai true, tampilkan:

- Tambah Konsumen
- Buat Laporan
- Lihat Riwayat
- Status Sinkronisasi

dan ubah layout menjadi empat kolom.

Jangan menyisakan kolom kosong.

Gunakan kondisi layout berdasarkan jumlah menu, bukan grid-cols-4 secara tetap.

==================================================
3. SEMBUNYIKAN LAPORAN DRAFT DARI BASIC VERSION
==================================================

Saat:

featureFlags.mobile.reportDraft === false

lakukan hal berikut:

- sembunyikan tab Draft;
- sembunyikan tombol Simpan sebagai Draft;
- sembunyikan laporan berstatus Draft dari tab Semua;
- jangan menampilkan badge Draft;
- jangan menampilkan detail laporan Draft melalui alur prototype basic.

Data dummy laporan Draft tidak boleh dihapus.

Saat:

featureFlags.mobile.reportDraft === true

atau:

fullVersion === true

tampilkan kembali:
- tab Draft;
- laporan Draft pada tab Semua;
- tombol Simpan sebagai Draft;
- detail Draft;
- alur melanjutkan Draft.

==================================================
4. TERAPKAN FLAG MOBILE JOURNEY MAP
==================================================

Gunakan:

featureFlags.mobile.mobileJourneyMap

Saat false, halaman Riwayat Basic tetap menampilkan:
- tanggal perjalanan;
- waktu mulai;
- waktu selesai;
- durasi;
- status;
- jumlah kunjungan;
- status sinkronisasi sederhana.

Sembunyikan dari Basic Version:
- peta rute;
- visual garis perjalanan;
- titik awal;
- titik akhir;
- titik kunjungan pada peta;
- detail jalur;
- analisis rute.

Jangan menghapus komponen peta.

Saat mobileJourneyMap bernilai true atau fullVersion bernilai true, tampilkan kembali seluruh peta dan informasi rute lengkap.

==================================================
5. TERAPKAN FLAG DETAILED GPS STATISTICS
==================================================

Gunakan:

featureFlags.mobile.detailedGpsStatistics

Saat false, sembunyikan:
- jumlah titik GPS;
- jumlah titik belum tersinkron;
- statistik titik per perjalanan;
- detail interval pencatatan;
- rincian GPS teknis;
- kartu statistik teknis tracking.

Pada Basic Version, Detail Tracking cukup menampilkan:
- Tracking Aktif atau Tidak Aktif;
- GPS Aktif atau Tidak Aktif;
- Internet Tersambung atau Terputus;
- waktu tracking dimulai;
- lokasi terakhir;
- waktu terakhir diperbarui;
- tombol Perbarui Lokasi.

Saat detailedGpsStatistics bernilai true atau fullVersion bernilai true, tampilkan kembali seluruh informasi titik GPS dan statistik rinci.

==================================================
6. TERAPKAN FLAG BATTERY STATISTICS
==================================================

Gunakan:

featureFlags.mobile.batteryStatistics

Saat false:
- sembunyikan kartu Penggunaan Baterai;
- jangan menampilkan statistik atau keterangan baterai pada Detail Tracking.

Jangan menghapus komponennya.

Saat batteryStatistics bernilai true atau fullVersion bernilai true, tampilkan kembali kartu Penggunaan Baterai.

==================================================
7. TERAPKAN FLAG EDIT SUBMITTED CUSTOMER
==================================================

Gunakan:

featureFlags.mobile.editSubmittedCustomer

Saat false:
- sembunyikan ikon Edit pada header detail konsumen;
- sembunyikan tombol Edit Data;
- jangan arahkan pengguna ke halaman Edit Konsumen melalui prototype basic.

Halaman Edit Konsumen, route, form, dan data dummy tetap disimpan.

Saat editSubmittedCustomer bernilai true atau fullVersion bernilai true:
- tampilkan kembali ikon Edit;
- tampilkan tombol Edit Data;
- halaman Edit Konsumen dapat dibuka kembali.

==================================================
8. TERAPKAN FLAG MULTIPLE REPORT PHOTOS
==================================================

Basic Version hanya menggunakan satu foto laporan.

Saat:

featureFlags.mobile.multipleReportPhotos === false

tampilkan:
- satu area foto;
- Ambil Foto;
- Pilih dari Galeri;
- Ganti Foto;
- Hapus Foto;
- satu keterangan foto.

Sembunyikan:
- Tambah Foto Lain;
- jumlah beberapa foto;
- daftar galeri beberapa foto;
- pengurutan foto;
- hapus per foto dalam galeri.

Jangan menghapus komponen Full Version jika sudah tersedia.

Saat multipleReportPhotos bernilai true atau fullVersion bernilai true, tampilkan kembali UI beberapa foto.

==================================================
9. KONSISTENSIKAN STATUS KONSUMEN
==================================================

Gunakan satu konstanta status yang sama pada:
- Tambah Konsumen;
- Edit Konsumen;
- Buat Laporan;
- Detail Konsumen;
- Filter Konsumen.

Saat advancedCustomerStatus bernilai false, gunakan:

- Baru
- Tertarik
- Follow Up
- Selesai

Saat advancedCustomerStatus bernilai true atau fullVersion bernilai true, gunakan:

- Baru
- Tertarik
- Perlu Follow Up
- Tidak Tertarik
- Selesai

Jangan menulis daftar status secara hardcoded terpisah pada form laporan.

==================================================
10. PERBAIKI RINGKASAN LAPORAN
==================================================

Pada halaman Laporan Kunjungan, hapus tanda petik dari teks:

"1 laporan menunggu sinkronisasi"

Ubah menjadi:

1 laporan menunggu sinkronisasi

Tampilan akhirnya:

2 laporan terkirim
1 laporan menunggu sinkronisasi

Jangan gunakan tanda petik pada teks UI.

==================================================
11. SEMBUNYIKAN INFORMASI TEKNIS BASIC VERSION
==================================================

Saat detailedGpsStatistics, batteryStatistics, dan detailedSyncStatus bernilai false, jangan tampilkan:

- 247 titik GPS;
- 892 titik GPS;
- 756 titik GPS;
- jumlah antrean per item;
- jumlah foto sinkronisasi;
- progress sinkronisasi rinci;
- statistik baterai;
- interval GPS;
- detail teknis server.

Data dummy dan komponen lengkap tetap tersimpan untuk Full Version.

Basic Version cukup menampilkan status sederhana:
- Tersinkron;
- Menunggu Sinkronisasi;
- Offline;
- GPS Aktif;
- GPS Tidak Aktif.

==================================================
12. PERBAIKI TEKS VERSI APLIKASI
==================================================

Pada halaman Profil, ganti:

MMS Marketing v1.0.0-prototype

menjadi:

MMS Marketing v1.0.0

Jangan menampilkan kata:
- prototype;
- Basic Version;
- Full Version;
- fitur tersembunyi;
- upgrade;
- premium.

Customer tidak perlu mengetahui sistem feature flag.

==================================================
13. PASTIKAN FULL VERSION DAPAT DIKEMBALIKAN
==================================================

Pastikan ketika:

fullVersion: true

seluruh fitur berikut kembali tampil:

- onboarding izin lengkap;
- edit konsumen;
- beberapa foto laporan;
- Draft laporan;
- jadwal follow-up;
- status sinkronisasi detail;
- peta rute APK;
- informasi akun;
- panduan;
- tentang aplikasi;
- ubah password;
- hubungi konsumen;
- notifikasi internal jika tersedia;
- status konsumen lengkap;
- hasil kunjungan lengkap;
- statistik baterai;
- statistik GPS detail.

Jangan hanya mengaktifkan sebagian fitur.

Gunakan helper:

isFeatureEnabled(featureFlags.mobile.namaFitur)

secara konsisten pada semua komponen yang terkait.

==================================================
14. CEK SELURUH FLAG YANG BELUM DIGUNAKAN
==================================================

Pastikan flag berikut benar-benar terhubung ke UI:

- editSubmittedCustomer
- multipleReportPhotos
- mobileJourneyMap
- internalNotification
- batteryStatistics
- detailedGpsStatistics

Jangan membiarkan flag hanya tercantum di konfigurasi tanpa dipakai pada komponen.

Untuk internalNotification:
- saat false, ikon, badge, daftar, dan halaman notifikasi tidak tampil;
- saat true atau fullVersion true, tampilkan kembali apabila UI notifikasi sudah tersedia;
- jika UI notifikasi belum tersedia, jangan membuat fitur baru dalam revisi ini.

==================================================
15. HASIL AKHIR
==================================================

Hasil akhir harus:

- menggunakan Basic Version sebagai default;
- tidak menghapus UI Full Version;
- tidak menyisakan kolom kosong;
- tidak menampilkan laporan Draft;
- tidak menampilkan peta rute APK;
- tidak menampilkan statistik GPS rinci;
- tidak menampilkan statistik baterai;
- tidak menampilkan tombol Edit Konsumen;
- menggunakan status konsumen basic secara konsisten;
- tidak menampilkan tulisan prototype;
- tidak menampilkan menu developer;
- tetap mempertahankan desain hitam, putih, abu-abu, dan emas;
- tetap mempertahankan bottom navigation;
- tetap mempertahankan seluruh route dan halaman full;
- dapat mengembalikan seluruh fitur melalui fullVersion: true;
- berhasil dijalankan tanpa error build.

Jangan membuat ulang desain dari awal dan jangan menambahkan dashboard admin pada project mobile ini.