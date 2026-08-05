Lanjutkan project UI “MMS Marketing” yang sudah ada.

Project ini merupakan front-end dan prototype UI. Jangan membuat ulang project dari awal, jangan menghapus halaman, komponen, route, aset, data dummy, atau desain yang sudah dibuat.

Tujuan perubahan ini adalah membuat dua versi tampilan dalam satu project:

1. BASIC VERSION
Versi yang saat ini ditampilkan kepada customer dan sesuai dengan anggaran Rp2.000.000.

2. FULL VERSION
Versi lengkap yang seluruh UI-nya tetap disimpan di project, tetapi sementara tidak ditampilkan kepada customer.

Fitur versi lengkap tidak boleh dihapus. Fitur hanya disembunyikan menggunakan sistem feature flag agar suatu saat dapat dimunculkan kembali tanpa membuat ulang desain.

==================================================
1. ATURAN UTAMA
==================================================

Jangan lakukan hal berikut:

- jangan menghapus file halaman;
- jangan menghapus komponen;
- jangan menghapus route;
- jangan menghapus desain UI;
- jangan menghapus aset gambar;
- jangan menghapus ikon;
- jangan menghapus data dummy;
- jangan menghapus modal;
- jangan menghapus state prototype;
- jangan membuat ulang project;
- jangan mengubah tema visual;
- jangan mengubah tampilan utama yang sudah final;
- jangan memindahkan semua kode ke satu file;
- jangan merusak navigasi yang sudah berjalan.

Semua fitur lengkap tetap harus tersimpan dalam struktur project.

Fitur yang tidak termasuk versi basic hanya:
- disembunyikan dari menu;
- disembunyikan dari tombol;
- disembunyikan dari bottom navigation;
- disembunyikan dari kartu dashboard;
- tidak dapat dibuka melalui alur prototype customer;
- tetapi file halaman, route, komponen, dan desainnya tetap tersedia.

==================================================
2. BUAT FEATURE FLAG TERPUSAT
==================================================

Buat satu konfigurasi terpusat, misalnya:

src/config/featureFlags.ts

Gunakan struktur seperti berikut:

export const featureFlags = {
  fullVersion: false,

  mobile: {
    separatePermissionOnboarding: false,
    editSubmittedCustomer: false,
    multipleReportPhotos: false,
    reportDraft: false,
    followUpSchedule: false,
    detailedSyncStatus: false,
    mobileJourneyMap: false,
    accountInformation: false,
    userGuide: false,
    aboutApplication: false,
    changePassword: false,
    contactCustomer: false,
    internalNotification: false,
    advancedCustomerStatus: false,
    advancedVisitResult: false,
    batteryStatistics: false,
    detailedGpsStatistics: false
  },

  admin: {
    resortManagementPage: false,
    resortDetailPage: false,
    multipleAdminRoles: false,
    advancedCharts: false,
    resortRanking: false,
    marketingRanking: false,
    detailedActivityLog: false,
    exportPdf: false,
    exportExcel: false,
    reportApproval: false,
    whatsappNotification: false,
    emailNotification: false,
    advancedRolePermission: false,
    auditLog: false,
    systemSettings: false,
    complexAdminProfile: false,
    marketingPerformanceAnalytics: false,
    speedAnalytics: false,
    distanceAnalytics: false,
    advancedRouteAnalytics: false,
    automaticRealtimeRefresh: false
  }
};

Pastikan semua menu, tombol, card, dan route fitur tambahan membaca nilai dari feature flag tersebut.

Ketika nilainya false:
- fitur tidak tampil pada prototype customer;
- route tetap tersimpan;
- komponen tetap tersimpan;
- UI tetap dapat dikembangkan kembali.

Ketika nilainya true:
- fitur kembali tampil;
- menu kembali muncul;
- tombol kembali dapat digunakan;
- route dapat dibuka;
- tidak perlu membuat ulang UI.

Jangan membuat tombol feature flag di tampilan customer.

==================================================
3. BUAT MODE BASIC SEBAGAI DEFAULT
==================================================

