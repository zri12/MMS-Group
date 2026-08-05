export const DAY_NAMES = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"] as const;
export type DayName = (typeof DAY_NAMES)[number];
export type MemberApprovalStatus = "Menunggu" | "Disetujui" | "Ditolak";
/** @deprecated Gunakan MemberApprovalStatus. */
export type AccStatus = MemberApprovalStatus;
export type SyncStatus = "Tersinkronisasi" | "Menunggu Sinkronisasi" | "Gagal";
export type ScheduleStatus = "Belum Dikunjungi" | "Berlangsung" | "Selesai" | "Dibatalkan";
export type TrackingStatus = "Aktif" | "Offline" | "Belum Mulai" | "GPS Tidak Aktif" | "Tidak Dijadwalkan";

export interface Marketing {
  id: string;
  code: string;
  name: string;
  username: string;
  phone: string;
  area: string;
  profilePhoto?: string;
  accountStatus: "Aktif" | "Nonaktif";
  trackingStatus: TrackingStatus;
  lastLatitude?: number;
  lastLongitude?: number;
  lastLocationAddress?: string;
  lastActiveAt?: string;
  workDays: DayName[];
}

export interface MarketingSchedule {
  id: string;
  marketingId: string;
  day: DayName;
  date: string;
  startTime: string;
  endTime: string;
  area: string;
  destination: string;
  consumerName?: string;
  agenda?: string;
  resort?: string;
  note?: string;
  status: ScheduleStatus;
}

export interface Member {
  id: string;
  resort: string;
  date: string;
  day: DayName;
  time: string;
  name: string;
  memberNumber: string;
  loanNumber: string;
  address: string;
  phone: string;
  business: string;
  loanAmount: number;
  installmentAmount: number;
  insuranceAmount: number;
  collateral: string;
  approvalStatus: MemberApprovalStatus;
  memberPhoto?: string;
  latitude?: number;
  longitude?: number;
  locationAddress?: string;
  marketingId: string;
  createdAt: string;
  syncStatus: SyncStatus;
}

export interface DailyReport {
  id: string;
  marketingId: string;
  day: DayName;
  date: string;
  time: string;
  resort: string;
  storting: number;
  insuranceAmount: number;
  drop: number;
  withdrawalSaving: number;
  previousTargetAmount: number;
  previousTargetPeople: number;
  incomingTargetAmount: number;
  incomingTargetPeople: number;
  outgoingTargetAmount: number;
  outgoingTargetPeople: number;
  totalTargetAmount: number;
  totalTargetPeople: number;
  newDrop: number;
  continuedDrop: number;
  notes?: string;
  createdAt: string;
  syncStatus: SyncStatus;
}

export type ProspectStatus = "Baru" | "Tertarik" | "Perlu Follow Up" | "Tidak Tertarik" | "Selesai";

export interface Prospect {
  id: string;
  name: string;
  phone: string;
  address: string;
  business: string;
  status: ProspectStatus;
  initialVisitResult: string;
  resort: string;
  marketingId: string;
  day: DayName;
  date: string;
  time: string;
  inputDate?: string;
  lastVisitDate?: string;
  lastVisitResult?: string;
  notes?: string;
  latitude?: number;
  longitude?: number;
  locationAddress?: string;
  createdAt: string;
  updatedAt?: string;
  syncStatus: SyncStatus;
  linkedMemberId?: string;
}

export type VisitResult = "Berhasil Bertemu" | "Tidak Bertemu" | "Transaksi Selesai";

export interface VisitReport {
  id: string;
  prospectId: string;
  marketingId: string;
  day: DayName;
  date: string;
  time: string;
  visitPurpose: string;
  visitResult: VisitResult;
  prospectStatus: ProspectStatus;
  notes?: string;
  followUpDate?: string;
  photo?: string;
  photoCaption?: string;
  resort: string;
  latitude?: number;
  longitude?: number;
  locationAddress?: string;
  createdAt: string;
  syncStatus: SyncStatus;
}

export interface OperationalRecapRow {
  id: string;
  marketingId: string;
  mg: string;
  members: { l: number; m: number; k: number; s: number };
  target: { previous: number; incoming: number; outgoing: number; s: number };
  drop: { previous: number; current: number; total: number };
  storting: { previous: number; current: number; total: number };
  percentage: number | null;
  previousCirculation: number;
  currentCirculation: number;
  followedBy: string;
  morningCash: number;
}

export interface OperationalRecap {
  id: string;
  reportNumber: string;
  day: DayName;
  date: string;
  rows: OperationalRecapRow[];
}

export interface LocationPoint {
  id: string;
  latitude: number;
  longitude: number;
  address?: string;
  timestamp: string;
  type: "Mulai" | "Perjalanan" | "Kunjungan" | "Selesai";
  memberId?: string;
}

export interface Journey {
  id: string;
  marketingId: string;
  scheduleId?: string;
  day: DayName;
  date: string;
  startedAt: string;
  endedAt?: string;
  status: "Berjalan" | "Selesai" | "Menunggu Sinkronisasi";
  visitCount: number;
  distanceKm?: number;
  locationPoints: LocationPoint[];
}

export interface TrackingData {
  marketingId: string;
  day: DayName;
  status: Marketing["trackingStatus"] | "Tidak Dijadwalkan";
  latitude?: number;
  longitude?: number;
  lastActiveAt?: string;
}

export interface DashboardData {
  totals: { members: number; target: number; drop: number; storting: number };
  members: Member[];
  reports: DailyReport[];
  tracking: TrackingData[];
  prospects: Prospect[];
  visitReports: VisitReport[];
}

export interface MemberFilters { day?: DayName; marketingId?: string; }
export interface DailyReportFilters { day?: DayName; marketingId?: string; }
export interface ProspectFilters { day?: DayName; marketingId?: string; status?: ProspectStatus; search?: string; }
export interface VisitReportFilters { day?: DayName; marketingId?: string; result?: VisitResult; search?: string; }
