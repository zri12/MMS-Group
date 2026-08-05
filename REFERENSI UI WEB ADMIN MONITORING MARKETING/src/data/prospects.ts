import { DAY_DATES } from "../app/constants";
import type { Prospect, ProspectStatus } from "../types";
import { marketers } from "./marketers";

const primaryProspects: Prospect[] = [
  {
    id: "P001", marketingId: "M01", name: "Ahmad Hidayat", phone: "0812-1111-2201",
    address: "Jl. Gedebage Selatan No. 21, Bandung", business: "Toko Kelontong",
    status: "Tertarik", initialVisitResult: "Bersedia menerima presentasi produk",
    notes: "Tertarik pada produk tabungan usaha.", resort: "Gedebage", day: "Senin",
    date: DAY_DATES.Senin, time: "08:30", inputDate: DAY_DATES.Senin,
    lastVisitDate: DAY_DATES.Senin, lastVisitResult: "Berhasil Bertemu",
    latitude: -6.9388, longitude: 107.7079, locationAddress: "Gedebage, Kota Bandung",
    createdAt: `${DAY_DATES.Senin}T08:30:00+07:00`, updatedAt: `${DAY_DATES.Senin}T09:10:00+07:00`,
    syncStatus: "Tersinkronisasi",
  },
  {
    id: "P002", marketingId: "M01", name: "Siti Nurjanah", phone: "0812-1111-2202",
    address: "Jl. Rancasari Raya No. 18, Bandung", business: "Warung Sembako",
    status: "Perlu Follow Up", initialVisitResult: "Meminta waktu untuk mempertimbangkan",
    notes: "Follow up produk simpanan.", resort: "Rancasari", day: "Senin",
    date: DAY_DATES.Senin, time: "13:30", inputDate: DAY_DATES.Senin,
    lastVisitDate: DAY_DATES.Senin, lastVisitResult: "Berhasil Bertemu",
    latitude: -6.9421, longitude: 107.6981, locationAddress: "Rancasari, Kota Bandung",
    createdAt: `${DAY_DATES.Senin}T13:30:00+07:00`,
    syncStatus: "Menunggu Sinkronisasi",
  },
  {
    id: "P003", marketingId: "M01", name: "Toko Berkah Jaya", phone: "0812-1111-2203",
    address: "Jl. Buahbatu No. 103, Bandung", business: "Grosir",
    status: "Baru", initialVisitResult: "Data konsumen baru dicatat",
    resort: "Buahbatu", day: "Senin", date: DAY_DATES.Senin, time: "15:00",
    inputDate: DAY_DATES.Senin, latitude: -6.9502, longitude: 107.6613,
    locationAddress: "Buahbatu, Kota Bandung", createdAt: `${DAY_DATES.Senin}T15:00:00+07:00`,
    syncStatus: "Tersinkronisasi",
  },
];

const extraNames = ["Bengkel Arjuna", "Kedai Kopi Pagi", "Depot Barokah", "Mitra Tani Jaya", "Konveksi Putri", "Kios Bintang", "Laundry Bersih", "Mebel Karya"];
const statuses: ProspectStatus[] = ["Baru", "Tertarik", "Perlu Follow Up", "Tidak Tertarik", "Selesai"];
const extraProspects: Prospect[] = extraNames.map((name, index) => {
  const marketing = marketers[index + 1];
  const day = marketing.workDays[index % marketing.workDays.length];
  return {
    id: `P${String(index + 4).padStart(3, "0")}`, marketingId: marketing.id, name,
    phone: `0813-4500-${6104 + index}`, address: `Jl. Usaha No. ${14 + index}, ${marketing.area}`,
    business: ["Bengkel", "Kuliner", "Warung", "Pertanian"][index % 4],
    status: statuses[index % statuses.length], initialVisitResult: "Pendataan awal konsumen",
    resort: marketing.area, day, date: DAY_DATES[day], time: `${String(9 + index).padStart(2, "0")}:00`,
    inputDate: DAY_DATES[day], latitude: marketing.lastLatitude, longitude: marketing.lastLongitude,
    locationAddress: marketing.lastLocationAddress, createdAt: `${DAY_DATES[day]}T10:00:00+07:00`,
    syncStatus: index === 4 ? "Gagal" : "Tersinkronisasi",
    linkedMemberId: index === 4 ? "A008" : undefined,
  };
});

export const prospects: Prospect[] = [...primaryProspects, ...extraProspects];