Gunakan:

fullVersion: false

sebagai kondisi default.

Saat fullVersion false:
- tampilkan hanya fitur basic;
- sembunyikan fitur tambahan;
- jangan tampilkan label “fitur dikunci”;
- jangan tampilkan label “fitur premium”;
- jangan tampilkan menu yang kosong;
- jangan menampilkan tombol yang tidak memiliki alur;
- jangan memberi tahu customer bahwa ada halaman tersembunyi.

Saat fullVersion true:
- tampilkan seluruh fitur lengkap yang sebelumnya sudah dibuat.

Tambahkan komentar sederhana pada file konfigurasi:

// Ubah ke true ketika customer melakukan upgrade ke versi lengkap.

==================================================
4. FITUR APK YANG TETAP DITAMPILKAN
==================================================

Untuk BASIC VERSION aplikasi marketing, tampilkan fitur berikut:

1. Splash screen.
2. Login marketing.
3. Penjelasan dan permintaan izin lokasi secara sederhana.
4. Dashboard marketing.
5. Status tracking aktif, offline, atau GPS tidak aktif.
6. Data konsumen.
7. Tambah data konsumen.
8. Detail konsumen sederhana.
9. Buat laporan kunjungan.
10. Maksimal satu foto dalam satu laporan.
11. Catatan hasil kunjungan.
12. Status konsumen sederhana.
13. Riwayat laporan sederhana.
14. Riwayat perjalanan sederhana.
15. Profil marketing.
16. Logout.
17. Tampilan contoh kondisi offline.
18. Tampilan contoh laporan berhasil dan menunggu sinkronisasi.

Gunakan bottom navigation versi basic:

- Beranda
- Konsumen
- Laporan
- Riwayat
- Profil

Pertahankan tampilan visual yang sudah final:
- hitam;
- putih;
- abu-abu muda;
- emas;
- font yang sekarang;
- kartu;
- ikon;
- bottom navigation;
- peta dummy;
- foto dummy;
- layout mobile.

==================================================
5. FITUR APK YANG DISEMBUNYIKAN
==================================================

Sembunyikan fitur berikut dari BASIC VERSION, tetapi jangan menghapus file atau UI-nya:

A. Onboarding izin terpisah yang terlalu panjang

Sembunyikan halaman izin yang terlalu banyak sebagai alur terpisah.

Untuk basic version, gunakan satu halaman penjelasan izin yang ringkas.

Tetap simpan halaman lengkap berikut:
- izin lokasi;
- izin lokasi background;
- izin notifikasi;
- izin kamera.

Hubungkan kembali halaman lengkap tersebut ketika:

featureFlags.mobile.separatePermissionOnboarding === true

B. Edit konsumen setelah laporan dikirim

Sembunyikan tombol:
- Edit Konsumen;
- Ubah Data Konsumen;

pada data konsumen yang sudah memiliki laporan terkirim.

Tetap simpan halaman edit konsumen.

Tampilkan kembali ketika:

featureFlags.mobile.editSubmittedCustomer === true

C. Beberapa foto laporan

Basic version hanya menampilkan satu foto per laporan.

Sembunyikan:
- tambah foto lainnya;
- galeri beberapa foto;
- jumlah foto;
- pengurutan foto.

Tetap simpan komponen multi-photo.

Tampilkan kembali ketika:

featureFlags.mobile.multipleReportPhotos === true

D. Draft laporan

Sembunyikan:
- tombol Simpan sebagai Draft;
- tab Draft;
- daftar laporan draft;
- halaman melanjutkan draft.

Tetap simpan semua komponen dan halaman draft.

Tampilkan kembali ketika:

featureFlags.mobile.reportDraft === true

E. Jadwal follow-up

Sembunyikan:
- rencana tindak lanjut;
- tanggal follow-up;
- date picker follow-up;
- daftar jadwal tindak lanjut.

Tetap simpan field dan komponennya.

Tampilkan kembali ketika:

featureFlags.mobile.followUpSchedule === true

F. Status sinkronisasi detail

