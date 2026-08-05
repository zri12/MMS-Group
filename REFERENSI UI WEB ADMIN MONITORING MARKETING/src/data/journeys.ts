import type { Journey } from "../types";
import { schedules } from "./schedules";
import { marketers } from "./marketers";

export const journeys: Journey[] = schedules.map((schedule, index) => {
  const marketing = marketers.find(item => item.id === schedule.marketingId)!;
  const isDedenMonday = schedule.id === "J-M01-SENIN-01";
  const baseLat = marketing.lastLatitude ?? -6.92;
  const baseLng = marketing.lastLongitude ?? 107.65;
  return {
    id:isDedenMonday ? "R-M01-SENIN" : `R-${schedule.id}`,
    marketingId:schedule.marketingId, scheduleId:schedule.id, day:schedule.day, date:schedule.date,
    startedAt:isDedenMonday ? `${schedule.date}T08:05:00+07:00` : `${schedule.date}T${schedule.startTime}:00+07:00`,
    endedAt:schedule.status === "Selesai" ? `${schedule.date}T${schedule.endTime}:00+07:00` : undefined,
    status:schedule.status === "Selesai" ? "Selesai" : "Berjalan",
    visitCount:isDedenMonday ? 3 : 2 + (index % 4),
    distanceKm:isDedenMonday ? 8.4 : 4.8,
    locationPoints:[
      {id:`P-${index}-1`,latitude:baseLat,longitude:baseLng,address:isDedenMonday ? "Kantor KSP MMS" : marketing.lastLocationAddress,timestamp:`${schedule.date}T08:05:00+07:00`,type:"Mulai"},
      {id:`P-${index}-2`,latitude:baseLat+.004,longitude:baseLng+.003,address:isDedenMonday ? "Ahmad Hidayat, Gedebage" : schedule.area,timestamp:`${schedule.date}T08:30:00+07:00`,type:"Kunjungan"},
      {id:`P-${index}-3`,latitude:baseLat+.007,longitude:baseLng-.002,address:isDedenMonday ? "Herman Malik, Buahbatu" : schedule.destination,timestamp:`${schedule.date}T10:00:00+07:00`,type:"Kunjungan",memberId:isDedenMonday ? "A001" : undefined},
      {id:`P-${index}-4`,latitude:baseLat+.002,longitude:baseLng-.005,address:isDedenMonday ? "Siti Nurjanah, Rancasari" : marketing.lastLocationAddress,timestamp:`${schedule.date}T13:30:00+07:00`,type:"Kunjungan"},
      {id:`P-${index}-5`,latitude:baseLat,longitude:baseLng,address:"Gedebage, Kota Bandung",timestamp:`${schedule.date}T15:30:00+07:00`,type:"Selesai"},
    ],
  };
});
