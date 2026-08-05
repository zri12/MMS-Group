import type { VisitReport, VisitResult } from "../types";
import { prospects } from "./prospects";

const results: VisitResult[] = ["Berhasil Bertemu", "Tidak Bertemu", "Transaksi Selesai"];

export const visitReports: VisitReport[] = Array.from({ length: 15 }, (_, index) => {
  const prospect = prospects[index % prospects.length];
  const visitResult = results[index % results.length];
  return {
    id: `LK${String(index + 1).padStart(3, "0")}`,
    prospectId: prospect.id,
    marketingId: prospect.marketingId,
    day: prospect.day,
    date: prospect.date,
    time: `${String(9 + (index % 7)).padStart(2, "0")}:${index % 2 ? "30" : "00"}`,
    visitPurpose: ["Perkenalan produk", "Follow up pengajuan", "Verifikasi usaha"][index % 3],
    visitResult,
    prospectStatus: visitResult === "Transaksi Selesai" ? "Selesai" : prospect.status,
    followUpDate: prospect.status === "Perlu Follow Up" ? "2026-07-28" : undefined,
    notes: visitResult === "Tidak Bertemu" ? "Lokasi tutup, perlu kunjungan ulang." : "Kunjungan berjalan baik.",
    photo: `/profiles/member-${String((index % 16) + 1).padStart(2, "0")}.jpg`,
    photoCaption: "Dokumentasi lokasi kunjungan",
    resort: prospect.resort,
    latitude: prospect.latitude,
    longitude: prospect.longitude,
    locationAddress: prospect.locationAddress,
    createdAt: `${prospect.date}T${String(9 + (index % 7)).padStart(2, "0")}:30:00+07:00`,
    syncStatus: index % 6 === 0 ? "Menunggu Sinkronisasi" : "Tersinkronisasi",
  };
});