Untuk basic version, cukup tampilkan:
- status online atau offline;
- jumlah data belum terkirim;
- tombol Coba Sinkronkan.

Sembunyikan:
- jumlah titik GPS secara rinci;
- progress per data;
- daftar antrean per item;
- jumlah foto menunggu;
- waktu sinkronisasi per item;
- statistik sinkronisasi.

Tetap simpan halaman lengkap Status Sinkronisasi.

Tampilkan kembali ketika:

featureFlags.mobile.detailedSyncStatus === true

G. Peta rute lengkap pada APK

Pada basic version, riwayat perjalanan cukup menampilkan:
- tanggal;
- waktu;
- durasi;
- status;
- jumlah kunjungan.

Sembunyikan peta rute lengkap pada aplikasi marketing.

Tetap simpan halaman peta dan detail rute.

Tampilkan kembali ketika:

featureFlags.mobile.mobileJourneyMap === true

H. Menu profil tambahan

Pada basic version, menu Profil hanya menampilkan:
- nama marketing;
- resort;
- status akun;
- logout.

Sembunyikan menu:
- Informasi Akun;
- Panduan Penggunaan;
- Tentang Aplikasi;
- Ubah Password;
- Status Izin Aplikasi yang terlalu detail.

Jangan menghapus halaman tersebut.

Tampilkan kembali berdasarkan feature flag:

accountInformation
userGuide
aboutApplication
changePassword

I. Hubungi konsumen

Sembunyikan tombol:
- Hubungi Konsumen;
- Telepon Konsumen;
- Salin Nomor.

Tetap simpan modal atau komponennya.

Tampilkan kembali ketika:

featureFlags.mobile.contactCustomer === true

J. Notifikasi internal

Sembunyikan:
- ikon lonceng;
- daftar notifikasi internal;
- halaman notifikasi;
- badge notifikasi.

Notifikasi background tracking tidak perlu divisualisasikan sebagai menu.

Tampilkan kembali ketika:

featureFlags.mobile.internalNotification === true

K. Status konsumen yang terlalu banyak

Basic version hanya menggunakan:

- Baru
- Tertarik
- Follow Up
- Selesai

Sembunyikan status tambahan, tetapi jangan menghapus opsinya.

Tampilkan seluruh status kembali ketika:

featureFlags.mobile.advancedCustomerStatus === true

L. Hasil kunjungan yang terlalu banyak

Basic version hanya menggunakan:

- Berhasil Bertemu
- Tidak Bertemu
- Tertarik
- Tidak Tertarik

Sembunyikan pilihan tambahan.

Tampilkan kembali ketika:

featureFlags.mobile.advancedVisitResult === true

M. Statistik teknis tracking

Sembunyikan dari basic version:
- estimasi penggunaan baterai;
- jumlah titik GPS secara detail;
- interval pencatatan lokasi;
- statistik teknis perangkat;
- informasi teknis sinkronisasi.

Tetap simpan komponennya.

Gunakan feature flag:

batteryStatistics
detailedGpsStatistics

==================================================
6. ALUR APK BASIC VERSION
==================================================

Gunakan alur prototype sederhana:

Splash
→ Login
→ Penjelasan Izin Lokasi
→ Dashboard
→ Tambah Konsumen
→ Simpan Konsumen
→ Buat Laporan
→ Ambil Satu Foto
→ Konfirmasi
→ Laporan Berhasil atau Menunggu Sinkronisasi
→ Riwayat
→ Profil
→ Logout

Jangan menampilkan alur tambahan dalam prototype basic.

Halaman tambahan tetap tersimpan tetapi tidak masuk ke alur customer.

==================================================
7. DASHBOARD ADMIN BASIC VERSION
==================================================

Jika project ini sudah memiliki halaman dashboard admin, gunakan aturan berikut.

Jika dashboard admin belum ada, jangan membuatnya dalam revisi project aplikasi mobile ini. Bagian admin hanya digunakan sebagai pedoman feature flag saat dashboard admin dibuat pada project terpisah.

Fitur dashboard admin yang ditampilkan pada BASIC VERSION:

