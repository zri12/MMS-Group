export const statusTone = (status: string) => {
  if (["Disetujui", "Aktif", "Selesai", "Tersinkronisasi", "Transaksi Selesai"].includes(status)) return "success";
  if (["Ditolak", "Gagal", "Tidak Tertarik"].includes(status)) return "danger";
  if (["Menunggu", "Menunggu Sinkronisasi", "Perlu Follow Up", "Belum Mulai", "Belum Dikunjungi"].includes(status)) return "warning";
  return "neutral";
};
