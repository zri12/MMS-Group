import { DAY_DATES } from "../app/constants";
import type { MarketingSchedule } from "../types";
import { marketers } from "./marketers";

const dedenMonday: MarketingSchedule[] = [
  {
    id:"J-M01-SENIN-01", marketingId:"M01", day:"Senin", date:DAY_DATES.Senin,
    startTime:"08:30", endTime:"09:30", consumerName:"Ahmad Hidayat",
    agenda:"Presentasi produk tabungan", area:"Gedebage", resort:"Gedebage",
    destination:"Ahmad Hidayat · Gedebage", status:"Selesai",
  },
  {
    id:"J-M01-SENIN-02", marketingId:"M01", day:"Senin", date:DAY_DATES.Senin,
    startTime:"10:00", endTime:"11:30", consumerName:"Herman Malik",
    agenda:"Survei pengajuan anggota", area:"Buahbatu", resort:"Buahbatu",
    destination:"Herman Malik · Buahbatu", status:"Berlangsung",
  },
  {
    id:"J-M01-SENIN-03", marketingId:"M01", day:"Senin", date:DAY_DATES.Senin,
    startTime:"13:30", endTime:"14:30", consumerName:"Siti Nurjanah",
    agenda:"Follow up produk simpanan", area:"Rancasari", resort:"Rancasari",
    destination:"Siti Nurjanah · Rancasari", status:"Belum Dikunjungi",
  },
];

const genericSchedules: MarketingSchedule[] = marketers.flatMap((marketing, marketingIndex) =>
  marketing.workDays
    .filter(day => !(marketing.id === "M01" && day === "Senin"))
    .map((day, dayIndex) => ({
      id:`J-${marketing.code}-${day}`, marketingId:marketing.id, day, date:DAY_DATES[day],
      startTime:["08:00","08:30","09:00"][dayIndex % 3], endTime:["15:00","16:00","16:30"][dayIndex % 3],
      consumerName:["Konsumen wilayah","Anggota binaan","Prospek baru"][dayIndex % 3],
      agenda:["Kunjungan rutin","Survei anggota","Pengenalan produk"][dayIndex % 3],
      area:marketing.area, resort:marketing.area,
      destination:`Kunjungan wilayah ${marketing.area}`,
      note:"Bawa berkas operasional",
      status:(["Selesai","Berlangsung","Belum Dikunjungi","Dibatalkan"] as const)[(marketingIndex + dayIndex) % 4],
    })),
);

export const schedules: MarketingSchedule[] = [...dedenMonday, ...genericSchedules];
