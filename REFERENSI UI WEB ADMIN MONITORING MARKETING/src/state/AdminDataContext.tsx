import { createContext, useCallback, useContext, useEffect, useMemo, useState, type ReactNode } from "react";
import { mockAdminDataService } from "../services/mockAdminDataService";
import type { AccStatus, DashboardData, DayName, OperationalRecap, Prospect, VisitReport } from "../types";

interface AdminDataState {
  selectedDay: DayName;
  setSelectedDay: (day: DayName) => void;
  dashboard?: DashboardData;
  prospects: Prospect[];
  visitReports: VisitReport[];
  operationalRecap?: OperationalRecap;
  loading: boolean;
  error?: string;
  refreshedAt?: Date;
  refresh: () => Promise<void>;
  updateMemberAccStatus: (memberId: string, status: AccStatus) => Promise<void>;
}

const AdminDataContext = createContext<AdminDataState | null>(null);

export function AdminDataProvider({ children }: { children: ReactNode }) {
  const [selectedDay, setSelectedDay] = useState<DayName>("Senin");
  const [dashboard, setDashboard] = useState<DashboardData>();
  const [prospects, setProspects] = useState<Prospect[]>([]);
  const [visitReports, setVisitReports] = useState<VisitReport[]>([]);
  const [operationalRecap, setOperationalRecap] = useState<OperationalRecap>();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string>();
  const [refreshedAt, setRefreshedAt] = useState<Date>();

  const refresh = useCallback(async () => {
    setLoading(true); setError(undefined);
    try {
      const [next, nextProspects, nextVisits, nextRecap] = await Promise.all([
        mockAdminDataService.getDashboard(selectedDay),
        mockAdminDataService.getProspects({ day: selectedDay }),
        mockAdminDataService.getVisitReports({ day: selectedDay }),
        mockAdminDataService.getOperationalRecap(selectedDay),
      ]);
      setDashboard(next);
      setProspects(nextProspects);
      setVisitReports(nextVisits);
      setOperationalRecap(nextRecap);
      setRefreshedAt(new Date());
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : "Data gagal dimuat");
    } finally {
      setLoading(false);
    }
  }, [selectedDay]);
  const updateMemberAccStatus = useCallback(async (memberId: string, status: AccStatus) => {
    await mockAdminDataService.updateMemberAccStatus(memberId, status);
    await refresh();
  }, [refresh]);

  useEffect(() => { void refresh(); }, [refresh]);
  const value = useMemo(() => ({
    selectedDay, setSelectedDay, dashboard, prospects, visitReports,
    operationalRecap, loading, error, refreshedAt, refresh, updateMemberAccStatus,
  }), [selectedDay, dashboard, prospects, visitReports, operationalRecap, loading, error, refreshedAt, refresh, updateMemberAccStatus]);
  return <AdminDataContext.Provider value={value}>{children}</AdminDataContext.Provider>;
}

export function useAdminData() {
  const context = useContext(AdminDataContext);
  if (!context) throw new Error("useAdminData harus digunakan di dalam AdminDataProvider");
  return context;
}