1. Login satu admin utama.
2. Dashboard ringkasan.
3. Total marketing.
4. Marketing aktif.
5. Marketing offline.
6. Jumlah konsumen.
7. Jumlah laporan.
8. Monitoring posisi terakhir marketing.
9. Filter resort.
10. Filter marketing.
11. Data marketing.
12. Tambah akun marketing.
13. Edit data akun marketing.
14. Aktifkan atau nonaktifkan akun.
15. Reset password marketing.
16. Riwayat perjalanan sederhana.
17. Data konsumen.
18. Laporan kunjungan.
19. Detail laporan dan satu foto.
20. Logout admin.

==================================================
8. FITUR DASHBOARD ADMIN YANG DISEMBUNYIKAN
==================================================

Sembunyikan dari BASIC VERSION, tetapi jangan menghapus UI-nya:

A. Halaman Data Resort dan Detail Resort

Resort cukup digunakan sebagai:
- label pada marketing;
- pilihan filter;
- informasi pada laporan.

Sembunyikan halaman khusus:
- Daftar Resort;
- Detail Resort;
- Edit Resort;
- Statistik Resort.

Tampilkan kembali ketika:

featureFlags.admin.resortManagementPage === true
atau
featureFlags.admin.resortDetailPage === true

B. Banyak admin dan pembagian hak akses

Basic version hanya memiliki satu admin utama.

Sembunyikan:
- admin per resort;
- supervisor;
- role manager;
- role viewer;
- konfigurasi permission.

Tampilkan kembali ketika:

featureFlags.admin.multipleAdminRoles === true
atau
featureFlags.admin.advancedRolePermission === true

C. Grafik kompleks

Sembunyikan:
- grafik aktivitas harian;
- grafik aktivitas mingguan;
- grafik performa;
- grafik perbandingan resort;
- grafik pertumbuhan konsumen;
- grafik tren laporan.

Pertahankan kartu ringkasan sederhana.

Tampilkan grafik kembali ketika:

featureFlags.admin.advancedCharts === true

D. Ranking

Sembunyikan:
- ranking resort;
- ranking marketing;
- marketing terbaik;
- resort paling aktif.

Tampilkan kembali ketika:

featureFlags.admin.resortRanking === true
atau
featureFlags.admin.marketingRanking === true

E. Aktivitas terbaru yang sangat detail

Pada basic version, tampilkan maksimal lima aktivitas sederhana.

Sembunyikan:
- histori aktivitas lengkap;
- filter aktivitas;
- log login;
- log perubahan akun;
- aktivitas sistem.

Tampilkan kembali ketika:

featureFlags.admin.detailedActivityLog === true

F. Ekspor laporan

Sembunyikan:
- Ekspor PDF;
- Ekspor Excel;
- Download laporan;
- Cetak laporan.

Tampilkan kembali ketika:

featureFlags.admin.exportPdf === true
atau
featureFlags.admin.exportExcel === true

G. Approval laporan

Admin basic hanya melihat laporan.

Sembunyikan:
- Setujui Laporan;
- Tolak Laporan;
- Minta Revisi;
- Status Menunggu Persetujuan.

Tampilkan kembali ketika:

featureFlags.admin.reportApproval === true

H. Notifikasi otomatis

Sembunyikan:
- WhatsApp otomatis;
- email otomatis;
- reminder otomatis;
- notifikasi laporan baru;
- notifikasi marketing offline.

Tampilkan kembali ketika:

featureFlags.admin.whatsappNotification === true
atau
featureFlags.admin.emailNotification === true

I. Audit log

Sembunyikan:
- log perubahan data;
- log login;
- log penghapusan;
- log aktivitas admin;
- riwayat keamanan.

Tampilkan kembali ketika:

featureFlags.admin.auditLog === true

J. Pengaturan sistem

Sembunyikan:
- konfigurasi interval tracking;
- pengaturan server;
- pengaturan peta;
- pengaturan penyimpanan;
- pengaturan aplikasi;
- konfigurasi notifikasi.

Tampilkan kembali ketika:

