---
title: "Open Questions"
project: "MMS Marketing Monitoring System"
status: "pending-confirmation"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: false
---


# Instruksi

Item di dokumen ini belum boleh dianggap aturan bisnis final. Implementasi dapat menyediakan field dan input manual, tetapi tidak boleh membuat rumus/asumsi tersembunyi.

# Rekap operasional

1. Apa arti kolom **MG**?
2. Apa arti **L, M, K, S** pada kelompok Anggota?
3. Apa arti **S** pada kelompok Target?
4. Bagaimana rumus persentase?
5. Bagaimana menentukan Drop Lalu, Kini, dan Total?
6. Bagaimana menentukan Storting Lalu, Kini, dan Total?
7. Apa definisi Sirkulasi Lalu?
8. Apa definisi Sirkulasi Sekarang?
9. Apakah label benar **Diikuti Oleh**?
10. Apa yang diisi pada Diikuti Oleh: nama orang, jumlah orang, atau informasi lain?
11. Apa arti dan sumber nilai **Kas Pagi**?
12. Apakah tabel rekap diinput manual, dihitung otomatis, atau gabungan?

# Laporan operasional

13. Apakah satu marketing hanya boleh satu laporan per tanggal?
14. Apakah laporan yang sudah dikirim dapat diedit?
15. Siapa yang dapat mengoreksi laporan?
16. Apakah jumlah target harus selalu sama dengan formula tertentu?

# Anggota

17. Apakah status Disetujui/Ditolak dapat dibatalkan atau diubah ulang?
18. Apakah admin wajib mengisi alasan penolakan?
19. Apakah nomor anggota sudah ada saat status Menunggu atau dibuat setelah Disetujui?
20. Apakah nomor pinjaman wajib unik secara global?

# Tracking

21. Berapa interval pengambilan GPS?
22. Berapa interval upload ke server?
23. Setelah berapa menit tanpa update status dianggap Offline?
24. Berapa lama data tracking disimpan?
25. Apakah tracking wajib hanya saat jam kerja/jadwal?
26. Apakah marketing boleh start tracking tanpa jadwal?
27. Apakah admin perlu melihat live tracking mendekati realtime atau 30–60 detik cukup?

# Jadwal

28. Apakah marketing boleh mengubah status Dibatalkan?
29. Apakah satu jadwal harus selalu terkait prospek?
30. Apakah admin dapat membuat jadwal tanpa konsumen tertentu?

# File/foto

31. Apakah foto anggota wajib?
32. Apakah foto laporan kunjungan wajib?
33. Maksimum ukuran final foto?
34. Berapa lama foto disimpan?

# Deployment

35. Domain final?
36. Paket cPanel dan versi PHP?
37. Batas storage dan bandwidth?
38. Apakah hosting menyediakan cron dan terminal?
