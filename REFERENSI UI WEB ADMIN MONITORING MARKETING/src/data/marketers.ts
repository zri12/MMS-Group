import type { DayName, Marketing } from "../types";

const rows: Array<[string, string, string, DayName[], string, number, number]> = [
  ["M01","Deden","Gedebage",["Senin","Selasa","Rabu","Kamis","Jumat"],"Aktif",-6.9388,107.7079],
  ["M02","Angil","Rancasari",["Senin","Rabu","Jumat"],"Offline",-6.9421,107.6981],
  ["M03","Ari","Buahbatu",["Senin","Selasa","Kamis","Sabtu"],"Aktif",-6.9502,107.6613],
  ["M04","Feri","Ujungberung",["Selasa","Rabu","Jumat","Sabtu"],"Aktif",-6.9076,107.7213],
  ["M05","Sukma","Cibiru",["Rabu","Kamis","Jumat"],"Belum Mulai",-6.9143,107.7341],
  ["M06","Sandi","Antapani",["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"],"Aktif",-6.9099,107.6793],
  ["M07","Vikri","Kiaracondong",["Senin","Rabu","Kamis","Sabtu"],"GPS Tidak Aktif",-6.9228,107.6634],
  ["M08","Farhad","Cicaheum",["Senin","Selasa","Rabu","Jumat"],"Aktif",-6.9195,107.6782],
  ["M09","Doni","Sukajadi",["Selasa","Kamis","Sabtu"],"Offline",-6.8941,107.5963],
  ["M10","Faiz","Lengkong",["Senin","Selasa","Rabu","Kamis","Jumat"],"Aktif",-6.9331,107.6257],
  ["M11","Agung","Arcamanik",["Rabu","Kamis","Jumat","Sabtu"],"Aktif",-6.9089,107.6923],
  ["M12","Faisal","Cimahi",["Senin","Rabu","Jumat"],"Belum Mulai",-6.8820,107.5434],
  ["M13","Agnes","Cileunyi",["Selasa","Kamis","Sabtu"],"Offline",-6.9526,107.7384],
];

export const marketers: Marketing[] = rows.map(([code,name,area,workDays,trackingStatus,lat,lng], index) => ({
  id: code, code, name, area, workDays,
  username: `${code.toLowerCase()}.${name.toLowerCase()}`,
  phone: `0812-1111-${String(index + 1).padStart(4, "0")}`,
  profilePhoto: `/profiles/marketing-${String(index + 1).padStart(2, "0")}.jpg`,
  accountStatus: "Aktif",
  trackingStatus: trackingStatus as Marketing["trackingStatus"],
  lastLatitude: lat,
  lastLongitude: lng,
  lastLocationAddress: `${area}, Kota Bandung`,
  lastActiveAt: `2026-07-2${index % 6}T${String(8 + index % 8).padStart(2, "0")}:20:00+07:00`,
}));
