import { journeys } from "../data/journeys";
import { marketers } from "../data/marketers";
import type { DayName, Marketing } from "../types";

export type TrackingStatus = Marketing["trackingStatus"] | "Tidak Dijadwalkan";

export interface LatestTrackingItem {
  marketingId: string;
  code: string;
  name: string;
  area: string;
  status: TrackingStatus;
  latitude: number;
  longitude: number;
  lastActiveAt?: string;
  photo?: string;
}

export interface JourneyMapPoint {
  id: string;
  label: string;
  type: "Mulai" | "Kunjungan" | "Selesai";
  latitude: number;
  longitude: number;
  timestamp: string;
}

export interface MarketingJourneyResult {
  marketingId: string;
  route: Array<[number, number]>;
  points: JourneyMapPoint[];
  updatedAt: string;
}

const pause = (ms = 220) => new Promise(resolve => window.setTimeout(resolve, ms));

// REST replacement: GET /api/admin/tracking?day=Senin
export async function getLatestTracking(day: DayName): Promise<LatestTrackingItem[]> {
  await pause();
  return marketers.flatMap(marketing => {
    const latitude = Number(marketing.lastLatitude);
    const longitude = Number(marketing.lastLongitude);
    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return [];

    return [{
      marketingId: marketing.id,
      code: marketing.code,
      name: marketing.name,
      area: marketing.area,
      status: marketing.workDays.includes(day) ? marketing.trackingStatus : "Tidak Dijadwalkan",
      latitude,
      longitude,
      lastActiveAt: marketing.lastActiveAt,
      photo: marketing.profilePhoto,
    }];
  });
}

// REST replacement: GET /api/admin/marketing/{id}/location
export async function getMarketingLocation(marketingId: string, day?: DayName): Promise<LatestTrackingItem | null> {
  await pause();
  const marketing = marketers.find(item => item.id === marketingId);
  if (!marketing) return null;
  const latitude = Number(marketing.lastLatitude);
  const longitude = Number(marketing.lastLongitude);
  if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return null;

  return {
    marketingId: marketing.id,
    code: marketing.code,
    name: marketing.name,
    area: marketing.area,
    status: day && !marketing.workDays.includes(day) ? "Tidak Dijadwalkan" : marketing.trackingStatus,
    latitude,
    longitude,
    lastActiveAt: marketing.lastActiveAt,
    photo: marketing.profilePhoto,
  };
}

// REST replacement: GET /api/admin/marketing/{id}/journeys?date=
export async function getMarketingJourney(marketingId: string, date: string): Promise<MarketingJourneyResult | null> {
  await pause();
  const journey = journeys.find(item => item.marketingId === marketingId && (!date || item.date === date))
    ?? (!date ? journeys.find(item => item.marketingId === marketingId) : undefined);
  const marketing = marketers.find(item => item.id === marketingId);
  if (!marketing) return null;
  if (date && !journey) return null;

  const fallbackLat = Number(marketing.lastLatitude);
  const fallbackLng = Number(marketing.lastLongitude);
  const rawPoints = journey?.locationPoints?.length
    ? journey.locationPoints
    : [
        { id:`${marketingId}-start`, latitude:fallbackLat-.006, longitude:fallbackLng-.006, timestamp:`${date}T08:00:00+07:00`, type:"Mulai" as const },
        { id:`${marketingId}-visit`, latitude:fallbackLat+.001, longitude:fallbackLng+.004, timestamp:`${date}T11:30:00+07:00`, type:"Kunjungan" as const },
        { id:`${marketingId}-end`, latitude:fallbackLat, longitude:fallbackLng, timestamp:`${date}T14:42:00+07:00`, type:"Selesai" as const },
      ];

  const validPoints = rawPoints.filter(point =>
    Number.isFinite(Number(point.latitude)) && Number.isFinite(Number(point.longitude))
  );
  if (!validPoints.length) return null;

  const points: JourneyMapPoint[] = validPoints.map((point, index) => ({
    id: point.id,
    label: point.type === "Mulai" ? "Titik Awal" : point.type === "Selesai" ? "Titik Akhir" : `Kunjungan ${index}`,
    type: point.type === "Mulai" ? "Mulai" : point.type === "Selesai" ? "Selesai" : "Kunjungan",
    latitude: Number(point.latitude),
    longitude: Number(point.longitude),
    timestamp: point.timestamp,
  }));

  return {
    marketingId,
    route: points.map(point => [point.latitude, point.longitude]),
    points,
    updatedAt: points.at(-1)?.timestamp ?? marketing.lastActiveAt ?? new Date().toISOString(),
  };
}
