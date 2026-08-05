import type {
  AccStatus, DailyReport, DailyReportFilters, DashboardData, DayName,
  Journey, Marketing, MarketingSchedule, Member, MemberFilters, OperationalRecap, Prospect,
  ProspectFilters, TrackingData,
  VisitReport, VisitReportFilters,
} from "../types";

export interface AdminDataService {
  // GET /api/admin/dashboard?day=
  getDashboard(day?: DayName): Promise<DashboardData>;
  // GET /api/admin/marketing
  getMarketers(): Promise<Marketing[]>;
  // GET /api/admin/schedules?day=
  getSchedules(day?: DayName): Promise<MarketingSchedule[]>;
  // GET /api/admin/members?day=&marketing_id=
  getMembers(filters?: MemberFilters): Promise<Member[]>;
  // GET /api/admin/daily-reports?day=&marketing_id=
  getDailyReports(filters?: DailyReportFilters): Promise<DailyReport[]>;
  getProspects(filters?: ProspectFilters): Promise<Prospect[]>;
  getProspectById(id: string): Promise<Prospect | undefined>;
  getVisitReports(filters?: VisitReportFilters): Promise<VisitReport[]>;
  getVisitReportById(id: string): Promise<VisitReport | undefined>;
  getOperationalRecap(day?: DayName): Promise<OperationalRecap | undefined>;
  // GET /api/admin/tracking?day=
  getTracking(day?: DayName): Promise<TrackingData[]>;
  // GET /api/admin/journeys?marketing_id=
  getJourneys(marketingId?: string): Promise<Journey[]>;
  // PATCH /api/admin/members/:id/acc-status
  updateMemberAccStatus(memberId: string, status: AccStatus): Promise<void>;
}
