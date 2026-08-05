import { DAY_DATES } from "../app/constants";
import { DAY_NAMES, type DailyReport } from "../types";
import { marketers } from "./marketers";

export const dailyReports: DailyReport[] = marketers.flatMap((marketing, marketingIndex) =>
  DAY_NAMES.map((day, dayIndex) => {
    const previousTargetAmount = 6_500_000 + (marketingIndex % 4) * 750_000;
    const incomingTargetAmount = [1_500_000, 2_000_000, 1_000_000][(marketingIndex + dayIndex) % 3];
    const outgoingTargetAmount = [0, 500_000, 750_000][(marketingIndex + dayIndex) % 3];
    const newDrop = [1_500_000, 2_000_000, 2_500_000][marketingIndex % 3];
    const continuedDrop = [2_750_000, 3_000_000, 3_500_000][dayIndex % 3];

    return {
      id: `LH-${marketing.code}-${day}`,
      marketingId: marketing.id,
      day,
      date: DAY_DATES[day],
      time: "14:30",
      resort: marketing.area,
      storting: 2_150_000 + marketingIndex * 125_000 + dayIndex * 50_000,
      insuranceAmount: 75_000 + (marketingIndex % 4) * 25_000,
      drop: newDrop + continuedDrop,
      withdrawalSaving: 100_000 + (dayIndex % 3) * 75_000,
      previousTargetAmount,
      previousTargetPeople: 2 + (marketingIndex % 3),
      incomingTargetAmount,
      incomingTargetPeople: incomingTargetAmount ? 1 : 0,
      outgoingTargetAmount,
      outgoingTargetPeople: outgoingTargetAmount ? 1 : 0,
      totalTargetAmount: previousTargetAmount + incomingTargetAmount - outgoingTargetAmount,
      totalTargetPeople: 3 + (marketingIndex % 3),
      newDrop,
      continuedDrop,
      notes: dayIndex === 5 ? "Rekap penutupan operasional pekan ini." : undefined,
      createdAt: `${DAY_DATES[day]}T14:${String((marketingIndex * 3) % 60).padStart(2, "0")}:00+07:00`,
      syncStatus: marketingIndex % 7 === 0 ? "Menunggu Sinkronisasi" : "Tersinkronisasi",
    };
  }),
);
