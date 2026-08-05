import { useEffect } from "react";
import { CalendarDays, ClipboardList, CreditCard, FileText, Home, LogOut, MapPin, NotebookTabs, Route, UserRound, Users } from "lucide-react";
import { LogoImage } from "../common/LogoImage";

const groups = [
  { label:"Menu Utama", items:[
    ["Dashboard",Home,"Dashboard"],["MarketingList",Users,"Data Marketing"],
    ["DailyHub",ClipboardList,"Rencana Kerja"],["TrackingMap",MapPin,"Tracking Lokasi"],
  ]},
  { label:"Data Pemasaran", items:[
    ["ProspectList",NotebookTabs,"Data Prospek"],["AnggotaList",CreditCard,"Data Anggota"],
  ]},
  { label:"Laporan", items:[
    ["LaporanHarianList",FileText,"Laporan Operasional Harian"],
    ["VisitReportList",MapPin,"Laporan Kunjungan"],
    ["RekapOperasional",ClipboardList,"Rekap Operasional"],
  ]},
  { label:"Aktivitas", items:[
    ["JadwalList",CalendarDays,"Jadwal Marketing"],
    ["RiwayatList",Route,"Riwayat Perjalanan"],
  ]},
  { label:"Akun", items:[["Profil",UserRound,"Profil Admin"]] },
] as const;

export function Sidebar({ current, open, navigate, onClose, onLogout }: {
  current:string;
  open:boolean;
  navigate:(route:string)=>void;
  onClose:()=>void;
  onLogout:()=>void;
}) {
  useEffect(() => {
    window.dispatchEvent(new Event("app-layout-change"));
    if (!open) return;
    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = "hidden";

    return () => {
      document.body.style.overflow = previousOverflow;
      window.dispatchEvent(new Event("app-layout-change"));
    };
  }, [open]);

  return <>
    {open&&<button onClick={onClose} aria-label="Tutup menu" className="fixed inset-0 z-[900] bg-black/75 backdrop-blur-[2px] lg:hidden"/>}
    <aside className={`fixed inset-y-0 left-0 z-[1000] flex w-[82vw] max-w-[calc(100vw-48px)] flex-col overflow-x-hidden border-r border-[var(--border-subtle)] bg-[var(--sidebar)] shadow-[18px_0_48px_rgba(0,0,0,.3)] transition-transform duration-300 sm:max-w-[304px] lg:w-[272px] lg:max-w-none lg:translate-x-0 ${open?"translate-x-0":"-translate-x-full"}`}>
      <div className="flex min-h-[84px] min-w-0 items-center gap-3 border-b border-[var(--border-subtle)] px-5"><LogoImage className="size-11 shrink-0"/><div className="min-w-0"><p className="truncate text-[14px] font-bold tracking-[-.01em] text-white">KSP MMS</p><p className="mt-0.5 truncate text-[11px] text-[var(--text-muted)]">Marketing Monitoring</p></div></div>
      <nav className="flex-1 space-y-5 overflow-y-auto overflow-x-hidden px-3 py-4">
        {groups.map(group=><div key={group.label}>
          <p className="mb-2 px-3 text-[10px] font-semibold tracking-[.12em] text-[#596472]">{group.label}</p>
          <div className="space-y-1">
            {group.items.map(([route,Icon,label])=><button key={route} onClick={()=>{navigate(route);onClose();}} aria-current={current===route?"page":undefined} className={`relative flex h-11 w-full min-w-0 items-center gap-3 overflow-hidden rounded-xl px-3 text-left text-[13px] font-medium transition ${current===route?"bg-[#D4AF37]/[0.1] text-[#E6C45A]":"text-[#A7AFBA] hover:bg-white/[0.04] hover:text-[#F3F5F7]"}`}>{current===route&&<span className="absolute inset-y-2 left-0 w-0.5 rounded-full bg-[#D4AF37]"/>}<Icon className="shrink-0" size={17}/><span className="min-w-0 truncate">{label}</span></button>)}
          </div>
        </div>)}
      </nav>
      <div className="space-y-2 border-t border-[var(--border-subtle)] p-3">
        <div className="rounded-2xl bg-white/[0.025] p-3.5 text-[12px] text-[#A7AFBA]">
          <p className="font-semibold text-[#F3F5F7]">Admin KSP</p>
          <p className="mt-0.5 text-[11px] text-[#747E8B]">Administrator</p>
        </div>
        <button type="button" onClick={onLogout}
          className="flex min-h-11 w-full min-w-0 items-center gap-3 overflow-hidden rounded-xl bg-red-500/[0.07] px-3 text-left text-[13px] font-semibold text-red-300 transition hover:bg-red-500/[0.11] hover:text-red-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400/50">
          <LogOut className="shrink-0" size={17}/><span className="truncate">Keluar</span>
        </button>
      </div>
    </aside>
  </>;
}
