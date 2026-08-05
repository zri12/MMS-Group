import { Menu } from "lucide-react";

const pageTitles: Record<string, string> = {
  Dashboard: "Dashboard",
  MarketingList: "Data Marketing",
  DailyHub: "Rencana Kerja",
  TrackingMap: "Tracking Lokasi",
  AnggotaList: "Data Anggota",
  ProspectList: "Data Prospek",
  VisitReportList: "Laporan Kunjungan",
  LaporanHarianList: "Laporan Operasional Harian",
  RekapOperasional: "Rekap Operasional",
  RiwayatList: "Riwayat Perjalanan",
  JadwalList: "Jadwal Marketing",
  Profil: "Profil Admin",
};

export function AppHeader({ current, day, menuOpen, onMenu, onProfile }: {
  current: string;
  day: string;
  menuOpen: boolean;
  onMenu: () => void;
  onProfile: () => void;
}) {
  return (
    <>
      <div aria-hidden="true" className="h-[68px] lg:hidden"/>
      <header className="fixed inset-x-0 top-0 z-[800] flex min-h-[68px] w-full min-w-0 max-w-full items-center gap-3 overflow-hidden border-b border-[var(--border-subtle)] bg-[var(--bg-secondary)] px-4 py-2.5 shadow-[0_8px_24px_rgba(0,0,0,.18)] before:absolute before:inset-x-0 before:top-0 before:h-px before:bg-[var(--bg-secondary)] before:content-[''] sm:px-6 lg:relative lg:inset-auto lg:z-20 lg:shadow-none lg:px-8">
        <button onClick={onMenu} aria-label="Buka menu" aria-expanded={menuOpen}
          className="grid size-11 shrink-0 place-items-center rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-primary)] text-[var(--text-secondary)] transition hover:bg-[var(--surface-hover)] hover:text-white active:scale-[.98] lg:invisible">
          <Menu size={19}/>
        </button>
        <div className="min-w-0 flex-1">
          <p className="truncate text-[16px] font-bold text-[var(--text-primary)] lg:text-[18px]">{pageTitles[current] ?? "KSP MMS"}</p>
          <p className="mt-0.5 truncate text-[11px] text-[var(--text-muted)]">Operasional hari {day}</p>
        </div>
        <button onClick={onProfile} aria-label="Buka profil"
          className="hidden size-10 shrink-0 place-items-center rounded-full border border-white/[0.08] bg-[#151C25] text-xs font-semibold text-[#E6C45A] transition hover:border-[#D4AF37]/25 hover:bg-[#1A222D] active:scale-[.98] min-[430px]:grid">
          A
        </button>
      </header>
    </>
  );
}
