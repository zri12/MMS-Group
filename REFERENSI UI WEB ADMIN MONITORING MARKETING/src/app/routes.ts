export const MAIN_ROUTES = ["Dashboard", "MarketingList", "DailyHub", "TrackingMap", "Profil"] as const;

export const PAGE_LABELS = {
  Dashboard:"Dashboard", MarketingList:"Data Marketing", DailyHub:"Data Harian",
  MarketingDetail:"Detail Marketing", MarketingForm:"Tambah/Edit Marketing",
  RekapOperasional:"Rekap Operasional", RekapAnggota:"Detail Total Anggota",
  RekapTarget:"Detail Total Target", RekapDrop:"Detail Total Drop", RekapStorting:"Detail Total Storting",
  AnggotaList:"Data Anggota", AnggotaDetail:"Detail Anggota",
  ProspectList:"Data Prospek", ProspectDetail:"Detail Prospek",
  VisitReportList:"Laporan Kunjungan", VisitReportDetail:"Detail Laporan Kunjungan",
  LaporanHarianList:"Laporan Operasional Harian", LaporanHarianDetail:"Detail Laporan Harian",
  TrackingMap:"Tracking Lokasi", TrackingDetail:"Detail Tracking",
  RiwayatList:"Riwayat Perjalanan", RiwayatDetail:"Detail Riwayat",
  JadwalList:"Jadwal Marketing", JadwalForm:"Tambah/Edit Jadwal",
  Profil:"Profil Admin", UbahPassword:"Ubah Password", Login:"Login",
} as const;
