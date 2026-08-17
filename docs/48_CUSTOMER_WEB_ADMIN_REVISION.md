# Customer Web Admin Revision

Customer revision accepted on 2026-08-15 overrides the previous web-admin dashboard scope.

- User-facing `Marketing` is now `PDL`; internal model, route, and API names remain unchanged for compatibility.
- Navigation uses `Pengaturan PDL`, `Tracking PDL`, and `Jadwal PDL`.
- Dashboard shows Drop, Storting, Sirkulasi, Target Masuk, Target Keluar, Total Target, Anggota Masuk, Anggota Keluar, and Total Anggota.
- Dashboard no longer displays the former Status Marketing section or Laporan Tunai.
- Foto Pencairan and Foto Bukti Transfer are distinct operational-report attachments.
- PDL cards link to the existing detail route. After the development account reset, the PDL list correctly renders an empty state.

Open business definition: Anggota Masuk and Anggota Keluar do not have canonical fields or approved formulas. The dashboard deliberately displays `Belum tersedia` rather than deriving a value.