featureFlags.admin.systemSettings === true

K. Profil admin kompleks

Basic version hanya menampilkan:
- nama admin;
- username;
- logout.

Sembunyikan:
- foto profil;
- ubah informasi akun;
- riwayat login;
- preferensi dashboard;
- pengaturan pribadi.

Tampilkan kembali ketika:

featureFlags.admin.complexAdminProfile === true

L. Analisis performa marketing

Sembunyikan:
- total jarak;
- kecepatan rata-rata;
- waktu berhenti;
- jumlah titik GPS;
- durasi produktif;
- performa marketing;
- perbandingan marketing;
- analisis rute.

Tampilkan kembali berdasarkan feature flag:

marketingPerformanceAnalytics
speedAnalytics
distanceAnalytics
advancedRouteAnalytics

M. Refresh posisi terlalu sering

Untuk basic version:
- gunakan tombol Refresh;
- tampilkan waktu pembaruan terakhir;
- gunakan konsep refresh berkala sederhana.

Sembunyikan simulasi update setiap beberapa detik.

Tampilkan kembali ketika:

featureFlags.admin.automaticRealtimeRefresh === true

==================================================
9. PERTAHANKAN FULL UI
==================================================

Semua halaman versi lengkap harus tetap tersedia pada struktur project.

Kelompokkan route agar mudah dikelola, misalnya:

Basic routes:
- /login
- /dashboard
- /customers
- /reports
- /journeys
- /profile

Full routes:
- /sync-detail
- /report-drafts
- /journey-map
- /account-information
- /user-guide
- /about
- /change-password
- /advanced-customer
- /advanced-report

Jangan menghapus full routes.

Ketika feature flag false:
- route tidak ditampilkan melalui navigasi customer;
- jangan arahkan pengguna ke route tersebut;
- tetap simpan halaman dan komponennya.

Ketika feature flag true:
- menu dan route dapat digunakan kembali.

==================================================
10. BUAT DOKUMENTASI INTERNAL SINGKAT
==================================================

Tambahkan file:

FEATURE_FLAGS.md

Isi dokumentasi:

- tujuan feature flag;
- lokasi file konfigurasi;
- daftar fitur basic;
- daftar fitur full;
- cara menampilkan fitur;
- cara menyembunyikan fitur.

Berikan contoh:

Untuk menampilkan kembali fitur Draft Laporan:

reportDraft: true

Untuk menampilkan kembali peta rute di aplikasi:

mobileJourneyMap: true

Untuk menampilkan grafik admin:

advancedCharts: true

Untuk menampilkan seluruh fitur:

fullVersion: true

Dokumentasi tersebut hanya untuk developer dan tidak tampil pada prototype customer.

==================================================
11. JANGAN TAMPILKAN MODE DEVELOPER KE CUSTOMER
==================================================

Jangan membuat:
- tombol Basic/Full;
- menu Developer;
- halaman Feature Flag;
- pengaturan versi;
- badge Basic Version;
- badge Full Version;
- tombol Unlock Feature;
- tulisan Upgrade;
- menu tersembunyi yang masih dapat dilihat customer.

Feature flag hanya berada di kode dan konfigurasi internal.

==================================================
12. HASIL AKHIR
==================================================

Hasil akhir harus memenuhi ketentuan:

- tampilan customer menggunakan BASIC VERSION;
- seluruh UI versi lengkap tetap tersimpan;
- tidak ada file atau halaman yang dihapus;
- menu basic tetap rapi;
- tidak ada tombol kosong;
- fitur tersembunyi tidak muncul pada prototype customer;
- full UI dapat dimunculkan kembali dengan mengubah feature flag;
- desain visual tidak berubah;
- project tetap berhasil dijalankan;
- tidak ada error build;
- tidak ada halaman duplikat yang tidak diperlukan;
- kode dan komponen tetap rapi;
- jangan membuat ulang project dari awal.

Setelah selesai, pastikan nilai default:

fullVersion: false

agar link prototype yang dibagikan kepada customer hanya menampilkan versi basic.