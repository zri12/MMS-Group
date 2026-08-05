import { useEffect, useLayoutEffect, useState } from "react";
import { journeys } from "../data/journeys";
import { prospects as prospectsData } from "../data/prospects";
import { members as membersData } from "../data/members";
import { dailyReports as dailyReportsData } from "../data/dailyReports";
import { visitReports as visitReportsData } from "../data/visitReports";
import { schedules as schedulesData } from "../data/schedules";
import { operationalRecaps } from "../data/operationalRecaps";
import { useAdminData } from "../state/AdminDataContext";
import type { DayName } from "../types";
import { MAIN_ROUTES } from "../app/routes";
import { ErrorState } from "../components/common/ErrorState";
import { LoadingState } from "../components/common/LoadingState";
import { AppHeader as LayoutAppHeader } from "../components/layout/AppHeader";
import { BottomNavigation as LayoutBottomNavigation } from "../components/layout/BottomNavigation";
import { Sidebar as LayoutSidebar } from "../components/layout/Sidebar";
import { RealMap, type RealMapMarker } from "../components/maps/RealMap";
import { MarketingPickerSheet } from "../components/tracking/MarketingPickerSheet";
import {
  ProspectDetailPage, ProspectListPage, VisitReportDetailPage, VisitReportListPage,
} from "./MarketingDataPages";
import { OperationalRecapPage } from "./OperationalRecapPage";
import { DailyReportDetailPage, DailyReportListPage } from "./DailyReportPages";
import { MemberDetailPage, MemberListPage } from "./MemberPages";
import { ScheduleListPage } from "./SchedulePage";
import { TrackingFilterSheet, type TrackingFilterValue } from "../components/tracking/TrackingFilterSheet";
import {
  getLatestTracking, getMarketingJourney, getMarketingLocation,
  type LatestTrackingItem, type MarketingJourneyResult,
} from "../services/trackingService";
import {
  Activity, AlertTriangle, ArrowLeft, CalendarDays, Check, CheckCircle2,
  ChevronDown, ChevronRight, Clock, ClipboardList, Eye, EyeOff, FileText,
  Home, Image as ImageIcon, Info, Lock, LogOut, MapPin,
  Navigation, Pencil, Phone, Plus, RefreshCw, Route,
  Search, ShieldCheck, SlidersHorizontal, Target, UserRound, Users, UserX,
  WalletCards, X, Hash, Building2, CreditCard, Printer, Share2,
} from "lucide-react";

// ── Types ─────────────────────────────────────────────────────────────────────

type RouteName =
  | "Login" | "Dashboard" | "MarketingList" | "MarketingDetail" | "MarketingForm" | "DailyHub"
  | "RekapOperasional" | "RekapAnggota" | "RekapTarget" | "RekapDrop" | "RekapStorting"
  | "AnggotaList" | "AnggotaDetail"
  | "ProspectList" | "ProspectDetail"
  | "VisitReportList" | "VisitReportDetail"
  | "LaporanHarianList" | "LaporanHarianDetail"
  | "TrackingMap" | "TrackingDetail"
  | "RiwayatList" | "RiwayatDetail"
  | "JadwalList" | "JadwalForm"
  | "Profil" | "UbahPassword";

type Route = { name: RouteName; params?: Record<string, unknown> };
type TrackStatus = "Aktif" | "Offline" | "Belum Mulai" | "GPS Tidak Aktif" | "Tidak Aktif" | "Tidak Dijadwalkan";
type AccStatus = "Menunggu" | "Disetujui" | "Ditolak";
type SyncStatus = "Tersinkronisasi" | "Menunggu Sinkronisasi";
type JadwalStatus = "Belum Dikunjungi" | "Berlangsung" | "Selesai" | "Dibatalkan";

interface Marketer {
  id: string; code: string; name: string; area: string;
  phone: string; username: string; schedule: string; days: string[];
  time: string; trackStatus: TrackStatus;
  target: number; drop: number; storting: number; members: number;
  accountStatus: "Aktif" | "Nonaktif"; lat: string; lng: string;
}
interface Anggota {
  id: string; resort: string; tanggal: string; hari: string;
  nama: string; noAnggota: string; noPinjaman: string;
  alamat: string; noHP: string; usaha: string;
  pinjaman: number; angsuran: number; asuransi: number; jaminan: string;
  statusACC: AccStatus; marketingCode: string; marketingName: string;
  waktuInput: string; lat: string; lng: string; alamatLokasi: string;
  statusSync: SyncStatus; foto: string;
}
interface Laporan {
  // Field laporan mengikuti rancangan UI terakhir.
  // Konfirmasi final dengan customer sebelum struktur backend/database dibuat.
  id: string; marketingCode: string; marketingName: string;
  hari: string; tanggal: string;
  storting: number; drop: number; tabunganKeluar: number; lainLain: number;
  targetLamaNominal: number; targetLamaOrang: number;
  targetMasukNominal: number; targetMasukOrang: number;
  targetKeluarNominal: number; targetKeluarOrang: number;
  jumlahTargetNominal: number; jumlahTargetOrang: number;
  dropBaru: number; dropLanjut: number;
  lokasi: string; alamat: string; koordinat: string;
  waktuInput: string; statusSync: SyncStatus;
}
interface Jadwal {
  id: string; marketingCode: string; marketingName: string;
  hari: string; tanggal: string; jamMulai: string; jamSelesai: string;
  area: string; tujuan: string; catatan: string; status: JadwalStatus;
}

// ── Data ──────────────────────────────────────────────────────────────────────

const DAYS = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
const DAY_DATES: Record<string,string> = {
  Senin:"2026-07-21", Selasa:"2026-07-22", Rabu:"2026-07-23",
  Kamis:"2026-07-24", Jumat:"2026-07-25", Sabtu:"2026-07-26",
};

const MARKETERS: Marketer[] = [
  { id:"M01",code:"M01",name:"Deden",area:"Gedebage",phone:"0812-1111-0001",username:"m01.deden",schedule:"08.00–16.00",days:["Senin","Selasa","Rabu","Kamis","Jumat"],time:"14.42",trackStatus:"Aktif",target:10,drop:7.5,storting:3,members:4,accountStatus:"Aktif",lat:"-6.9388",lng:"107.7079"},
  { id:"M02",code:"M02",name:"Angil",area:"Rancasari",phone:"0812-1111-0002",username:"m02.angil",schedule:"08.00–16.00",days:["Senin","Rabu","Jumat"],time:"14.20",trackStatus:"Offline",target:9.5,drop:7,storting:2.8,members:3,accountStatus:"Aktif",lat:"-6.9421",lng:"107.6981"},
  { id:"M03",code:"M03",name:"Ari",area:"Buahbatu",phone:"0812-1111-0003",username:"m03.ari",schedule:"08.00–16.00",days:["Senin","Selasa","Kamis","Sabtu"],time:"14.35",trackStatus:"Aktif",target:9.5,drop:7,storting:3,members:3,accountStatus:"Aktif",lat:"-6.9502",lng:"107.6613"},
  { id:"M04",code:"M04",name:"Feri",area:"Ujungberung",phone:"0812-1111-0004",username:"m04.feri",schedule:"08.00–15.00",days:["Selasa","Rabu","Jumat","Sabtu"],time:"13.55",trackStatus:"Aktif",target:9,drop:6.5,storting:2.7,members:3,accountStatus:"Aktif",lat:"-6.9076",lng:"107.7213"},
  { id:"M05",code:"M05",name:"Sukma",area:"Cibiru",phone:"0812-1111-0005",username:"m05.sukma",schedule:"08.00–16.00",days:["Rabu","Kamis","Jumat"],time:"—",trackStatus:"Belum Mulai",target:9,drop:6.5,storting:2.8,members:2,accountStatus:"Aktif",lat:"-6.9143",lng:"107.7341"},
  { id:"M06",code:"M06",name:"Sandi",area:"Antapani",phone:"0812-1111-0006",username:"m06.sandi",schedule:"08.00–16.00",days:["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"],time:"14.22",trackStatus:"Aktif",target:9.5,drop:7,storting:3,members:4,accountStatus:"Aktif",lat:"-6.9099",lng:"107.6793"},
  { id:"M07",code:"M07",name:"Vikri",area:"Kiaracondong",phone:"0812-1111-0007",username:"m07.vikri",schedule:"09.00–17.00",days:["Senin","Rabu","Kamis","Sabtu"],time:"14.12",trackStatus:"GPS Tidak Aktif",target:9.5,drop:7,storting:3,members:3,accountStatus:"Aktif",lat:"-6.9228",lng:"107.6634"},
  { id:"M08",code:"M08",name:"Farhad",area:"Cicaheum",phone:"0812-1111-0008",username:"m08.farhad",schedule:"08.00–16.00",days:["Senin","Selasa","Rabu","Jumat"],time:"14.05",trackStatus:"Aktif",target:9.5,drop:7,storting:3,members:3,accountStatus:"Aktif",lat:"-6.9195",lng:"107.6782"},
  { id:"M09",code:"M09",name:"Doni",area:"Sukajadi",phone:"0812-1111-0009",username:"m09.doni",schedule:"08.00–15.00",days:["Selasa","Kamis","Sabtu"],time:"13.48",trackStatus:"Offline",target:9,drop:6.5,storting:2.7,members:2,accountStatus:"Aktif",lat:"-6.8941",lng:"107.5963"},
  { id:"M10",code:"M10",name:"Faiz",area:"Lengkong",phone:"0812-1111-0010",username:"m10.faiz",schedule:"08.00–16.00",days:["Senin","Selasa","Rabu","Kamis","Jumat"],time:"14.09",trackStatus:"Aktif",target:10,drop:7.5,storting:3,members:4,accountStatus:"Aktif",lat:"-6.9331",lng:"107.6257"},
  { id:"M11",code:"M11",name:"Agung",area:"Arcamanik",phone:"0812-1111-0011",username:"m11.agung",schedule:"08.00–16.00",days:["Rabu","Kamis","Jumat","Sabtu"],time:"13.42",trackStatus:"Aktif",target:10,drop:7.5,storting:3,members:3,accountStatus:"Aktif",lat:"-6.9089",lng:"107.6923"},
  { id:"M12",code:"M12",name:"Faisal",area:"Cimahi",phone:"0812-1111-0012",username:"m12.faisal",schedule:"08.00–16.00",days:["Senin","Rabu","Jumat"],time:"—",trackStatus:"Belum Mulai",target:9,drop:6.5,storting:2.7,members:2,accountStatus:"Aktif",lat:"-6.8820",lng:"107.5434"},
  { id:"M13",code:"M13",name:"Agnes",area:"Cileunyi",phone:"0812-1111-0013",username:"m13.agnes",schedule:"08.00–15.00",days:["Selasa","Kamis","Sabtu"],time:"13.26",trackStatus:"Offline",target:10,drop:7.5,storting:3,members:3,accountStatus:"Aktif",lat:"-6.9526",lng:"107.7384"},
];

const MEMBER_NAMES = [
  "Toko Berkah Jaya","Siti Nurjanah","Dedi Saputra","Warung Sumber Rezeki",
  "Ahmad Hidayat","Maya Rahayu","Bengkel Sukses","Kios Makmur",
  "Dewi Fortuna Shop","Pak Budi Santoso","Ibu Sri Wahyuni","Toko Anugrah",
  "CV Maju Bersama","Rizki Abadi Store","Putra Mandiri","Sumber Barokah",
  "Warung Pak Eko","Ibu Yanti Kuliner","Agro Jaya Farm","Batik Nusantara",
  "Counter Pulsa Cepat","Depot Air Segar","Elektronik Murah","Furniture Indah",
  "Grab Mitra Setia","Herbal Sehat","Indo Ritel","Jasa Las Karya",
  "Kopi Nusantara","Laundry Express","Meubel Jaya","Nasi Uduk Bu Yuli",
  "Optik Sehat","Pasar Swalayan Mini","Qolbu Store","Roti Bakar Malam",
  "Salon Cantik","Tahu Tempe Pak Heri","Usaha Bersama 39",
];

let _idx = 0;
const ANGGOTA_DATA: Anggota[] = MARKETERS.flatMap((m) =>
  Array.from({ length: m.members }, (_, ai): Anggota => {
    const i = _idx++;
    const pinjamans = [2000000,3000000,5000000,7500000,10000000];
    const p = pinjamans[i % 5];
    return {
      id:`A${String(i+1).padStart(3,"0")}`,resort:m.area,
      tanggal:`2026-07-${String(14+(i%7)).padStart(2,"0")}`,
      hari:DAYS[i%6],nama:MEMBER_NAMES[i],
      noAnggota:`AN-${1000+i+1}`,noPinjaman:`PJ-${2000+i+1}`,
      alamat:`Jl. ${["Mawar","Melati","Kenanga","Dahlia","Anggrek"][i%5]} No.${i+1}, ${m.area}`,
      noHP:`0812-${String(2000+i).padStart(4,"0")}-${String(5000+i).padStart(4,"0")}`,
      usaha:["Warung","Toko Kelontong","Bengkel","Kuliner","Pertanian"][i%5],
      pinjaman:p,angsuran:Math.round(p/8),asuransi:Math.round(p*0.025),
      jaminan:["BPKB Motor","Sertifikat Tanah","KK + KTP","Surat Tanah"][i%4],
      statusACC:(["Disetujui","Menunggu","Disetujui","Disetujui","Ditolak","Disetujui","Menunggu","Disetujui"] as AccStatus[])[i%8],
      marketingCode:m.code,marketingName:m.name,
      waktuInput:`${String(8+(i%8)).padStart(2,"0")}:${String(i%60).padStart(2,"0")} WIB`,
      lat:m.lat,lng:m.lng,alamatLokasi:`${m.area}, Kota Bandung`,
      statusSync:(i%5===0?"Menunggu":"Tersinkronisasi") as SyncStatus,
      foto:`/profiles/member-${String((i%16)+1).padStart(2,"0")}.jpg`,
    };
  })
);

const LAPORAN_DATA: Laporan[] = MARKETERS.map((m, i): Laporan => ({
  id:`L${String(i+1).padStart(3,"0")}`,marketingCode:m.code,marketingName:m.name,
  hari:DAYS[i%6],tanggal:`2026-07-${String(20+(i%6)).padStart(2,"0")}`,
  storting:Math.round(m.storting*1_000_000),drop:Math.round(m.drop*1_000_000),
  tabunganKeluar:150_000*(i+1),lainLain:50_000*(i+1),
  targetLamaNominal:Math.round(m.target*1_000_000),targetLamaOrang:m.members,
  targetMasukNominal:1_000_000,targetMasukOrang:1,
  targetKeluarNominal:0,targetKeluarOrang:0,
  jumlahTargetNominal:Math.round(m.target*1_000_000),jumlahTargetOrang:m.members,
  dropBaru:Math.round(m.drop*400_000),dropLanjut:Math.round(m.drop*600_000),
  lokasi:m.area,alamat:`${m.area}, Kota Bandung`,koordinat:`${m.lat}, ${m.lng}`,
  waktuInput:m.time==="—"?"09.00 WIB":`${m.time} WIB`,
  statusSync:(i%5===0?"Menunggu Sinkronisasi":"Tersinkronisasi") as SyncStatus,
}));


const START_TIMES = ["08:00","08:30","07:30","08:00","08:15","07:45","09:00","08:00","08:00","08:30","07:30","08:00","08:00"];
const END_TIMES = ["16:00","16:30","15:30","15:00","16:15","15:45","17:00","16:00","15:00","16:30","15:30","16:00","15:00"];
const getScheduleStatus = (dayIndex: number, totalDays: number): JadwalStatus =>
  dayIndex === totalDays - 1 ? "Berlangsung" : "Selesai";
const JADWAL_DATA: Jadwal[] = MARKETERS.flatMap((m,mi): Jadwal[] =>
  m.days.map((day,di): Jadwal => ({
    id:`J-${m.code}-${di}`,marketingCode:m.code,marketingName:m.name,
    hari:day,tanggal:DAY_DATES[day],
    jamMulai:START_TIMES[mi],jamSelesai:END_TIMES[mi],
    area:m.area,tujuan:`Kunjungan anggota wilayah ${m.area}`,catatan:"Bawa berkas lengkap",
    status:getScheduleStatus(di,m.days.length),
  }))
);

// ── Utilities ─────────────────────────────────────────────────────────────────

const fmtJt = (n: number) => `Rp${(n/1_000_000).toLocaleString("id-ID",{minimumFractionDigits:1,maximumFractionDigits:1})}\u00A0jt`;
const fmtFull = (n: number) => `Rp${n.toLocaleString("id-ID")}`;
const formatDate = (value: string) => new Intl.DateTimeFormat("id-ID", {
  day:"2-digit", month:"short", year:"numeric",
}).format(new Date(`${value}T00:00:00+07:00`));

const TOTAL_ANGGOTA = 39;
const TOTAL_TARGET = 123_500_000;
const TOTAL_DROP = 91_000_000;
const TOTAL_STORTING = 37_700_000;
const trackingStatusFor = (marketing: Marketer, day: string): TrackStatus =>
  marketing.days.includes(day) ? marketing.trackStatus : "Tidak Dijadwalkan";
const routeFor = (lat: number, lng: number): Array<[number, number]> => [
  [lat - .006, lng - .006],
  [lat - .003, lng + .002],
  [lat + .001, lng + .004],
  [lat + .004, lng - .001],
  [lat, lng],
];

// ── Shared UI ─────────────────────────────────────────────────────────────────

function LogoImg({ cls = "size-10" }: { cls?: string }) {
  const [err, setErr] = useState(false);
  if (err) return (
    <div className={`${cls} grid place-items-center rounded-full bg-[#10251d] text-[#65c78f] font-extrabold text-[10px] shrink-0 ring-1 ring-emerald-400/30`}>KSP</div>
  );
  return <img src="/LOGO-KSP.jpeg" alt="Logo KSP" onError={()=>setErr(true)} className={`${cls} object-cover rounded-full shrink-0`}/>;
}

function Ava({ name, cls = "size-11 text-sm" }: { name: string; cls?: string }) {
  const [failed, setFailed] = useState(false);
  const marketing = MARKETERS.find(item=>item.name===name);
  const photo = marketing ? `/profiles/marketing-${marketing.code.slice(1)}.jpg` : "";
  if (!photo || failed) return <div className={`${cls} grid shrink-0 place-items-center rounded-full bg-[#1A2235] font-extrabold text-[#D4AF37] ring-1 ring-white/10`}>{name.slice(0,1).toUpperCase()}</div>;
  return <img src={photo} alt={`Foto profil ${name}`} loading="lazy" onError={()=>setFailed(true)} className={`${cls} shrink-0 rounded-full object-cover ring-1 ring-white/10`}/>;
}

function DCard({ children, cls = "" }: { children: React.ReactNode; cls?: string }) {
  return <div className={`ui-card ${cls}`}>{children}</div>;
}

function GoldBtn({ children, onClick, cls = "", type = "button", disabled = false }: {
  children: React.ReactNode; onClick?: () => void; cls?: string; type?: "button"|"submit"; disabled?: boolean;
}) {
  return (
    <button type={type} onClick={onClick} disabled={disabled}
      className={`ui-button-primary transition active:scale-[.98] disabled:opacity-50 ${cls}`}>
      {children}
    </button>
  );
}

function GhostBtn({ children, onClick, cls = "", danger = false }: {
  children: React.ReactNode; onClick?: () => void; cls?: string; danger?: boolean;
}) {
  return (
    <button onClick={onClick}
      className={`transition active:scale-[.98] ${danger?"flex min-h-12 min-w-0 max-w-full items-center justify-center gap-2 rounded-[14px] bg-red-500/[0.08] px-4 text-[13px] font-bold text-red-400 hover:bg-red-500/[0.12]":"ui-button-secondary"} ${cls}`}>
      {children}
    </button>
  );
}

function StatusBadge({ status }: { status: TrackStatus }) {
  const cfg: Record<TrackStatus,string> = {
    Aktif:"bg-emerald-500/[0.12] text-emerald-400",
    Offline:"bg-red-500/[0.12] text-red-400",
    "Belum Mulai":"bg-[#9CA3AF]/[0.11] text-[#C7CDD5]",
    "GPS Tidak Aktif":"bg-amber-500/[0.12] text-amber-400",
    "Tidak Aktif":"bg-[#6B7280]/[0.12] text-[#9CA3AF]",
    "Tidak Dijadwalkan":"bg-[#5B6471]/[0.12] text-[#89929E]",
  };
  const dot: Record<TrackStatus,string> = { Aktif:"bg-[#22C55E]",Offline:"bg-[#EF4444]","Belum Mulai":"bg-[#9CA3AF]","GPS Tidak Aktif":"bg-[#F59E0B]","Tidak Aktif":"bg-[#6B7280]","Tidak Dijadwalkan":"bg-[#5B6471]" };
  return (
    <span className={`inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ${cfg[status]}`}>
      <span className={`size-1.5 rounded-full ${dot[status]}`}/>
      {status}
    </span>
  );
}

function AccBadge({ status }: { status: AccStatus }) {
  const cfg: Record<AccStatus,string> = {
    Disetujui:"bg-emerald-500/[0.12] text-emerald-400",
    Menunggu:"bg-amber-500/[0.12] text-amber-400",
    Ditolak:"bg-red-500/[0.12] text-red-400",
  };
  return <span className={`inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold ${cfg[status]}`}>{status}</span>;
}

function SyncBadge({ status }: { status: SyncStatus }) {
  return status === "Tersinkronisasi"
    ? <span className="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-bold text-emerald-400"><CheckCircle2 size={10}/>Tersinkronisasi</span>
    : <span className="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2 py-0.5 text-[11px] font-bold text-amber-400"><Clock size={10}/>Menunggu Sinkronisasi</span>;
}

function JadwalStatusBadge({ status }: { status: JadwalStatus }) {
  const cfg: Record<JadwalStatus,string> = {
    Selesai:"bg-emerald-500/[0.12] text-emerald-400",
    Berlangsung:"bg-blue-500/[0.12] text-blue-400",
    "Belum Dikunjungi":"bg-[#6B7280]/[0.12] text-[#9CA3AF]",
    Dibatalkan:"bg-red-500/[0.12] text-red-400",
  };
  return <span className={`inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold ${cfg[status]}`}>{status}</span>;
}

function PageHdr({ title, sub, onBack, action }: { title:string; sub?:string; onBack?:()=>void; action?:React.ReactNode }) {
  return (
    <div className={`flex w-full min-w-0 max-w-full items-center justify-between gap-3 overflow-hidden px-4 sm:px-6 lg:px-8 ${onBack?"sticky top-0 z-20 min-h-[68px] border-b border-white/[0.07] bg-[#0A0F15]/95 py-3 backdrop-blur-md":"pb-2 pt-6"}`}>
      <div className="flex items-center gap-3 min-w-0">
        {onBack && (
          <button onClick={onBack} aria-label="Kembali" className="grid size-11 shrink-0 place-items-center rounded-[14px] border border-white/[0.08] bg-white/[0.04] text-[#A8B0BD] transition duration-200 hover:border-[#D4AF37]/30 hover:text-white active:scale-95">
            <ArrowLeft size={18}/>
          </button>
        )}
        <div className="min-w-0">
          <h1 className="line-clamp-2 text-[21px] font-semibold leading-[1.25] tracking-[-.02em] text-[#F3F5F7] sm:text-[23px] lg:text-[27px]">{title}</h1>
          {sub && <p className="mt-1 text-[12px] leading-relaxed text-[#747E8B]">{sub}</p>}
        </div>
      </div>
      {action && <div className="min-w-0 max-w-[50%] shrink-0">{action}</div>}
    </div>
  );
}

function EmptyState({ icon: Icon, title, desc }: { icon: React.ElementType; title:string; desc:string }) {
  return (
    <div className="flex flex-col items-center px-4 py-14 text-center">
      <div className="grid size-12 place-items-center rounded-2xl bg-white/[0.035] text-[#747E8B]"><Icon size={22}/></div>
      <p className="mt-4 text-[14px] font-semibold text-[#F3F5F7]">{title}</p>
      <p className="mt-1 max-w-sm text-[12px] leading-relaxed text-[#747E8B]">{desc}</p>
    </div>
  );
}

function Photo({ src, label }: { src?: string; label: string }) {
  const [err, setErr] = useState(false);
  const isDocument = /transfer|bukti/i.test(label);
  if (!src || err) return (
    <div className="flex h-56 w-full flex-col items-center justify-center gap-2 rounded-2xl border border-white/[0.08] bg-[#10161e] sm:h-60">
      <ImageIcon size={24} className="text-[#7E8794]"/>
      <p className="text-[12px] text-[#7E8794]">Foto belum tersedia</p>
    </div>
  );
  return (
    <div className="relative overflow-hidden rounded-2xl border border-white/[0.08] bg-[#0a0f15] shadow-[0_12px_30px_rgba(0,0,0,.14)]">
      <img src={src} alt={label} loading="lazy" onError={()=>setErr(true)}
        className={`h-56 w-full sm:h-60 ${isDocument?"object-contain p-2":"object-cover"}`}/>
      <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/85 via-black/45 to-transparent px-3 pb-3 pt-8">
        <p className="text-[11px] font-bold text-white">{label}</p>
      </div>
    </div>
  );
}

function MapPH({ label, lat=-6.9175, lng=107.6191, route=[] }: { label: string; lat?:number; lng?:number; route?:Array<[number,number]> }) {
  return <RealMap
    className="h-[260px] sm:h-[320px]"
    markers={[{id:label,name:label,latitude:lat,longitude:lng,address:label,status:"Aktif"}]}
    route={route}
    zoom={15}
  />;
}

function Inp({ label, type="text", ph, value, onChange, cls="" }: {
  label:string; type?:string; ph?:string; value?:string; onChange?:(v:string)=>void; cls?:string;
}) {
  return (
    <label className={`block w-full min-w-0 max-w-full ${cls}`}>
      <span className="ui-label">{label}</span>
      <input type={type} placeholder={ph} value={value} onChange={e=>onChange?.(e.target.value)}
        className="ui-field mt-1.5 px-3.5 py-2.5"/>
    </label>
  );
}

function Sel({ label, opts, value, onChange }: { label:string; opts:string[]; value?:string; onChange?:(v:string)=>void }) {
  return (
    <label className="block w-full min-w-0 max-w-full">
      <span className="ui-label">{label}</span>
      <select value={value} onChange={e=>onChange?.(e.target.value)}
        className="ui-field mt-1.5 px-3.5 py-2.5">
        {opts.map(o=><option key={o} value={o}>{o}</option>)}
      </select>
    </label>
  );
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
  return (
    <div className="flex min-h-11 items-center justify-between gap-4 border-b border-white/5 py-2.5 last:border-none">
      <span className="shrink-0 text-[12px] text-[#7E8794]">{label}</span>
      <span className="min-w-0 break-words text-right text-[13px] font-bold text-[#F8FAFC]">{value}</span>
    </div>
  );
}

function TabBar({ tabs, active, onSelect }: { tabs: string[]; active: string; onSelect: (t: string) => void }) {
  return (
    <div className="no-scrollbar flex gap-5 overflow-x-auto border-b border-white/[0.07] bg-[#080B10] px-4">
      {tabs.map(t => (
        <button key={t} onClick={() => onSelect(t)}
          className={`relative min-h-11 shrink-0 px-0.5 py-2 text-[12px] font-semibold transition active:scale-[.98] ${active===t?"text-[#E6C45A] after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 after:rounded-full after:bg-[#D4AF37]":"text-[#747E8B] hover:text-[#A7AFBA]"}`}>
          {t}
        </button>
      ))}
    </div>
  );
}

// ── Login ─────────────────────────────────────────────────────────────────────

function LoginPage({ onLogin }: { onLogin: () => void }) {
  const [busy, setBusy] = useState(false);
  const [show, setShow] = useState(false);
  const enter = () => { setBusy(true); setTimeout(onLogin, 600); };
  return (
    <div className="grid min-h-dvh place-items-center overflow-x-hidden bg-[#080B10] font-sans sm:px-6 sm:py-8 lg:p-0">
      <section className="relative flex min-h-dvh w-full flex-col overflow-hidden bg-[#0A0F15] sm:min-h-0 sm:max-w-[520px] sm:rounded-[32px] sm:border sm:border-white/[0.07] sm:shadow-[0_20px_60px_rgba(0,0,0,.3)] lg:grid lg:min-h-dvh lg:max-w-none lg:grid-cols-[1.05fr_.95fr] lg:rounded-none lg:border-0 lg:shadow-none">
        <div className="pointer-events-none absolute -right-12 -top-16 size-44 rounded-full border border-[#D4AF37]/[0.12]"/>
        <div className="pointer-events-none absolute right-3 top-0 size-24 rounded-full border border-[#D4AF37]/[0.12]"/>

        <div className="relative flex min-h-[263px] shrink-0 flex-col items-center px-6 pb-8 pt-14 text-center sm:min-h-0 sm:px-10 sm:pb-11 sm:pt-14 lg:min-h-dvh lg:items-start lg:justify-center lg:overflow-hidden lg:border-r lg:border-white/[0.07] lg:bg-[#0D1219] lg:px-[clamp(64px,8vw,150px)] lg:py-16 lg:text-left">
          <div className="pointer-events-none absolute -bottom-40 -left-32 hidden size-[480px] rounded-full border border-[#D4AF37]/10 lg:block"/>
          <div className="pointer-events-none absolute -bottom-20 -left-10 hidden size-[280px] rounded-full border border-[#D4AF37]/10 lg:block"/>
          <LogoImg cls="size-16 sm:size-[76px] lg:size-20"/>
          <p className="mt-5 hidden text-[14px] font-semibold tracking-[.01em] text-[#E6C45A] lg:block">
            KSP Manunggal Makmur Sejahtera
          </p>
          <h1 className="mt-6 text-[26px] font-semibold tracking-[-.025em] text-[#F3F5F7] sm:text-[31px] lg:mt-8 lg:text-[38px]">
            <span className="lg:hidden">Selamat Datang</span>
            <span className="hidden max-w-[560px] lg:block lg:text-[48px] lg:leading-[1.08]">Marketing<br/>Monitoring</span>
          </h1>
          <p className="mt-2 max-w-[280px] text-[13px] leading-5 text-[#A5AEBA] sm:text-[15px] sm:leading-6 lg:mt-3 lg:max-w-[330px] lg:text-[16px]">
            <span className="lg:hidden">Masuk menggunakan akun marketing Anda</span>
            <span className="hidden lg:inline">Pantau aktivitas, lokasi, dan laporan marketing dalam satu ruang kerja yang terintegrasi.</span>
          </p>
          <div className="mt-8 hidden flex-wrap gap-2.5 lg:flex">
            {["Tracking Lokasi","Data Harian","Monitoring Marketing"].map(label=>(
              <span key={label} className="rounded-full border border-[#D4AF37]/15 bg-[#D4AF37]/[0.07] px-3.5 py-2 text-[11px] font-bold text-[#D4AF37]">
                {label}
              </span>
            ))}
          </div>
        </div>

        <form
          onSubmit={event=>{event.preventDefault();enter();}}
          className="relative flex flex-1 flex-col gap-5 border-t border-[#D4AF37]/[0.18] bg-[#05080b] px-5 pb-[max(28px,env(safe-area-inset-bottom))] pt-7 sm:block sm:space-y-6 sm:px-10 sm:pb-10 sm:pt-9 lg:flex lg:min-h-dvh lg:items-center lg:justify-center lg:border-t-0 lg:bg-[#070b0f] lg:px-14 lg:py-16"
        >
          <div className="contents lg:block lg:w-full lg:max-w-[440px] lg:space-y-6">
          <div className="hidden lg:block">
            <p className="text-[11px] font-semibold tracking-[.18em] text-[#D4AF37]">AKSES SISTEM</p>
            <h2 className="mt-3 text-[30px] font-semibold tracking-[-.02em] text-[#F3F5F7]">Selamat Datang</h2>
            <p className="mt-2 text-[13px] leading-5 text-[#7E8794]">Masukkan username dan password untuk melanjutkan.</p>
          </div>
          <label className="block">
            <span className="text-[14px] font-bold text-[#E8C65A]">Username</span>
            <input
              name="username"
              autoComplete="username"
              placeholder="Masukkan username Anda"
              className="mt-2 min-h-[50px] w-full rounded-[20px] border border-white/[0.12] bg-[#151b22] px-4 text-[14px] text-[#F8FAFC] outline-none transition placeholder:text-[#647084] focus:border-[#D4AF37]/55 focus:ring-2 focus:ring-[#D4AF37]/10 sm:mt-2.5 sm:min-h-[58px] sm:rounded-[24px] sm:px-5 sm:text-[15px]"
            />
          </label>
          <label className="block">
            <span className="text-[14px] font-bold text-[#E8C65A]">Password</span>
            <span className="relative mt-2 block sm:mt-2.5">
              <input
                name="password"
                type={show?"text":"password"}
                autoComplete="current-password"
                placeholder="Masukkan password"
                className="min-h-[50px] w-full rounded-[20px] border border-white/[0.12] bg-[#151b22] px-4 pr-13 text-[14px] text-[#F8FAFC] outline-none transition placeholder:text-[#647084] focus:border-[#D4AF37]/55 focus:ring-2 focus:ring-[#D4AF37]/10 sm:min-h-[58px] sm:rounded-[24px] sm:px-5 sm:pr-14 sm:text-[15px]"
              />
              <button type="button" onClick={()=>setShow(!show)} aria-label={show?"Sembunyikan password":"Tampilkan password"}
                className="absolute right-3 top-1/2 grid size-11 -translate-y-1/2 place-items-center rounded-full text-[#8993A2] transition hover:text-[#E8C65A] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]/60">
                {show ? <EyeOff size={20}/> : <Eye size={20}/>}
              </button>
            </span>
          </label>
          <label className="flex w-fit cursor-pointer items-center gap-3 text-[14px] font-bold text-[#F8FAFC]">
            <input type="checkbox" className="size-5 appearance-none rounded-full border-2 border-[#34404D] bg-[#151b22] checked:border-[#D4AF37] checked:bg-[#D4AF37] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]/50 sm:size-6"/>
            Ingat saya
          </label>
          <button type="submit" disabled={busy}
            className="mt-1 flex min-h-[50px] w-full items-center justify-center gap-2 rounded-[14px] bg-[#D4AF37] text-[14px] font-semibold text-[#080B10] transition hover:bg-[#E6C45A] active:scale-[.98] disabled:cursor-wait disabled:opacity-70 sm:min-h-[54px] sm:text-[15px]">
            {busy && <RefreshCw size={17} className="animate-spin"/>}
            {busy ? "Masuk..." : "Masuk"}
          </button>
          </div>
        </form>
      </section>
    </div>
  );
}

// ── Layout ────────────────────────────────────────────────────────────────────

const MAIN_PAGES: RouteName[] = [
  ...MAIN_ROUTES, "ProspectList", "AnggotaList", "RekapOperasional",
  "VisitReportList", "LaporanHarianList", "JadwalList", "RiwayatList",
];

// ── Dashboard ─────────────────────────────────────────────────────────────────

function DashboardPage({ day, setDay, navigate }: { day: string; setDay: (d:string)=>void; navigate: (n:RouteName,p?:Record<string,unknown>)=>void }) {
  const [showDayMenu, setShowDayMenu] = useState(false);
  const [toast, setToast] = useState("");
  const notify = (s:string) => { setToast(s); setTimeout(()=>setToast(""),2200); };
  useEffect(() => {
    if (!showDayMenu) return;
    const close = (event: KeyboardEvent) => {
      if (event.key === "Escape") setShowDayMenu(false);
    };
    window.addEventListener("keydown", close);
    return () => window.removeEventListener("keydown", close);
  }, [showDayMenu]);

  const recapRows = operationalRecaps.find(item=>item.day===day)?.rows ?? [];
  const totalTargetMasuk = recapRows.reduce((total,item)=>total+item.target.incoming,0);
  const totalTargetKeluar = recapRows.reduce((total,item)=>total+item.target.outgoing,0);
  const totalSirkulasi = recapRows.reduce((total,item)=>total+item.currentCirculation,0);

  const summaryCards = [
    { label:"Total Anggota", value:"39", sub:"anggota aktif", icon:Users, page:"RekapAnggota" as RouteName },
    { label:"Total Target", value:"Rp123,5 jt", sub:"target nominal", icon:Target, page:"RekapTarget" as RouteName },
    { label:"Total Drop", value:"Rp91 jt", sub:"realisasi drop", icon:WalletCards, page:"RekapDrop" as RouteName },
    { label:"Total Storting", value:"Rp37,7 jt", sub:"realisasi storting", icon:CreditCard, page:"RekapStorting" as RouteName },
    { label:"Target Keluar", value:fmtJt(totalTargetKeluar), sub:`rekap hari ${day}`, icon:Target, page:"RekapOperasional" as RouteName },
    { label:"Target Masuk", value:fmtJt(totalTargetMasuk), sub:`rekap hari ${day}`, icon:Target, page:"RekapOperasional" as RouteName },
    { label:"Sirkulasi", value:fmtJt(totalSirkulasi), sub:`sirkulasi hari ${day}`, icon:RefreshCw, page:"RekapOperasional" as RouteName },
  ];

  const dailyShortcuts = [
    {label:"Foto Pencairan",hint:"Dokumentasi pencairan",icon:ImageIcon,to:"AnggotaList" as RouteName},
    {label:"Laporan Tunai",hint:"Laporan transaksi tunai",icon:WalletCards,to:"LaporanHarianList" as RouteName},
    {label:"Rekap Target",hint:"Ringkasan target marketing",icon:Target,to:"RekapTarget" as RouteName},
    {label:"Foto Bukti Transfer",hint:"Dokumentasi transfer",icon:CreditCard,to:"LaporanHarianList" as RouteName},
  ];
  return (
    <div className="max-w-full overflow-x-clip pb-20 lg:pb-8">
      {/* Summary cards */}
      <div className="px-4 pt-6 sm:px-6 lg:px-8">
        <p className="text-[12px] font-semibold text-[#A7AFBA]">Ringkasan Operasional</p>
        <p className="mt-1 text-[13px] text-[#7E8794]">Ringkasan total data operasional seluruh marketing.</p>
        <div className="mt-4 grid grid-cols-2 gap-2.5 sm:gap-3 lg:grid-cols-4">
          {summaryCards.map(({ label, value, sub, icon: Icon, page }) => (
            <button key={label} onClick={()=>navigate(page)}
            className="group min-h-[126px] w-full min-w-0 max-w-full overflow-hidden rounded-[16px] border border-white/[0.07] bg-[#111720] p-3.5 text-left transition hover:-translate-y-px hover:border-white/[0.12] hover:bg-[#151C25] active:scale-[.99] sm:min-h-[136px] sm:p-5">
              <div className="grid size-9 place-items-center rounded-xl bg-white/[0.04] text-[#A7AFBA] transition group-hover:text-[#E6C45A] sm:size-10"><Icon size={18}/></div>
              <p className="font-data mt-3 break-words text-[20px] font-medium leading-tight text-[#F3F5F7] sm:text-[24px]" data-numeric="true">{value}</p>
              <p className="mt-1 text-[12px] font-medium text-[#A7AFBA]">{label}</p>
              <p className="mt-0.5 text-[11px] text-[#747E8B]">{sub}</p>
            </button>
          ))}
        </div>
      </div>

      {/* Day picker */}
      <div className="mt-7 px-4 sm:px-6 lg:px-8">
        <div className="mb-3 flex items-start justify-between gap-3">
          <div className="min-w-0">
            <p className="text-[16px] font-semibold text-[#F3F5F7]">Pilih Hari Operasional</p>
            <p className="text-[12px] text-[#7E8794]">Pilih hari untuk membuka menu operasional</p>
          </div>
          <button onClick={()=>navigate("RekapOperasional")} className="shrink-0 text-[12px] font-bold text-[#D4AF37]"><span className="sm:hidden">Rekap</span><span className="hidden sm:inline">Rekap Tabel</span></button>
        </div>
        <div className="grid w-full min-w-0 grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
          {DAYS.map(d => (
            <button key={d} onClick={()=>setDay(d)}
            className={`h-10 w-full min-w-0 rounded-xl border px-3 text-[12px] font-semibold transition sm:h-11 sm:px-4 sm:text-[13px] ${d===day?"border-[#D4AF37] bg-[#D4AF37] text-[#080B10]":"border-white/[0.07] bg-[#151C25] text-[#A7AFBA] hover:bg-[#1E2834] hover:text-[#F3F5F7]"}`}>
              {d}
            </button>
          ))}
        </div>
      </div>

      {/* Daily shortcuts */}
      <div className="mt-6 px-4 sm:px-6 lg:px-8">
        <div className="mb-3">
          <p className="text-[16px] font-semibold text-[#F3F5F7]">Akses Cepat {day}</p>
          <p className="mt-0.5 text-[12px] text-[#7E8794]">Buka kebutuhan operasional tanpa langkah tambahan.</p>
        </div>
        <div className="grid grid-cols-2 gap-2.5 sm:gap-3 lg:grid-cols-4">
          {dailyShortcuts.map(({label,hint,icon:Icon,to})=>(
            <button key={label} onClick={()=>navigate(to)}
              className="group flex min-h-[88px] w-full min-w-0 max-w-full items-center gap-3 overflow-hidden rounded-[16px] border border-white/[0.07] bg-[#111720] p-3 text-left transition hover:bg-[#1A222D] active:scale-[.985] sm:p-3.5">
              <span className="grid size-9 shrink-0 place-items-center rounded-[12px] bg-[#D4AF37]/[0.1] text-[#D4AF37] sm:size-10"><Icon size={18}/></span>
              <span className="min-w-0">
                <span className="block text-[13px] font-semibold leading-tight text-[#F3F5F7]">{label}</span>
                <span className="mt-1 block text-[10.5px] leading-tight text-[#7E8794]">{hint}</span>
              </span>
            </button>
          ))}
        </div>
      </div>

      {/* Marketing carousel */}
      <div className="mt-8 px-4 sm:px-6 lg:px-8">
        <div className="mb-3 flex items-end justify-between gap-4">
          <div>
            <p className="text-[16px] font-semibold text-[#F3F5F7]">Status Marketing Hari Ini</p>
            <p className="text-[12px] text-[#7E8794]">Geser untuk melihat seluruh marketing</p>
          </div>
          <button onClick={()=>navigate("MarketingList")} className="text-[12px] font-bold text-[#D4AF37] shrink-0">Lihat Semua</button>
        </div>
        <div className="no-scrollbar flex w-full min-w-0 max-w-full gap-3 overflow-x-auto overflow-y-hidden scroll-smooth pb-3 pt-1" style={{scrollSnapType:"x mandatory"}}>
          {MARKETERS.filter(m=>m.days.includes(day)).map(m => (
            <button key={m.id} onClick={()=>navigate("MarketingDetail",{id:m.id})}
              style={{scrollSnapAlign:"start"}}
              className="marketing-carousel-card w-[230px] max-w-[80vw] flex-none rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:-translate-y-px hover:bg-[#151C25]">
              <div className="flex items-center gap-3">
                <Ava name={m.name} cls="size-10 text-sm"/>
                <div className="min-w-0">
                  <p className="text-[11px] font-bold text-[#D4AF37]">{m.code}</p>
                  <p className="truncate text-[15px] font-semibold text-[#F3F5F7]">{m.name}</p>
                </div>
              </div>
              <StatusBadge status={m.trackStatus}/>
              <p className="mt-2.5 text-[11px] text-[#7E8794]">{m.area}</p>
              {m.trackStatus==="Offline" && <p className="mt-1 text-[11px] text-red-400">Terakhir aktif {m.time} WIB</p>}
              {m.trackStatus==="Belum Mulai" && <p className="mt-1 text-[11px] text-[#6B7280]">Belum memulai tracking</p>}
              {m.trackStatus==="Aktif" && <p className="mt-1 text-[11px] text-emerald-400">Aktif {m.time} WIB</p>}
            </button>
          ))}
        </div>
      </div>

      {/* Rekap target mini */}
      <div className="mt-7 px-4 sm:px-6 lg:px-8">
        <div className="mb-3 flex items-start justify-between gap-3">
          <p className="min-w-0 text-[15px] font-extrabold text-[#F8FAFC]">Rekap Target Harian — {day}</p>
          <button onClick={()=>navigate("RekapOperasional")} className="shrink-0 text-[12px] font-bold text-[#D4AF37]"><span className="sm:hidden">Lihat Rekap</span><span className="hidden sm:inline">Lihat Rekap Lengkap</span></button>
        </div>
        <RekapMiniTable/>
      </div>

      {/* Day menu modal */}
      {showDayMenu && (
        <>
          <button aria-label="Tutup menu hari" onClick={()=>setShowDayMenu(false)} className="fixed inset-0 z-50 bg-black/60"/>
          <div role="dialog" aria-modal="true" aria-labelledby="day-menu-title" className="fixed inset-x-4 bottom-20 z-[60] max-w-full rounded-[20px] border border-white/[0.07] bg-[#111720] p-5 shadow-[0_16px_48px_rgba(0,0,0,.32)] lg:bottom-auto lg:left-1/2 lg:right-auto lg:top-1/2 lg:w-[calc(100%-32px)] lg:max-w-md lg:-translate-x-1/2 lg:-translate-y-1/2">
            <div className="flex items-start justify-between">
              <div>
                <p className="text-[11px] font-bold tracking-[.16em] text-[#D4AF37]">HARI OPERASIONAL</p>
                <h3 id="day-menu-title" className="mt-1 text-[20px] font-semibold text-[#F3F5F7]">Menu {day}</h3>
                <p className="mt-0.5 text-[13px] text-[#7E8794]">Pilih aktivitas yang ingin dibuka</p>
              </div>
              <button onClick={()=>setShowDayMenu(false)} className="grid size-10 place-items-center rounded-xl border border-white/10 bg-[#121820] text-[#A8B0BD]">
                <X size={18}/>
              </button>
            </div>
            <div className="mt-5 grid gap-3">
              {[
                {label:"Tracking",desc:`Lokasi marketing · ${day}`,icon:MapPin,to:"TrackingMap" as RouteName,gold:true},
                {label:"Data Anggota",desc:`Anggota aktif · ${day}`,icon:Users,to:"AnggotaList" as RouteName,gold:false},
                {label:"Laporan Operasional Harian",desc:`Laporan operasional · ${day}`,icon:FileText,to:"LaporanHarianList" as RouteName,gold:false},
                {label:"Rekap Operasional",desc:`Ringkasan lengkap · ${day}`,icon:ClipboardList,to:"RekapOperasional" as RouteName,gold:false},
              ].map(({ label, desc, icon: Icon, to, gold }) => (
                <button key={label} onClick={()=>{setShowDayMenu(false);navigate(to);}}
                  className={`flex min-h-[68px] items-center gap-4 rounded-2xl border p-4 text-left transition-colors ${gold?"border-[#D4AF37]/30 bg-[#1A1500] hover:bg-[#221C00]":"border-white/10 bg-[#121820] hover:bg-[#171E27]"}`}>
                  <div className={`grid size-11 shrink-0 place-items-center rounded-xl ${gold?"bg-[#D4AF37] text-[#07090C]":"bg-[#1A2235] text-[#D4AF37]"}`}>
                    <Icon size={20}/>
                  </div>
                  <div className="min-w-0 flex-1">
                    <p className="text-[14px] font-extrabold text-[#F8FAFC]">{label}</p>
                    <p className="mt-0.5 text-[12px] text-[#7E8794]">{desc}</p>
                  </div>
                  <ChevronRight size={16} className="text-[#D4AF37] shrink-0"/>
                </button>
              ))}
            </div>
          </div>
        </>
      )}

      {toast && (
        <div className="fixed bottom-20 right-4 z-[1400] rounded-xl border border-white/[0.07] bg-[#1A222D] px-4 py-3 text-[13px] font-semibold text-[#F3F5F7] shadow-[0_8px_24px_rgba(0,0,0,.22)] lg:bottom-6">
          <Check size={14} className="inline text-[#D4AF37] mr-1"/>
          {toast}
        </div>
      )}
    </div>
  );
}

function DailyHubPage({ day, setDay, navigate }: { day:string; setDay:(d:string)=>void; navigate:(n:RouteName,p?:Record<string,unknown>)=>void }) {
  const { prospects, visitReports } = useAdminData();
  const primaryShortcuts = [
    {label:"Tracking",desc:"Lokasi dan status marketing",icon:MapPin,to:"TrackingMap" as RouteName},
    {label:"Data Anggota",desc:"Anggota yang masuk hari ini",icon:Users,to:"AnggotaList" as RouteName},
    {label:"Laporan Operasional",desc:"Target, drop, dan storting",icon:FileText,to:"LaporanHarianList" as RouteName},
    {label:"Rekap Operasional",desc:"Ringkasan seluruh marketing",icon:ClipboardList,to:"RekapOperasional" as RouteName},
  ];
  const marketingShortcuts = [
    {label:"Data Prospek",desc:"Calon anggota dan status follow up",icon:ClipboardList,to:"ProspectList" as RouteName},
    {label:"Laporan Kunjungan",desc:"Hasil kunjungan tim lapangan",icon:MapPin,to:"VisitReportList" as RouteName},
  ];
  const activeToday=MARKETERS.filter(m=>trackingStatusFor(m,day)==="Aktif").length;
  const reportsToday=LAPORAN_DATA.filter(item=>item.hari===day);
  const membersToday=ANGGOTA_DATA.filter(item=>item.hari===day);
  const latest = [
    {title:`${membersToday[0]?.nama||"Anggota baru"} ditambahkan`,meta:`${membersToday[0]?.marketingCode||"M01"} · ${membersToday[0]?.waktuInput||"14.42 WIB"}`,icon:Users,to:"AnggotaList" as RouteName},
    {title:`Laporan ${reportsToday[0]?.marketingName||"marketing"} diterima`,meta:`${reportsToday[0]?.lokasi||"Kota Bandung"} · ${reportsToday[0]?.waktuInput||"14.35 WIB"}`,icon:FileText,to:"LaporanHarianList" as RouteName},
    {title:"Lokasi marketing diperbarui",meta:`${activeToday} marketing aktif · pembaruan terbaru`,icon:MapPin,to:"TrackingMap" as RouteName},
  ];
  return (
    <div className="pb-24 lg:pb-8">
      <section className="px-4 pb-2 pt-6 sm:px-6 lg:px-8">
        <p className="text-[12px] font-medium text-[#A7AFBA]">Pusat Data Harian</p>
        <h1 className="mt-1.5 text-[23px] font-semibold tracking-[-.03em] text-[#F3F5F7]">Operasional {day}</h1>
        <p className="mt-1 text-[13px] text-[#7E8794]">Pantau aktivitas dan data yang masuk dari tim lapangan.</p>
      </section>

      <section className="mt-4 px-4 sm:px-6 lg:px-8">
        <div className="no-scrollbar flex gap-2 overflow-x-auto pb-1">
          {DAYS.map(item=>(
            <button key={item} onClick={()=>setDay(item)}
              className={`min-h-10 shrink-0 rounded-xl border px-4 text-[12px] font-semibold transition active:scale-[.98] ${item===day?"border-[#D4AF37] bg-[#D4AF37] text-[#080B10]":"border-white/[0.07] bg-[#151C25] text-[#A7AFBA] hover:bg-[#1E2834]"}`}>
              {item}
            </button>
          ))}
        </div>
      </section>

      <section className="mt-5 px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-2 gap-3 lg:grid-cols-5">
          {[
            ["Marketing Aktif",`${activeToday}`],
            ["Anggota Baru",`${membersToday.length}`],
            ["Prospek",`${prospects.length}`],
            ["Laporan Masuk",`${reportsToday.length}`],
            ["Kunjungan",`${visitReports.length}`],
          ].map(([label,value])=>(
            <DCard key={label} cls="min-h-[100px] p-4">
              <p className="text-[11px] leading-tight text-[#7E8794]">{label}</p>
              <p className="mt-2 text-[24px] font-semibold tracking-[-.03em] text-[#F3F5F7]">{value}</p>
            </DCard>
          ))}
        </div>
      </section>

      <section className="mt-6 px-4 sm:px-6 lg:px-8">
        <div className="mb-3">
          <h2 className="text-[16px] font-semibold text-[#F3F5F7]">Aktivitas Utama</h2>
          <p className="mt-0.5 text-[12px] text-[#7E8794]">Operasional utama untuk hari {day}.</p>
        </div>
        <div className="grid grid-cols-2 gap-3 lg:grid-cols-4">
          {primaryShortcuts.map(({label,desc,icon:Icon,to})=>(
            <button key={label} onClick={()=>navigate(to)}
              className="min-h-[124px] rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:bg-[#1A222D] active:scale-[.985]">
              <span className="grid size-10 place-items-center rounded-[13px] bg-[#D4AF37]/[0.1] text-[#D4AF37]"><Icon size={19}/></span>
              <span className="mt-3 block text-[13px] font-semibold leading-tight text-[#F3F5F7]">{label}</span>
              <span className="mt-1 block text-[10.5px] leading-snug text-[#7E8794]">{desc}</span>
            </button>
          ))}
        </div>
      </section>

      <section className="mt-6 px-4 sm:px-6 lg:px-8">
        <div className="mb-3"><h2 className="text-[16px] font-semibold text-[#F3F5F7]">Pemasaran</h2><p className="mt-0.5 text-[12px] text-[#7E8794]">Prospek dan hasil kunjungan marketing.</p></div>
        <div className="grid grid-cols-2 gap-3">
          {marketingShortcuts.map(({label,desc,icon:Icon,to})=><button key={label} onClick={()=>navigate(to)} className="min-h-[112px] rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:bg-[#1A222D]"><span className="grid size-10 place-items-center rounded-[13px] bg-[#D4AF37]/[0.1] text-[#D4AF37]"><Icon size={19}/></span><span className="mt-3 block text-[13px] font-semibold text-[#F3F5F7]">{label}</span><span className="mt-1 block text-[10.5px] text-[#7E8794]">{desc}</span></button>)}
        </div>
      </section>

      <section className="mt-6 px-4 sm:px-6 lg:px-8">
        <div className="mb-3 flex items-end justify-between gap-3">
          <div>
            <h2 className="text-[16px] font-semibold text-[#F3F5F7]">Aktivitas Operasional</h2>
            <p className="mt-0.5 text-[12px] text-[#7E8794]">Pembaruan data operasional pada {day}.</p>
          </div>
        </div>
        <DCard cls="overflow-hidden">
          {latest.map(({title,meta,icon:Icon,to})=>(
            <button key={title} onClick={()=>navigate(to)}
              className="flex min-h-[76px] w-full items-center gap-3 border-b border-white/[0.06] p-4 text-left transition hover:bg-white/[0.035] active:bg-white/[0.05] last:border-0">
              <span className="grid size-10 shrink-0 place-items-center rounded-[13px] bg-white/[0.04] text-[#A7AFBA]"><Icon size={17}/></span>
              <span className="min-w-0 flex-1">
                <span className="block text-[13px] font-bold text-[#F8FAFC]">{title}</span>
                <span className="mt-1 block text-[11px] text-[#7E8794]">{meta}</span>
              </span>
              <ChevronRight size={16} className="shrink-0 text-[#657080]"/>
            </button>
          ))}
        </DCard>
      </section>
    </div>
  );
}

function RekapMiniTable() {
  return (
    <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
      {MARKETERS.slice(0,5).map(m => {
        const pct=75;
        return <DCard key={m.code} cls="p-4">
          <div className="flex items-center justify-between gap-3">
            <div><p className="text-[14px] font-extrabold text-[#F8FAFC]">{m.name}</p><p className="text-[11px] font-bold text-[#D4AF37]">{m.code}</p></div>
            <span className="text-[12px] font-extrabold text-emerald-400">{pct}%</span>
          </div>
          <div className="mt-3 h-1.5 overflow-hidden rounded-full bg-white/10"><div className="h-full rounded-full bg-[#D4AF37]" style={{width:`${pct}%`}}/></div>
          <div className="mt-3 grid grid-cols-2 gap-2 text-[11px]">
            <div><p className="text-[#7E8794]">Anggota</p><p className="font-bold text-[#F8FAFC]">{m.members}</p></div>
            <div><p className="text-[#7E8794]">Target</p><p className="font-bold text-[#F8FAFC]">{fmtJt(m.target*1_000_000)}</p></div>
            <div><p className="text-[#7E8794]">Drop</p><p className="font-bold text-[#F8FAFC]">{fmtJt(m.drop*1_000_000)}</p></div>
            <div><p className="text-[#7E8794]">Storting</p><p className="font-bold text-[#F8FAFC]">{fmtJt(m.storting*1_000_000)}</p></div>
          </div>
        </DCard>;
      })}
    </div>
  );
}

// ── Marketing List ────────────────────────────────────────────────────────────

function MarketingListPage({ navigate }: { navigate: (n:RouteName,p?:Record<string,unknown>)=>void }) {
  const [search, setSearch] = useState("");
  const [accountFilter, setAccountFilter] = useState("Semua");
  const [trackingFilter, setTrackingFilter] = useState("Semua");
  const [areaFilter, setAreaFilter] = useState("Semua");
  const filtered = MARKETERS.filter(m =>
    (accountFilter==="Semua"||m.accountStatus===accountFilter) &&
    (trackingFilter==="Semua"||m.trackStatus===trackingFilter) &&
    (areaFilter==="Semua"||m.area===areaFilter) && (
    m.name.toLowerCase().includes(search.toLowerCase()) ||
    m.code.toLowerCase().includes(search.toLowerCase()) ||
    m.area.toLowerCase().includes(search.toLowerCase()))
  );
  return (
    <div className="pb-20 lg:pb-8">
      <PageHdr title="Data Marketing" sub={`${MARKETERS.length} marketing terdaftar`}
        action={<GoldBtn onClick={()=>navigate("MarketingForm")} cls="px-3"><Plus size={16}/>Tambah</GoldBtn>}/>
      <div className="px-4 py-4 lg:px-8">
        <div className="relative mb-4">
          <Search size={16} className="absolute left-3 top-3 text-[#7E8794]"/>
          <input value={search} onChange={e=>setSearch(e.target.value)} placeholder="Cari marketing..."
            className="w-full rounded-xl border border-white/10 bg-[#121820] pl-9 pr-3 py-2.5 text-[13px] text-[#F8FAFC] placeholder:text-[#7E8794] outline-none focus:border-[#D4AF37]/50"/>
        </div>
        <div className="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-3">
          <select aria-label="Filter status akun" value={accountFilter} onChange={e=>setAccountFilter(e.target.value)} className="min-h-11 rounded-xl border border-white/10 bg-[#121820] px-3 py-2.5 text-[12px] text-[#F8FAFC]"><option>Semua</option><option>Aktif</option><option>Nonaktif</option></select>
          <select aria-label="Filter tracking" value={trackingFilter} onChange={e=>setTrackingFilter(e.target.value)} className="min-h-11 rounded-xl border border-white/10 bg-[#121820] px-3 py-2.5 text-[12px] text-[#F8FAFC]"><option>Semua</option><option>Aktif</option><option>Offline</option><option>Belum Mulai</option></select>
          <select aria-label="Filter area" value={areaFilter} onChange={e=>setAreaFilter(e.target.value)} className="col-span-2 min-h-11 rounded-xl border border-white/10 bg-[#121820] px-3 py-2.5 text-[12px] text-[#F8FAFC] sm:col-span-1">{["Semua",...MARKETERS.map(m=>m.area)].map(v=><option key={v}>{v}</option>)}</select>
        </div>
        <div className="grid gap-3 lg:grid-cols-2 xl:grid-cols-3">
          {filtered.map(m => (
            <DCard key={m.id} cls="p-4 transition hover:bg-[#151C25]">
              <div className="flex items-start justify-between gap-3">
                <div className="flex items-center gap-3 min-w-0">
                  <Ava name={m.name} cls="size-12 text-base"/>
                  <div className="min-w-0">
                    <p className="text-[11px] font-bold text-[#D4AF37]">{m.code}</p>
                    <p className="truncate text-[16px] font-extrabold text-[#F8FAFC]">{m.name}</p>
                    <p className="truncate text-[12px] text-[#7E8794]">{m.area} · {m.schedule}</p>
                  </div>
                </div>
                <div className="flex shrink-0 flex-col items-end gap-1.5">
                  <StatusBadge status={m.trackStatus}/>
                  <span className="text-[10px] font-bold text-emerald-400">{m.accountStatus}</span>
                </div>
              </div>
              <p className="mt-3 hidden text-[11px] text-[#747E8B] sm:block"><CalendarDays size={12} className="mr-1 inline text-[#A7AFBA]"/>{m.days.join(", ")}</p>
              <div className="mt-3 hidden grid-cols-3 gap-2 border-y border-white/5 py-3 text-center sm:grid">
                {[["Target",fmtJt(m.target*1_000_000)],["Drop",fmtJt(m.drop*1_000_000)],["Storting",fmtJt(m.storting*1_000_000)]].map(([l,v])=>(
                  <div key={l}><p className="text-[10px] font-bold text-[#7E8794]">{l}</p><p className="mt-0.5 text-[11px] font-extrabold text-[#F8FAFC]">{v}</p></div>
                ))}
              </div>
              <div className="mt-3 flex gap-2">
                <GhostBtn onClick={()=>navigate("MarketingDetail",{id:m.id})} cls="flex-1 text-[13px]">Detail</GhostBtn>
                <GhostBtn onClick={()=>navigate("JadwalList",{code:m.code})} cls="size-11 px-0"><CalendarDays size={16}/></GhostBtn>
                <GhostBtn onClick={()=>navigate("MarketingForm",{id:m.id})} cls="size-11 px-0"><Pencil size={16}/></GhostBtn>
              </div>
            </DCard>
          ))}
        </div>
      </div>
    </div>
  );
}

// ── Marketing Detail ──────────────────────────────────────────────────────────

function MarketingDetailPage({ id, day, navigate, back }: { id: string; day:string; navigate: (n:RouteName,p?:Record<string,unknown>)=>void; back: ()=>void }) {
  const m = MARKETERS.find(x=>x.id===id) || MARKETERS[0];
  const currentTrackingStatus = trackingStatusFor(m,day);
  const [tab, setTab] = useState("Ringkasan");
  const anggota = membersData.filter(item=>item.marketingId===m.code);
  const laporan = dailyReportsData.filter(item=>item.marketingId===m.code);
  const prospek = prospectsData.filter(item=>item.marketingId===m.code);
  const kunjungan = visitReportsData.filter(item=>item.marketingId===m.code);
  const jadwal = schedulesData.filter(item=>item.marketingId===m.code);
  const riwayat = journeys.filter(item=>item.marketingId===m.code);

  return (
    <div className="pb-4">
      <PageHdr title={`${m.code} · ${m.name}`} sub="Detail Marketing" onBack={back}
        action={<GhostBtn onClick={()=>navigate("MarketingForm",{id:m.id})}><Pencil size={15}/>Edit</GhostBtn>}/>
      {/* Profile header */}
      <div className="border-b border-white/10 bg-[#0D1117] p-4 lg:p-8">
        <div className="flex items-center gap-4">
          <Ava name={m.name} cls="size-20 text-2xl"/>
          <div>
            <div className="flex flex-wrap items-center gap-2">
              <h2 className="text-[20px] font-extrabold text-[#F8FAFC]">{m.name}</h2>
              <StatusBadge status={currentTrackingStatus}/>
            </div>
            <p className="mt-1 text-[13px] text-[#7E8794]">{m.area} · {m.schedule}</p>
            <p className="text-[12px] text-[#7E8794]">{m.phone} · @{m.username}</p>
            {currentTrackingStatus==="Aktif"&&<p className="mt-1 text-[12px] text-emerald-400">Terakhir aktif {m.time} WIB</p>}
            {currentTrackingStatus==="Offline"&&<p className="mt-1 text-[12px] text-red-400">Offline sejak {m.time} WIB</p>}
            {currentTrackingStatus==="Belum Mulai"&&<p className="mt-1 text-[12px] text-[#6B7280]">Belum memulai tracking hari ini</p>}
            {currentTrackingStatus==="Tidak Dijadwalkan"&&<p className="mt-1 text-[12px] text-[#7E8794]">Tidak dijadwalkan pada hari {day}</p>}
          </div>
        </div>
        <div className="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
          {[["Anggota",String(m.members)],["Target",fmtJt(m.target*1_000_000)],["Drop",fmtJt(m.drop*1_000_000)],["Storting",fmtJt(m.storting*1_000_000)]].map(([l,v])=>(
            <DCard key={l} cls="p-3">
              <p className="text-[11px] text-[#7E8794]">{l}</p>
              <p className="mt-1 text-[15px] font-extrabold text-[#F8FAFC]">{v}</p>
            </DCard>
          ))}
        </div>
      </div>
      {/* Tabs */}
      <TabBar tabs={["Ringkasan","Jadwal","Tracking","Prospek","Anggota","Setoran","Kunjungan","Riwayat"]} active={tab} onSelect={setTab}/>
      <div className="px-4 py-4 lg:px-8">
        {tab==="Ringkasan" && (
          <div className="space-y-4">
            <DCard cls="p-4">
              <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Informasi Marketing</p>
              <InfoRow label="Kode" value={m.code}/>
              <InfoRow label="Area / Resort" value={m.area}/>
              <InfoRow label="Jadwal" value={m.schedule}/>
              <InfoRow label="Hari Kerja" value={m.days.join(", ")}/>
              <InfoRow label="Username Marketing" value={`@${m.username}`}/>
              <InfoRow label="Nomor Telepon" value={m.phone}/>
              <InfoRow label="Status Akun" value={<span className="text-emerald-400">{m.accountStatus}</span>}/>
            </DCard>
            <DCard cls="p-4">
              <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-2">Lokasi Terakhir</p>
              <p className="mb-3 text-[12px] text-[#7E8794]">{m.area}, Kota Bandung · {m.time} WIB</p>
              <MapPH label={`Lokasi terakhir ${m.name}`} lat={Number(m.lat)} lng={Number(m.lng)}/>
              <p className="mt-2 text-[11px] text-[#7E8794]">Koordinat: {m.lat}, {m.lng}</p>
            </DCard>
            <div className="flex gap-2 flex-wrap">
              <GoldBtn onClick={()=>navigate("TrackingDetail",{id:m.id})} cls="flex-1"><Navigation size={15}/>Tracking</GoldBtn>
              <GhostBtn onClick={()=>navigate("RiwayatList",{code:m.code})} cls="flex-1"><Route size={15}/>Riwayat</GhostBtn>
              <GhostBtn onClick={()=>navigate("AnggotaList",{code:m.code})} cls="flex-1"><Users size={15}/>Anggota</GhostBtn>
            </div>
          </div>
        )}
        {tab==="Jadwal" && (
          <div className="space-y-3">
            <div className="flex justify-end"><GoldBtn onClick={()=>navigate("JadwalForm",{code:m.code})}><Plus size={15}/>Tambah Jadwal</GoldBtn></div>
            {jadwal.length===0 ? <EmptyState icon={CalendarDays} title="Belum ada jadwal" desc="Tambah jadwal untuk marketing ini"/> : jadwal.map(j=>(
              <DCard key={j.id} cls="p-4">
                <div className="flex items-start justify-between">
                  <div>
                    <div className="flex items-center gap-2 flex-wrap">
                      <p className="text-[14px] font-extrabold text-[#F8FAFC]">{j.day} · {j.date}</p>
                      <JadwalStatusBadge status={j.status}/>
                    </div>
                    <p className="mt-1 text-[12px] text-[#7E8794]">{j.startTime}–{j.endTime} · {j.area}</p>
                    <p className="text-[12px] text-[#7E8794]">{j.consumerName} · {j.agenda}</p>
                  </div>
                  <GhostBtn onClick={()=>navigate("JadwalForm",{id:j.id})} cls="size-9 min-h-0 p-0 shrink-0"><Pencil size={14}/></GhostBtn>
                </div>
              </DCard>
            ))}
          </div>
        )}
        {tab==="Tracking" && (
          <div className="space-y-4">
            <DCard cls="p-4">
              <div className="flex items-center justify-between mb-2">
                <p className="text-[14px] font-extrabold text-[#F8FAFC]">Status Tracking</p>
                <StatusBadge status={currentTrackingStatus}/>
              </div>
              {currentTrackingStatus==="Offline" && <p className="text-[13px] text-red-400">Marketing sedang offline. Lokasi terakhir diperbarui pukul {m.time}.</p>}
              {currentTrackingStatus==="Belum Mulai" && <p className="text-[13px] text-[#6B7280]">Marketing belum memulai tracking hari ini.</p>}
              {currentTrackingStatus==="Aktif" && <p className="text-[13px] text-emerald-400">Marketing aktif. Lokasi diperbarui pukul {m.time}.</p>}
              {currentTrackingStatus==="GPS Tidak Aktif" && <p className="text-[13px] text-amber-400">GPS marketing tidak aktif.</p>}
              {currentTrackingStatus==="Tidak Dijadwalkan" && <p className="text-[13px] text-[#7E8794]">Marketing tidak memiliki jadwal pada hari {day}.</p>}
            </DCard>
            <MapPH label={`Rute perjalanan ${m.name}`} lat={Number(m.lat)} lng={Number(m.lng)} route={routeFor(Number(m.lat),Number(m.lng))}/>
            <GoldBtn onClick={()=>navigate("RiwayatList",{code:m.code})} cls="w-full"><Route size={15}/>Lihat Riwayat Perjalanan</GoldBtn>
          </div>
        )}
        {tab==="Anggota" && (
          <div className="space-y-3">
            {anggota.length===0 ? <EmptyState icon={Users} title="Belum ada anggota" desc="Data anggota akan muncul di sini"/> : anggota.map(a=>(
              <button key={a.id} onClick={()=>navigate("AnggotaDetail",{id:a.id})}
                className="flex w-full items-center gap-3 rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:bg-[#1A222D]">
                <img src={a.memberPhoto} alt={a.name} className="size-12 rounded-xl object-cover border border-white/10"/>
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2 flex-wrap">
                    <p className="text-[14px] font-bold text-[#F8FAFC] truncate">{a.name}</p>
                    <span className="rounded-full bg-[#D4AF37]/10 px-2 py-1 text-[10px] text-[#E8C65A]">{a.approvalStatus}</span>
                  </div>
                  <p className="text-[12px] text-[#7E8794]">{a.memberNumber} · {a.resort}</p>
                </div>
                <ChevronRight size={16} className="text-[#7E8794] shrink-0"/>
              </button>
            ))}
          </div>
        )}
        {tab==="Setoran" && (
          <div className="space-y-3">
            {laporan.map(l=>(
              <button key={l.id} onClick={()=>navigate("LaporanHarianDetail",{id:l.id})}
                className="flex w-full items-start gap-3 rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:bg-[#1A222D]">
                <div className="grid size-10 shrink-0 place-items-center rounded-xl bg-[#1A2235] text-[#D4AF37]"><FileText size={18}/></div>
                <div className="min-w-0 flex-1">
                  <div className="flex flex-wrap items-center gap-2">
                    <p className="text-[14px] font-bold text-[#F8FAFC]">{l.day}, {l.date}</p>
                    <span className="text-[10px] text-[#E8C65A]">{l.syncStatus}</span>
                  </div>
                  <p className="mt-0.5 text-[12px] text-[#7E8794]">Storting {fmtFull(l.storting)} · Drop {fmtFull(l.drop)}</p>
                  <p className="text-[12px] text-[#7E8794]">{l.resort} · {l.time}</p>
                </div>
                <ChevronRight size={16} className="text-[#7E8794] shrink-0"/>
              </button>
            ))}
          </div>
        )}
        {tab==="Prospek" && (
          <div className="space-y-3">
            {prospek.map(item=>(
              <button key={item.id} onClick={()=>navigate("ProspectDetail",{id:item.id})}
                className="flex w-full items-center gap-3 rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:bg-[#1A222D]">
                <span className="grid size-12 shrink-0 place-items-center rounded-full bg-[#D4AF37]/10 text-[#E8C65A]">{item.name.slice(0,1)}</span>
                <div className="min-w-0 flex-1">
                  <p className="text-[14px] font-bold text-[#F8FAFC] truncate">{item.name}</p>
                  <p className="mt-0.5 text-[12px] text-[#7E8794]">{item.business} · {item.resort} · {item.status}</p>
                </div>
                <ChevronRight size={16} className="text-[#7E8794] shrink-0"/>
              </button>
            ))}
          </div>
        )}
        {tab==="Kunjungan" && (
          <div className="space-y-3">
            {kunjungan.map(item=><button key={item.id} onClick={()=>navigate("VisitReportDetail",{id:item.id})} className="flex w-full items-center gap-3 rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left">
              <img src={item.photo} alt={item.photoCaption} className="size-12 rounded-xl object-cover"/>
              <span className="min-w-0 flex-1 text-left"><strong className="block truncate text-sm text-white">{prospectsData.find(row=>row.id===item.prospectId)?.name}</strong><span className="mt-1 block text-xs text-[#7E8794]">{item.visitPurpose} · {item.visitResult}</span></span><ChevronRight size={16} className="text-[#7E8794]"/>
            </button>)}
          </div>
        )}
        {tab==="Riwayat" && (
          <div className="space-y-3">
            {riwayat.map(item=><button key={item.id} onClick={()=>navigate("RiwayatDetail",{id:item.marketingId,day:item.day})} className="w-full rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left"><strong className="text-sm text-white">{item.day} · {item.date}</strong><p className="mt-1 text-xs text-[#7E8794]">{item.visitCount} kunjungan · {item.status}</p></button>)}
          </div>
        )}
      </div>
    </div>
  );
}

// ── Marketing Form ────────────────────────────────────────────────────────────

function MarketingFormPage({ id, navigate, back, showToast }: { id?: string; navigate: (n:RouteName,p?:Record<string,unknown>)=>void; back:()=>void; showToast:(s:string)=>void }) {
  const existing = id ? MARKETERS.find(m=>m.id===id) : null;
  const [code, setCode] = useState(existing?.code||"");
  const [name, setName] = useState(existing?.name||"");
  const [area, setArea] = useState(existing?.area||"");
  const [phone, setPhone] = useState(existing?.phone||"");
  const [username, setUsername] = useState(existing?.username||"");
  const [status, setStatus] = useState<string>(existing?.accountStatus||"Aktif");
  const [workDays, setWorkDays] = useState<string[]>(existing?.days||[]);
  const [startTime, setStartTime] = useState("08:00");
  const [endTime, setEndTime] = useState("16:00");
  const [error, setError] = useState("");
  const [dialog, setDialog] = useState<""|"password"|"disable">("");
  const [newPassword, setNewPassword] = useState("");
  const [profilePhoto, setProfilePhoto] = useState(existing ? `/profiles/marketing-${existing.code.slice(1)}.jpg` : "");
  const [photoFailed, setPhotoFailed] = useState(false);

  useEffect(() => {
    if (!dialog) return;
    const close = (event: KeyboardEvent) => {
      if (event.key === "Escape") setDialog("");
    };
    window.addEventListener("keydown", close);
    return () => window.removeEventListener("keydown", close);
  }, [dialog]);

  useEffect(() => () => {
    if (profilePhoto.startsWith("blob:")) URL.revokeObjectURL(profilePhoto);
  }, [profilePhoto]);

  const selectProfilePhoto = (file?: File) => {
    if (!file) return;
    setPhotoFailed(false);
    setProfilePhoto(URL.createObjectURL(file));
  };

  const save = () => {
    if (!code.trim() || !name.trim() || !area.trim() || !phone.trim() || !username.trim()) {
      setError("Kode, nama, username, telepon, dan area wajib diisi."); return;
    }
    if (!workDays.length) { setError("Pilih minimal satu hari kerja."); return; }
    if (endTime <= startTime) { setError("Jam selesai harus setelah jam mulai."); return; }
    setError("");
    showToast(`Marketing ${name||"baru"} berhasil ${id?"diperbarui":"ditambahkan"}`);
    back();
  };

  return (
    <div className="pb-4">
      <PageHdr title={id?"Edit Marketing":"Tambah Marketing"} sub={id?"Perbarui data marketing":"Daftarkan marketing baru"} onBack={back}/>
      <div className="mx-auto max-w-5xl space-y-4 px-4 py-5 sm:px-6 lg:px-8">
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC]">Foto Profil Marketing</p>
          <p className="mt-1 text-[11px] text-[#7E8794]">Preview lokal untuk prototype. Foto belum diunggah ke server.</p>
          <div className="mt-4 flex flex-wrap items-center gap-4">
            <div className="grid size-20 shrink-0 place-items-center overflow-hidden rounded-full border-2 border-[#D4AF37]/35 bg-[#18212d] text-[24px] font-extrabold text-[#D4AF37]">
              {profilePhoto && !photoFailed
                ? <img src={profilePhoto} alt="Preview foto profil marketing" onError={()=>setPhotoFailed(true)} className="size-full object-cover"/>
                : (name.trim().slice(0,1)||code.trim().slice(0,1)||"M").toUpperCase()}
            </div>
            <div className="flex min-w-0 flex-1 flex-wrap gap-2">
              <label className="flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-[13px] bg-[#D4AF37] px-4 text-[12px] font-semibold text-[#080B10] transition hover:bg-[#E6C45A]">
                <ImageIcon size={15}/>{profilePhoto ? "Ganti Foto" : "Pilih Foto"}
                <input type="file" accept="image/*" className="sr-only" onChange={event=>{
                  selectProfilePhoto(event.target.files?.[0]);
                  event.currentTarget.value="";
                }}/>
              </label>
              {profilePhoto && (
                <button type="button" onClick={()=>{setProfilePhoto("");setPhotoFailed(false);}}
                  className="min-h-11 rounded-xl border border-white/10 bg-[#121820] px-4 text-[12px] font-bold text-[#A8B0BD] transition hover:border-red-500/30 hover:text-red-300">
                  Hapus Foto
                </button>
              )}
            </div>
          </div>
        </DCard>
        <DCard cls="p-4 space-y-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC]">Data Marketing</p>
          <div className="grid gap-4 lg:grid-cols-2">
            <Inp label="Kode Marketing" ph="Contoh: M14" value={code} onChange={setCode}/>
            <Inp label="Nama Lengkap" ph="Masukkan nama" value={name} onChange={setName}/>
            <Inp label="Area / Resort" ph="Contoh: Gedebage" value={area} onChange={setArea}/>
            <Inp label="Username Marketing" ph="Contoh: m01.deden" value={username} onChange={setUsername}/>
            <Inp label="Nomor Telepon" ph="08xx-xxxx-xxxx" value={phone} onChange={setPhone}/>
            {!id && <Inp label="Password Awal" type="password" ph="Password awal marketing"/>}
            <Sel label="Status Akun" opts={["Aktif","Nonaktif"]} value={status} onChange={setStatus}/>
          </div>
        </DCard>
        <DCard cls="p-4 space-y-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC]">Jadwal Hari Kerja</p>
          <div className="flex flex-wrap gap-2">
            {DAYS.map(d=>(
              <button key={d} onClick={()=>setWorkDays(prev=>prev.includes(d)?prev.filter(item=>item!==d):[...prev,d])}
                className={`rounded-xl border px-3 py-2 text-[12px] font-bold transition-colors ${workDays.includes(d)?"border-[#D4AF37] bg-[#D4AF37] text-[#07090C]":"border-white/10 bg-[#121820] text-[#A8B0BD] hover:border-[#D4AF37]/40"}`}>{d}</button>
            ))}
          </div>
          <div className="grid gap-4 lg:grid-cols-2">
            <Inp label="Jam Mulai" type="time" value={startTime} onChange={setStartTime}/>
            <Inp label="Jam Selesai" type="time" value={endTime} onChange={setEndTime}/>
          </div>
        </DCard>
        {error && <p role="alert" className="rounded-xl border border-red-500/30 bg-red-500/10 p-3 text-[12px] font-bold text-red-400">{error}</p>}
        <div className="flex gap-3">
          <GhostBtn onClick={back} cls="flex-1">Batal</GhostBtn>
          <GoldBtn onClick={save} cls="flex-1"><Check size={15}/>Simpan</GoldBtn>
        </div>
        {id && (
          <div className="flex gap-3">
            <GhostBtn onClick={()=>setDialog("password")} cls="flex-1"><Lock size={15}/>Reset Password</GhostBtn>
            <GhostBtn onClick={()=>setDialog("disable")} danger cls="flex-1"><UserX size={15}/>Nonaktifkan</GhostBtn>
          </div>
        )}
      </div>
      {dialog && <>
        <button aria-label="Tutup dialog" onClick={()=>setDialog("")} className="fixed inset-0 z-50 bg-black/60"/>
        <div role="dialog" aria-modal="true" className="fixed inset-x-4 top-1/2 z-[60] mx-auto max-w-sm -translate-y-1/2 rounded-[20px] border border-white/[0.07] bg-[#111720] p-5 shadow-[0_16px_48px_rgba(0,0,0,.32)]">
          <p className="text-[17px] font-semibold text-[#F3F5F7]">{dialog==="password"?"Reset Password Marketing":"Nonaktifkan Akun?"}</p>
          {dialog==="password" ? <Inp label="Password baru (minimal 8 karakter)" type="password" value={newPassword} onChange={setNewPassword} cls="mt-4"/> :
            <p className="mt-2 text-[13px] text-[#7E8794]">Akun {existing?.name} tidak dihapus dan dapat diaktifkan kembali.</p>}
          <div className="mt-5 flex gap-3">
            <GhostBtn onClick={()=>setDialog("")} cls="flex-1">Batal</GhostBtn>
            <GoldBtn disabled={dialog==="password"&&newPassword.length<8} onClick={()=>{
              if(dialog==="password") showToast("Password marketing berhasil direset");
              else { setStatus("Nonaktif"); showToast("Akun marketing dinonaktifkan"); }
              setDialog("");
            }} cls="flex-1">Konfirmasi</GoldBtn>
          </div>
        </div>
      </>}
    </div>
  );
}

// ── Rekap Operasional ─────────────────────────────────────────────────────────

function RekapOperasionalPage({ day, back }: { day:string; back:()=>void }) {
  const [tab, setTab] = useState("Ringkasan");
  const tabs = ["Ringkasan","Anggota","Target","Drop","Storting","Lainnya"];
  const dailyMarketers = MARKETERS.filter(marketing=>marketing.days.includes(day));
  const dailyMembers = ANGGOTA_DATA.filter(member=>member.hari===day).length;
  const dailyTarget = dailyMarketers.reduce((total,marketing)=>total+marketing.target*1_000_000,0);
  const dailyDrop = dailyMarketers.reduce((total,marketing)=>total+marketing.drop*1_000_000,0);
  const dailyStorting = dailyMarketers.reduce((total,marketing)=>total+marketing.storting*1_000_000,0);
  const dailyMemberCountFor = (marketingCode:string) =>
    ANGGOTA_DATA.filter(member=>member.hari===day&&member.marketingCode===marketingCode).length;
  return (
    <div className="pb-4">
      <PageHdr title={`Rekap Operasional — ${day}`} sub={`Ringkasan data marketing pada hari ${day}.`} onBack={back}/>
      {/* Mobile tabs */}
      <div className="lg:hidden">
        <TabBar tabs={tabs} active={tab} onSelect={setTab}/>
        <div className="px-4 py-4">
          {tab==="Ringkasan" && (
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-3">
                {[["Total Anggota",String(dailyMembers)],["Total Target",fmtFull(dailyTarget)],["Total Drop",fmtFull(dailyDrop)],["Total Storting",fmtFull(dailyStorting)]].map(([l,v])=>(
                  <DCard key={l} cls="overflow-hidden p-4">
                    <p className="text-[12px] text-[#7E8794]">{l}</p>
                    <p className="mt-1 break-words text-[15px] font-extrabold leading-tight text-[#F8FAFC]">{v}</p>
                  </DCard>
                ))}
              </div>
              {dailyMarketers.map(m=>(
                <DCard key={m.id} cls="p-4">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      <Ava name={m.name} cls="size-9 text-xs"/>
                      <div><p className="text-[13px] font-bold text-[#F8FAFC]">{m.name}</p><p className="text-[11px] text-[#D4AF37]">{m.code}</p></div>
                    </div>
                    <StatusBadge status={m.trackStatus}/>
                  </div>
                  <div className="mt-3 grid grid-cols-2 gap-2 text-center text-[11px] sm:grid-cols-4">
                    <div><p className="text-[#7E8794]">Anggota</p><p className="font-bold text-[#F8FAFC]">{dailyMemberCountFor(m.code)}</p></div>
                    <div><p className="text-[#7E8794]">Target</p><p className="font-bold text-[#F8FAFC]">{fmtJt(m.target*1_000_000)}</p></div>
                    <div><p className="text-[#7E8794]">Drop</p><p className="font-bold text-[#F8FAFC]">{fmtJt(m.drop*1_000_000)}</p></div>
                    <div><p className="text-[#7E8794]">Storting</p><p className="font-bold text-[#F8FAFC]">{fmtJt(m.storting*1_000_000)}</p></div>
                  </div>
                </DCard>
              ))}
            </div>
          )}
          {tab!=="Ringkasan" && <div className="space-y-3">
            {dailyMarketers.map(m=><DCard key={m.code} cls="p-4">
              <div className="mb-2 flex items-center justify-between"><p className="font-bold text-[#F8FAFC]">{m.name}</p><span className="text-[12px] font-bold text-[#D4AF37]">{m.code}</span></div>
              {tab==="Anggota"&&<><InfoRow label="MG" value={dailyMemberCountFor(m.code)}/><InfoRow label="L" value="2"/><InfoRow label="M" value="1"/><InfoRow label="K" value="1"/><InfoRow label="S" value="0"/></>}
              {tab==="Target"&&<><InfoRow label="Target Lalu" value={fmtJt(m.target*1_000_000)}/><InfoRow label="MSK" value={fmtJt(1_000_000)}/><InfoRow label="KLR" value="0"/><InfoRow label="S" value={fmtJt(m.target*1_000_000)}/></>}
              {tab==="Drop"&&<><InfoRow label="Drop Lalu" value={fmtJt(m.drop*1_000_000)}/><InfoRow label="Drop Kini" value={fmtJt(1_000_000)}/><InfoRow label="Total" value={fmtJt(m.drop*1_000_000)}/></>}
              {tab==="Storting"&&<><InfoRow label="Storting Lalu" value={fmtJt(m.storting*1_000_000)}/><InfoRow label="Storting Kini" value={fmtJt(1_000_000)}/><InfoRow label="Total" value={fmtJt(m.storting*1_000_000)}/></>}
              {tab==="Lainnya"&&<><InfoRow label="Persentase" value="75%"/><InfoRow label="Sirkulasi Lalu" value={fmtJt((m.target+m.drop)*1_000_000)}/><InfoRow label="Sirkulasi Sekarang" value={fmtJt((m.target+m.drop)*1_000_000)}/><InfoRow label="Dibuat Oleh" value="Admin KSP"/><InfoRow label="Keterangan" value="—"/></>}
            </DCard>)}
          </div>}
          {false && tab==="Anggota" && (
            <DCard cls="overflow-hidden">
              <div className="overflow-x-auto no-scrollbar">
                <table className="w-full text-left text-[12px]">
                  <thead><tr className="bg-[#0D1117] border-b border-white/10">{["Marketing","Kode","MG","L","M","K","S"].map(h=><th key={h} className="px-3 py-2.5 text-[#D4AF37] font-bold whitespace-nowrap">{h}</th>)}</tr></thead>
                  <tbody>{MARKETERS.map(m=><tr key={m.code} className="border-b border-white/5 hover:bg-white/3"><td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{m.name}</td><td className="px-3 py-2.5 text-[#D4AF37] font-bold">{m.code}</td><td className="px-3 py-2.5 text-[#F8FAFC]">{m.members}</td><td className="px-3 py-2.5 text-[#F8FAFC]">2</td><td className="px-3 py-2.5 text-[#F8FAFC]">1</td><td className="px-3 py-2.5 text-[#F8FAFC]">1</td><td className="px-3 py-2.5 text-[#F8FAFC]">0</td></tr>)}</tbody>
                </table>
              </div>
            </DCard>
          )}
          {false && tab==="Target" && (
            <DCard cls="overflow-hidden">
              <div className="overflow-x-auto no-scrollbar">
                <table className="w-full text-left text-[12px]">
                  <thead><tr className="bg-[#0D1117] border-b border-white/10">{["Marketing","Kode","Target Lalu","MSK","KLR","S"].map(h=><th key={h} className="px-3 py-2.5 text-[#D4AF37] font-bold whitespace-nowrap">{h}</th>)}</tr></thead>
                  <tbody>{MARKETERS.map(m=><tr key={m.code} className="border-b border-white/5 hover:bg-white/3"><td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{m.name}</td><td className="px-3 py-2.5 text-[#D4AF37] font-bold">{m.code}</td><td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(m.target*1_000_000)}</td><td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(1_000_000)}</td><td className="px-3 py-2.5 text-[#F8FAFC]">0</td><td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{fmtJt(m.target*1_000_000)}</td></tr>)}</tbody>
                </table>
              </div>
            </DCard>
          )}
          {false && tab==="Drop" && (
            <DCard cls="overflow-hidden">
              <div className="overflow-x-auto no-scrollbar">
                <table className="w-full text-left text-[12px]">
                  <thead><tr className="bg-[#0D1117] border-b border-white/10">{["Marketing","Kode","Drop Lalu","Drop Kini","Total"].map(h=><th key={h} className="px-3 py-2.5 text-[#D4AF37] font-bold whitespace-nowrap">{h}</th>)}</tr></thead>
                  <tbody>{MARKETERS.map(m=><tr key={m.code} className="border-b border-white/5 hover:bg-white/3"><td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{m.name}</td><td className="px-3 py-2.5 text-[#D4AF37] font-bold">{m.code}</td><td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(m.drop*1_000_000)}</td><td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(1_000_000)}</td><td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{fmtJt(m.drop*1_000_000)}</td></tr>)}</tbody>
                </table>
              </div>
            </DCard>
          )}
          {false && tab==="Storting" && (
            <DCard cls="overflow-hidden">
              <div className="overflow-x-auto no-scrollbar">
                <table className="w-full text-left text-[12px]">
                  <thead><tr className="bg-[#0D1117] border-b border-white/10">{["Marketing","Kode","Storting Lalu","Storting Kini","Total"].map(h=><th key={h} className="px-3 py-2.5 text-[#D4AF37] font-bold whitespace-nowrap">{h}</th>)}</tr></thead>
                  <tbody>{MARKETERS.map(m=><tr key={m.code} className="border-b border-white/5 hover:bg-white/3"><td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{m.name}</td><td className="px-3 py-2.5 text-[#D4AF37] font-bold">{m.code}</td><td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(m.storting*1_000_000)}</td><td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(1_000_000)}</td><td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{fmtJt(m.storting*1_000_000)}</td></tr>)}</tbody>
                </table>
              </div>
            </DCard>
          )}
          {false && tab==="Lainnya" && (
            <DCard cls="overflow-hidden">
              <div className="overflow-x-auto no-scrollbar">
                <table className="w-full text-left text-[12px]">
                  <thead><tr className="bg-[#0D1117] border-b border-white/10">{["Marketing","Kode","% Realisasi","Dibuat Oleh","Keterangan"].map(h=><th key={h} className="px-3 py-2.5 text-[#D4AF37] font-bold whitespace-nowrap">{h}</th>)}</tr></thead>
                  <tbody>{MARKETERS.map(m=>{const pct=75;return(<tr key={m.code} className="border-b border-white/5 hover:bg-white/3"><td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{m.name}</td><td className="px-3 py-2.5 text-[#D4AF37] font-bold">{m.code}</td><td className="px-3 py-2.5"><span className="font-bold text-emerald-400">{pct}%</span></td><td className="px-3 py-2.5 text-[#F8FAFC]">Admin KSP</td><td className="px-3 py-2.5 text-[#7E8794]">—</td></tr>);})}</tbody>
                </table>
              </div>
            </DCard>
          )}
        </div>
      </div>
      {/* Desktop full table */}
      <div className="hidden px-8 py-6 lg:block">
        <div className="mb-3 flex items-center justify-between gap-4">
          <div>
            <p className="text-[13px] font-bold text-[#F8FAFC]">Rincian Marketing Hari {day}</p>
            <p className="mt-0.5 text-[11px] text-[#7E8794]">Kolom identitas tetap terlihat saat tabel digeser horizontal.</p>
          </div>
          <span className="rounded-full border border-[#D4AF37]/20 bg-[#D4AF37]/[0.07] px-3 py-1.5 text-[11px] font-bold text-[#D4AF37]">{dailyMarketers.length} Marketing</span>
        </div>
        <DCard cls="overflow-hidden">
          <div className="operation-table-scroll w-full min-w-0 max-w-full overflow-x-auto">
            <table className="operation-table w-full min-w-[1840px] text-left text-[12px]">
              <thead>
                <tr className="bg-[#0A0D12]">
                  <th colSpan={3} className="border-r border-white/10 px-4 py-3 text-[#D4AF37] font-bold">IDENTITAS</th>
                  <th colSpan={5} className="border-r border-white/10 px-4 py-3 text-[#D4AF37] font-bold">ANGGOTA</th>
                  <th colSpan={4} className="border-r border-white/10 px-4 py-3 text-[#D4AF37] font-bold">TARGET</th>
                  <th colSpan={3} className="border-r border-white/10 px-4 py-3 text-[#D4AF37] font-bold">DROP</th>
                  <th colSpan={3} className="border-r border-white/10 px-4 py-3 text-[#D4AF37] font-bold">STORTING</th>
                  <th colSpan={5} className="px-4 py-3 text-[#D4AF37] font-bold">LAINNYA</th>
                </tr>
                <tr className="border-b border-white/10 bg-[#121820]">
                  {["No","Marketing","Kode","MG","L","M","K","S","Lalu","MSK","KLR","S","Lalu","Kini","Total","Lalu","Kini","Total","Sirk. Lalu","Sirk. Sekarang","%","Dibuat Oleh","Ket"].map(h=>(
                    <th key={h} className="whitespace-nowrap border-r border-white/5 px-4 py-3 text-[#8d97a6] font-bold last:border-none">{h}</th>
                  ))}
                </tr>
              </thead>
              <tbody>
                {dailyMarketers.map((m,i)=>{
                  const pct=75;
                  return (
                    <tr key={m.code} className={`border-b border-white/[0.055] transition-colors hover:bg-white/[0.035] ${i%2===1?"bg-white/[0.012]":""}`}>
                      <td className="px-3 py-2.5 text-[#7E8794]">{i+1}</td>
                      <td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{m.name}</td>
                      <td className="px-3 py-2.5 font-bold text-[#D4AF37]">{m.code}</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">{dailyMemberCountFor(m.code)}</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">2</td><td className="px-3 py-2.5 text-[#F8FAFC]">1</td><td className="px-3 py-2.5 text-[#F8FAFC]">1</td><td className="px-3 py-2.5 text-[#F8FAFC]">0</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(m.target*1_000_000)}</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(1_000_000)}</td><td className="px-3 py-2.5 text-[#F8FAFC]">0</td>
                      <td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{fmtJt(m.target*1_000_000)}</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(m.drop*1_000_000)}</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(1_000_000)}</td>
                      <td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{fmtJt(m.drop*1_000_000)}</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(m.storting*1_000_000)}</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt(1_000_000)}</td>
                      <td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{fmtJt(m.storting*1_000_000)}</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt((m.target+m.drop)*1_000_000)}</td>
                      <td className="px-3 py-2.5 text-[#F8FAFC]">{fmtJt((m.target+m.drop)*1_000_000)}</td>
                      <td className="px-3 py-2.5"><span className={`font-bold ${pct>=70?"text-emerald-400":"text-amber-400"}`}>{pct}%</span></td>
                      <td className="px-3 py-2.5 font-bold text-[#F8FAFC]">{m.code} {m.name}</td>
                      <td className="px-3 py-2.5 text-[#7E8794]">—</td>
                    </tr>
                  );
                })}
                <tr className="operation-total bg-[#0D1117] border-t-2 border-[#D4AF37]/30 font-bold">
                  <td colSpan={3} className="px-3 py-3 text-[#D4AF37] font-extrabold">TOTAL</td>
                  <td className="px-3 py-3 text-[#F8FAFC]">{dailyMembers}</td>
                  <td colSpan={4} className="px-3 py-3 text-[#7E8794]">—</td>
                  <td colSpan={3} className="px-3 py-3 text-[#7E8794]">—</td>
                  <td className="px-3 py-3 font-bold text-[#F8FAFC]">{fmtFull(dailyTarget)}</td>
                  <td colSpan={2} className="px-3 py-3 text-[#7E8794]">—</td>
                  <td className="px-3 py-3 font-bold text-[#F8FAFC]">{fmtFull(dailyDrop)}</td>
                  <td colSpan={2} className="px-3 py-3 text-[#7E8794]">—</td>
                  <td className="px-3 py-3 font-bold text-[#F8FAFC]">{fmtFull(dailyStorting)}</td>
                  <td colSpan={5}/>
                </tr>
              </tbody>
            </table>
          </div>
        </DCard>
      </div>
    </div>
  );
}

// ── Rekap Detail Pages ────────────────────────────────────────────────────────

function RekapDetailPage({ type, back, navigate }: { type:"Anggota"|"Target"|"Drop"|"Storting"; back:()=>void; navigate:(n:RouteName,p?:Record<string,unknown>)=>void }) {
  const cfg = {
    Anggota:{ title:"Detail Total Anggota", sub:"39 anggota aktif", color:"text-emerald-400", total:"39 Anggota" },
    Target:{ title:"Detail Total Target", sub:fmtFull(TOTAL_TARGET), color:"text-[#D4AF37]", total:fmtFull(TOTAL_TARGET) },
    Drop:{ title:"Detail Total Drop", sub:fmtFull(TOTAL_DROP), color:"text-blue-400", total:fmtFull(TOTAL_DROP) },
    Storting:{ title:"Detail Total Storting", sub:fmtFull(TOTAL_STORTING), color:"text-purple-400", total:fmtFull(TOTAL_STORTING) },
  };
  const c = cfg[type];
  const shareText = encodeURIComponent(`Rekap ${type} Harian\nTotal: ${c.total}`);
  return (
    <div className="pb-4">
      <PageHdr title="Rekap Operasional Keseluruhan" sub={`Ringkasan total seluruh marketing · ${type}`} onBack={back}
        action={
          <div className="flex items-center gap-1.5 print:hidden">
            <button onClick={() => window.print()} className="grid size-9 place-items-center rounded-xl border border-white/[0.08] bg-white/[0.035] text-[#AAB2BE] hover:bg-white/10 hover:text-white transition" title="Cetak"><Printer size={16}/></button>
            <button onClick={() => window.open(`https://wa.me/?text=${shareText}`, '_blank')} className="grid size-9 place-items-center rounded-xl border border-white/[0.08] bg-white/[0.035] text-[#AAB2BE] hover:bg-[#25D366]/20 hover:text-[#25D366] transition" title="Share WA"><Share2 size={16}/></button>
          </div>
        }/>
      <div className="px-4 py-4 lg:px-8">
        <DCard cls="mb-4 p-5 flex items-center justify-between">
          <div><p className="text-[13px] text-[#7E8794]">Total Keseluruhan</p><p className={`mt-1 text-[24px] font-extrabold ${c.color}`}>{c.total}</p></div>
          <div className="text-[#D4AF37] opacity-20 text-[60px] font-extrabold">{type[0]}</div>
        </DCard>
        <div className="grid gap-3">
          {MARKETERS.map(m => {
            const val = type==="Anggota" ? `${m.members} anggota` : type==="Target" ? fmtFull(m.target*1_000_000) : type==="Drop" ? fmtFull(m.drop*1_000_000) : fmtFull(m.storting*1_000_000);
            const pct = type==="Anggota" ? Math.round((m.members/TOTAL_ANGGOTA)*100) : type==="Target" ? Math.round((m.target*1_000_000/TOTAL_TARGET)*100) : type==="Drop" ? Math.round((m.drop*1_000_000/TOTAL_DROP)*100) : Math.round((m.storting*1_000_000/TOTAL_STORTING)*100);
            return (
              <button key={m.id} onClick={()=>navigate("MarketingDetail",{id:m.id})}
                className="flex items-center gap-3 rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:bg-[#1A222D]">
                <Ava name={m.name} cls="size-10 text-sm"/>
                <div className="min-w-0 flex-1">
                  <div className="flex items-center justify-between">
                    <p className="text-[14px] font-bold text-[#F8FAFC]">{m.name} <span className="text-[#D4AF37]">{m.code}</span></p>
                    <p className={`text-[14px] font-extrabold ${c.color}`}>{val}</p>
                  </div>
                  <div className="mt-2 h-1.5 w-full rounded-full bg-white/10">
                    <div className="h-1.5 rounded-full bg-[#D4AF37]" style={{width:`${pct}%`}}/>
                  </div>
                  <p className="mt-1 text-[11px] text-[#7E8794]">{pct}% dari total</p>
                </div>
              </button>
            );
          })}
        </div>
      </div>
    </div>
  );
}

// ── Anggota List ──────────────────────────────────────────────────────────────

function AnggotaListPage({ day, navigate, back, filterCode }: { day:string; navigate:(n:RouteName,p?:Record<string,unknown>)=>void; back:()=>void; filterCode?:string }) {
  const [search, setSearch] = useState("");
  const [accFilter, setAccFilter] = useState("Semua");
  const [mCode, setMCode] = useState(filterCode||"Semua");
  const filtered = ANGGOTA_DATA.filter(a => a.hari===day &&
    (accFilter==="Semua"||a.statusACC===accFilter) &&
    (mCode==="Semua"||a.marketingCode===mCode) &&
    (search===""||a.nama.toLowerCase().includes(search.toLowerCase())||a.noAnggota.includes(search)||a.noPinjaman.includes(search))
  );
  return (
    <div className="pb-4">
      <PageHdr title={`Data Anggota — ${day}`} sub={`${filtered.length} anggota pada hari ${day}`} onBack={back}/>
      <div className="px-4 py-4 lg:px-8 space-y-3">
        <div className="relative">
          <Search size={16} className="absolute left-3 top-3 text-[#7E8794]"/>
          <input value={search} onChange={e=>setSearch(e.target.value)} placeholder="Cari nama / nomor anggota..."
            className="w-full rounded-xl border border-white/10 bg-[#121820] pl-9 pr-3 py-2.5 text-[13px] text-[#F8FAFC] placeholder:text-[#7E8794] outline-none focus:border-[#D4AF37]/50"/>
        </div>
        <div className="grid grid-cols-2 gap-2">
          <select value={accFilter} onChange={e=>setAccFilter(e.target.value)}
            className="min-h-11 rounded-xl border border-white/10 bg-[#121820] px-3 py-2 text-[12px] text-[#F8FAFC] outline-none">
            {["Semua","Disetujui","Menunggu","Ditolak"].map(v=><option key={v} value={v}>{v}</option>)}
          </select>
          <select value={mCode} onChange={e=>setMCode(e.target.value)}
            className="min-h-11 rounded-xl border border-white/10 bg-[#121820] px-3 py-2 text-[12px] text-[#F8FAFC] outline-none">
            <option value="Semua">Semua Marketing</option>
            {MARKETERS.map(m=><option key={m.code} value={m.code}>{m.code} {m.name}</option>)}
          </select>
          <button onClick={()=>{setSearch("");setAccFilter("Semua");setMCode("Semua");}}
            className="min-h-11 rounded-xl border border-white/10 bg-[#121820] px-3 py-2 text-[12px] font-bold text-[#7E8794] hover:text-[#A8B0BD]">Reset</button>
        </div>
        {filtered.length===0 ? <EmptyState icon={Users} title={`Belum ada data anggota pada hari ${day}.`} desc="Coba ubah pencarian, status, atau marketing."/> : (
          <div className="space-y-3">
            {filtered.map(a => (
              <button key={a.id} onClick={()=>navigate("AnggotaDetail",{id:a.id})}
                className="flex min-h-[108px] w-full items-center gap-3 rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:bg-[#1A222D] active:scale-[.99]">
                <img src={a.foto} alt={a.nama} className="size-14 shrink-0 rounded-[14px] border border-white/10 object-cover"/>
                <div className="min-w-0 flex-1">
                  <div className="flex flex-wrap items-center gap-1.5">
                    <p className="text-[14px] font-bold text-[#F8FAFC] truncate">{a.nama}</p>
                    <AccBadge status={a.statusACC}/>
                  </div>
                  <p className="mt-0.5 text-[12px] text-[#D4AF37]">{a.noAnggota} · {a.noPinjaman}</p>
                  <p className="text-[11px] text-[#7E8794]">{a.marketingCode} {a.marketingName} · {a.resort} · {a.hari}</p>
                </div>
                <ChevronRight size={16} className="text-[#7E8794] shrink-0"/>
              </button>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

// ── Anggota Detail ────────────────────────────────────────────────────────────

function AnggotaDetailPage({ id, back, navigate }: { id:string; back:()=>void; navigate:(n:RouteName,p?:Record<string,unknown>)=>void }) {
  const a = ANGGOTA_DATA.find(x=>x.id===id) || ANGGOTA_DATA[0];
  const [accStatus, setAccStatus] = useState<AccStatus>(a.statusACC);
  const [savingAcc, setSavingAcc] = useState(false);
  const [saved, setSaved] = useState(false);
  const { updateMemberAccStatus } = useAdminData();
  const saveAcc = async () => {
    setSavingAcc(true);
    await updateMemberAccStatus(a.id, accStatus);
    setSavingAcc(false); setSaved(true);
    setTimeout(()=>setSaved(false), 2200);
  };
  return (
    <div className="pb-4">
      <PageHdr title="Detail Anggota" sub={a.noAnggota} onBack={back}/>
      <div className="mx-auto max-w-6xl space-y-4 px-4 py-5 sm:px-6 lg:px-8">
        {/* Photos */}
        <div className="grid grid-cols-1 gap-3">
          <Photo src={a.foto} label="Foto Anggota"/>
        </div>
        {/* Informasi sinkronisasi */}
        <DCard cls="p-4">
          <div className="flex items-center gap-2 mb-3">
            <SyncBadge status={a.statusSync}/>
            <p className="text-[11px] text-[#7E8794] ml-1">Diterima {a.waktuInput}</p>
          </div>
          <InfoRow label="Nama" value={a.nama}/>
          <InfoRow label="No. Anggota" value={a.noAnggota}/>
          <InfoRow label="No. Pinjaman" value={a.noPinjaman}/>
          <InfoRow label="Resort" value={a.resort}/>
          <InfoRow label="Hari" value={a.hari}/>
          <InfoRow label="Tanggal" value={a.tanggal}/>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Informasi Pribadi</p>
          <InfoRow label="Alamat" value={a.alamat}/>
          <InfoRow label="No. HP" value={a.noHP}/>
          <InfoRow label="Usaha" value={a.usaha}/>
          <InfoRow label="Jaminan" value={a.jaminan}/>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Informasi Pinjaman</p>
          <InfoRow label="Jumlah Pinjaman" value={fmtFull(a.pinjaman)}/>
          <InfoRow label="Angsuran" value={fmtFull(a.angsuran)}/>
          <InfoRow label="Asuransi" value={fmtFull(a.asuransi)}/>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Status Persetujuan</p>
          <div className="flex gap-2 flex-wrap">
            {(["Menunggu","Disetujui","Ditolak"] as AccStatus[]).map(s => (
              <button key={s} onClick={()=>setAccStatus(s)}
                className={`rounded-xl border px-4 py-2 text-[13px] font-bold transition-colors ${accStatus===s?"border-[#D4AF37] bg-[#D4AF37] text-[#07090C]":"border-white/10 bg-[#171E27] text-[#A8B0BD]"}`}>
                {s}
              </button>
            ))}
          </div>
          <p className="mt-2 text-[11px] text-[#7E8794]">Status saat ini: <AccBadge status={accStatus}/></p>
          <GoldBtn onClick={saveAcc} disabled={savingAcc} cls="mt-4 w-full">
            {savingAcc?<RefreshCw size={15} className="animate-spin"/>:<Check size={15}/>}
            {savingAcc?"Menyimpan...":"Simpan Status"}
          </GoldBtn>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Lokasi Tercatat</p>
          <MapPH label={`Lokasi anggota: ${a.alamatLokasi}`} lat={Number(a.lat)} lng={Number(a.lng)}/>
          <InfoRow label="Koordinat" value={`${a.lat}, ${a.lng}`}/>
          <InfoRow label="Alamat GPS" value={a.alamatLokasi}/>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Marketing Pembuat</p>
          <div className="flex items-center gap-3">
            <Ava name={a.marketingName} cls="size-10 text-sm"/>
            <div>
              <p className="text-[14px] font-bold text-[#F8FAFC]">{a.marketingName}</p>
              <p className="text-[12px] text-[#D4AF37]">{a.marketingCode} · {a.resort}</p>
            </div>
          </div>
        </DCard>
      </div>
      {saved && <div className="fixed bottom-20 right-4 z-[1400] rounded-xl border border-[#D4AF37]/30 bg-[#121820] px-4 py-3 text-[13px] font-bold">Status persetujuan berhasil diperbarui</div>}
    </div>
  );
}

// ── Laporan Harian List ───────────────────────────────────────────────────────

function LaporanHarianListPage({ day, navigate, back }: { day:string; navigate:(n:RouteName,p?:Record<string,unknown>)=>void; back:()=>void }) {
  const [search, setSearch] = useState("");
  const filtered = LAPORAN_DATA.filter(l => l.hari===day && (
    l.marketingName.toLowerCase().includes(search.toLowerCase()) ||
    l.marketingCode.toLowerCase().includes(search.toLowerCase())
  ));
  return (
    <div className="pb-20 lg:pb-8">
      <PageHdr title="Laporan Operasional Harian" sub={`${filtered.length} laporan · ${day}`} onBack={back}/>
      <div className="px-4 py-4 lg:px-8 space-y-3">
        <div className="relative">
          <Search size={16} className="absolute left-3 top-3 text-[#7E8794]"/>
          <input value={search} onChange={e=>setSearch(e.target.value)} placeholder="Cari marketing..."
            className="w-full rounded-xl border border-white/10 bg-[#121820] pl-9 pr-3 py-2.5 text-[13px] text-[#F8FAFC] placeholder:text-[#7E8794] outline-none focus:border-[#D4AF37]/50"/>
        </div>
        {filtered.length===0 ? <EmptyState icon={ClipboardList} title="Laporan tidak ditemukan" desc="Belum ada laporan yang masuk"/> : filtered.map(l => (
          <button key={l.id} onClick={()=>navigate("LaporanHarianDetail",{id:l.id})}
            className="flex w-full items-start gap-3 rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:bg-[#1A222D]">
            <div className="grid size-12 shrink-0 place-items-center rounded-lg bg-[#1A2235] text-[#D4AF37]"><FileText size={18}/></div>
            <div className="min-w-0 flex-1">
              <div className="flex flex-wrap items-center gap-1.5">
                <p className="text-[14px] font-bold text-[#F8FAFC]">{l.marketingName}</p>
                <span className="text-[11px] font-bold text-[#D4AF37]">{l.marketingCode}</span>
                <SyncBadge status={l.statusSync}/>
              </div>
              <p className="mt-0.5 text-[12px] text-[#7E8794]">{l.hari} · {l.tanggal} · {l.waktuInput}</p>
              <div className="mt-1.5 flex flex-wrap gap-3">
                <span className="text-[11px] text-[#A8B0BD]">Storting: <b className="text-[#F8FAFC]">{fmtFull(l.storting)}</b></span>
                <span className="text-[11px] text-[#A8B0BD]">Drop: <b className="text-[#F8FAFC]">{fmtFull(l.drop)}</b></span>
              </div>
              <p className="mt-1 text-[11px] text-[#7E8794]"><MapPin size={11} className="inline"/>{l.lokasi}</p>
            </div>
            <ChevronRight size={16} className="text-[#7E8794] shrink-0 mt-1"/>
          </button>
        ))}
      </div>
    </div>
  );
}

// ── Laporan Harian Detail ─────────────────────────────────────────────────────

function LaporanHarianDetailPage({ id, back, navigate }: { id:string; back:()=>void; navigate:(n:RouteName,p?:Record<string,unknown>)=>void }) {
  const l = LAPORAN_DATA.find(x=>x.id===id) || LAPORAN_DATA[0];
  const reportMarketing = MARKETERS.find(item=>item.code===l.marketingCode) || MARKETERS[0];
  return (
    <div className="pb-4">
      <PageHdr title="Detail Laporan Harian" sub={`${l.marketingCode} ${l.marketingName}`} onBack={back}/>
      <div className="mx-auto max-w-6xl space-y-4 px-4 py-5 sm:px-6 lg:px-8">
        <DCard cls="p-4">
          <div className="flex flex-wrap items-center gap-2 mb-3">
            <SyncBadge status={l.statusSync}/><p className="text-[11px] text-[#7E8794]">{l.waktuInput}</p>
          </div>
          <div className="flex items-center gap-3 mb-3">
            <Ava name={l.marketingName} cls="size-10 text-sm"/>
            <div>
              <p className="text-[14px] font-bold text-[#F8FAFC]">{l.marketingName}</p>
              <p className="text-[12px] text-[#D4AF37]">{l.marketingCode} · {l.hari}, {l.tanggal}</p>
            </div>
          </div>
          <InfoRow label="Hari" value={l.hari}/>
          <InfoRow label="Tanggal" value={l.tanggal}/>
          <InfoRow label="Waktu Input" value={l.waktuInput}/>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Data Keuangan</p>
          <InfoRow label="Storting" value={fmtFull(l.storting)}/>
          <InfoRow label="Drop" value={fmtFull(l.drop)}/>
          <InfoRow label="Tabungan Keluar" value={fmtFull(l.tabunganKeluar)}/>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Data Target</p>
          <InfoRow label="Target Lama (Nominal)" value={fmtFull(l.targetLamaNominal)}/>
          <InfoRow label="Target Lama (Orang)" value={`${l.targetLamaOrang} orang`}/>
          <InfoRow label="Target Masuk (Nominal)" value={fmtFull(l.targetMasukNominal)}/>
          <InfoRow label="Target Masuk (Orang)" value={`${l.targetMasukOrang} orang`}/>
          <InfoRow label="Target Keluar (Nominal)" value={fmtFull(l.targetKeluarNominal)}/>
          <InfoRow label="Target Keluar (Orang)" value={`${l.targetKeluarOrang} orang`}/>
          <InfoRow label="Jumlah Target (Nominal)" value={fmtFull(l.jumlahTargetNominal)}/>
          <InfoRow label="Jumlah Target (Orang)" value={`${l.jumlahTargetOrang} orang`}/>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Data Drop</p>
          <InfoRow label="Drop Baru" value={fmtFull(l.dropBaru)}/>
          <InfoRow label="Drop Lanjut" value={fmtFull(l.dropLanjut)}/>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Lokasi Tercatat</p>
          <MapPH label={`Lokasi laporan: ${l.lokasi}`} lat={Number(reportMarketing.lat)} lng={Number(reportMarketing.lng)}/>
          <InfoRow label="Alamat" value={l.alamat}/>
          <InfoRow label="Koordinat" value={l.koordinat}/>
        </DCard>
        <GoldBtn onClick={()=>navigate("MarketingDetail",{id:l.marketingCode})} cls="w-full">Lihat Detail Marketing</GoldBtn>
      </div>
    </div>
  );
}

// ── Tracking Map ──────────────────────────────────────────────────────────────

function TrackingMapPage({ day, navigate }: { day:string; navigate:(n:RouteName,p?:Record<string,unknown>)=>void }) {
  const [search, setSearch] = useState("");
  const [statusFilter, setStatusFilter] = useState("Semua");
  const [areaFilter, setAreaFilter] = useState("Semua");
  const [marketingFilter, setMarketingFilter] = useState("Semua");
  const [selectedTrackingDay, setSelectedTrackingDay] = useState<DayName>(day as DayName);
  const [marketingSheetOpen, setMarketingSheetOpen] = useState(false);
  const [filterSheetOpen, setFilterSheetOpen] = useState(false);
  const [trackingItems, setTrackingItems] = useState<LatestTrackingItem[]>([]);
  const [mapLoading, setMapLoading] = useState(true);
  const [mapError, setMapError] = useState<string|null>(null);
  const [refreshKey, setRefreshKey] = useState(0);
  const [lastUpdate, setLastUpdate] = useState(() =>
    new Date().toLocaleTimeString("id-ID",{hour:"2-digit",minute:"2-digit",hour12:false}).replace(".",":")
  );

  useEffect(() => setSelectedTrackingDay(day as DayName), [day]);

  useEffect(() => {
    let active = true;
    setMapLoading(true);
    setMapError(null);
    getLatestTracking(selectedTrackingDay)
      .then(items => {
        if (!active) return;
        setTrackingItems(items);
        setLastUpdate(new Date().toLocaleTimeString("id-ID",{hour:"2-digit",minute:"2-digit",hour12:false}).replace(".",":"));
      })
      .catch(() => { if (active) setMapError("Periksa koneksi atau coba muat ulang data tracking."); })
      .finally(() => { if (active) setMapLoading(false); });
    return () => { active = false; };
  }, [selectedTrackingDay, refreshKey]);

  useEffect(() => {
    const update = () => setRefreshKey(value=>value+1);
    const interval = window.setInterval(update, 30_000);
    const updateWhenVisible = () => {
      if (document.visibilityState === "visible") update();
    };
    document.addEventListener("visibilitychange", updateWhenVisible);
    return () => {
      window.clearInterval(interval);
      document.removeEventListener("visibilitychange", updateWhenVisible);
    };
  }, []);

  const resetFilters = () => {
    setSelectedTrackingDay(day as DayName);
    setSearch("");
    setStatusFilter("Semua");
    setAreaFilter("Semua");
    setMarketingFilter("Semua");
  };
  const showUnscheduled = statusFilter === "Tidak Dijadwalkan" || marketingFilter !== "Semua";
  const filtered = trackingItems.filter(item =>
    (showUnscheduled || item.status!=="Tidak Dijadwalkan") &&
    (statusFilter==="Semua"||item.status===statusFilter) &&
    (areaFilter==="Semua"||item.area===areaFilter) &&
    (marketingFilter==="Semua"||item.marketingId===marketingFilter) &&
    (search===""||item.name.toLowerCase().includes(search.toLowerCase())||item.code.toLowerCase().includes(search.toLowerCase()))
  );
  const timeLabel = (value?:string) => value
    ? new Date(value).toLocaleTimeString("id-ID",{hour:"2-digit",minute:"2-digit",hour12:false}).replace(".",":")
    : "—";
  const mapMarkers: RealMapMarker[] = filtered.map(item => ({
    id:item.marketingId,
    code:item.code,
    name:item.name,
    latitude:item.latitude,
    longitude:item.longitude,
    address:`${item.area}, Kota Bandung`,
    status:item.status,
    time:`${timeLabel(item.lastActiveAt)} WIB`,
    photo:item.photo,
    onClick:()=>navigate("TrackingDetail",{id:item.marketingId,day:selectedTrackingDay}),
  }));
  const selectedMarketing = trackingItems.find(item=>item.marketingId===marketingFilter);
  const marketingLabel = selectedMarketing ? `${selectedMarketing.code} ${selectedMarketing.name}` : "Semua Marketing";
  const statusLabel = statusFilter==="Semua" ? "Semua Status" : statusFilter;
  const summaryParts = [selectedTrackingDay, marketingLabel, statusLabel, ...(areaFilter==="Semua"?[]:[areaFilter])];
  const activeFilterCount = [
    selectedTrackingDay !== day,
    marketingFilter !== "Semua",
    statusFilter !== "Semua",
    areaFilter !== "Semua",
    search.trim() !== "",
  ].filter(Boolean).length;
  const areas = [...new Set(trackingItems.map(item=>item.area))];
  const mobileFilterValue: TrackingFilterValue = { day:selectedTrackingDay, status:statusFilter, area:areaFilter, search };
  const statusDescription = (item: LatestTrackingItem) => {
    if (item.status==="Aktif") return "Lokasi diperbarui 2 menit lalu";
    if (item.status==="Offline") return `Terakhir aktif pukul ${timeLabel(item.lastActiveAt)}`;
    if (item.status==="Belum Mulai") return "Belum memulai tracking";
    if (item.status==="Tidak Dijadwalkan") return "Tidak memiliki jadwal hari ini";
    return "GPS tidak aktif";
  };

  return (
    <div className="pb-20 lg:pb-8">
      <PageHdr title={`Tracking Marketing — ${selectedTrackingDay}`} sub={`Pembaruan terakhir ${lastUpdate} WIB`}/>
      <div className="px-4 pb-4 pt-3 lg:px-8 lg:pt-4">
        <div className="mb-3 rounded-[16px] border border-white/[0.09] bg-[#10161E] p-3 lg:hidden">
          <div className="grid grid-cols-[minmax(0,.78fr)_minmax(0,1.45fr)] gap-2">
            <button type="button" onClick={()=>setFilterSheetOpen(true)}
              className="flex min-h-[44px] min-w-0 items-center justify-between gap-1 rounded-[13px] border border-white/[0.09] bg-[#0D131B] px-3 text-[12px] font-bold text-[#F8FAFC] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]">
              <span className="truncate">{selectedTrackingDay}</span><ChevronDown size={14} className="shrink-0 text-[#8F98A5]"/>
            </button>
            <button type="button" onClick={()=>setMarketingSheetOpen(true)}
              className="flex min-h-[44px] min-w-0 items-center justify-between gap-1 rounded-[13px] border border-white/[0.09] bg-[#0D131B] px-3 text-[12px] font-bold text-[#F8FAFC] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]">
              <span className="truncate">{marketingLabel}</span><ChevronDown size={14} className="shrink-0 text-[#8F98A5]"/>
            </button>
          </div>
          <div className="mt-2 grid grid-cols-[auto_minmax(0,1fr)] items-center gap-2">
            <button type="button" onClick={()=>setFilterSheetOpen(true)}
              className="flex min-h-[42px] items-center gap-2 rounded-[12px] border border-white/[0.09] bg-[#0D131B] px-3 text-[12px] font-bold text-[#F8FAFC] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]">
              <SlidersHorizontal size={15}/>Filter
            </button>
            <div className="min-w-0">
              <p className="truncate text-[11px] text-[#8F98A5]">{summaryParts.join(" · ")}</p>
              {activeFilterCount>0 && <span className="mt-1 inline-flex rounded-full bg-[#D4AF37]/10 px-2 py-0.5 text-[10px] font-bold text-[#D4AF37]">{activeFilterCount} filter aktif</span>}
            </div>
          </div>
        </div>

        <DCard cls="mb-4 hidden p-4 lg:flex lg:flex-wrap lg:items-center lg:gap-2 xl:grid xl:grid-cols-[140px_minmax(180px,1fr)_155px_155px_minmax(220px,1.2fr)_auto] xl:items-center xl:gap-2">
          <div className="lg:contents">
            <select aria-label="Filter hari" value={selectedTrackingDay} onChange={event=>setSelectedTrackingDay(event.target.value as DayName)}
              className="min-h-11 w-full rounded-[13px] border border-white/[0.08] bg-[#0b1118] px-3 text-[12px] text-[#F8FAFC] outline-none lg:w-[120px]">
              {DAYS.map(value=><option key={value} value={value}>{value}</option>)}
            </select>
            <select aria-label="Filter marketing" value={marketingFilter} onChange={event=>setMarketingFilter(event.target.value)}
              className="min-h-11 w-full rounded-[13px] border border-white/[0.08] bg-[#0b1118] px-3 text-[12px] text-[#F8FAFC] outline-none lg:w-[160px]">
              <option value="Semua">Semua Marketing</option>
              {trackingItems.map(item=><option key={item.marketingId} value={item.marketingId}>{item.code} {item.name}</option>)}
            </select>
            <select aria-label="Filter status" value={statusFilter} onChange={event=>setStatusFilter(event.target.value)}
              className="min-h-11 w-full rounded-[13px] border border-white/[0.08] bg-[#0b1118] px-3 text-[12px] text-[#F8FAFC] outline-none lg:w-[135px]">
              {["Semua","Aktif","Offline","Belum Mulai","GPS Tidak Aktif","Tidak Dijadwalkan"].map(value=><option key={value} value={value}>{value}</option>)}
            </select>
            <select aria-label="Filter area" value={areaFilter} onChange={event=>setAreaFilter(event.target.value)}
              className="min-h-11 w-full rounded-[13px] border border-white/[0.08] bg-[#0b1118] px-3 text-[12px] text-[#F8FAFC] outline-none lg:w-[135px]">
              <option value="Semua">Semua Area</option>
              {areas.map(value=><option key={value} value={value}>{value}</option>)}
            </select>
            <div className="relative min-w-[190px] flex-1">
              <Search size={15} className="absolute left-3.5 top-3.5 text-[#7E8794]"/>
              <input value={search} onChange={e=>setSearch(e.target.value)} placeholder="Cari marketing atau kode..."
                className="min-h-11 w-full rounded-[13px] border border-white/[0.08] bg-[#0b1118] pl-10 pr-3 text-[12px] text-[#F8FAFC] placeholder:text-[#687382] outline-none transition focus:border-[#D4AF37]/40"/>
            </div>
            <GhostBtn onClick={resetFilters} cls="min-h-11 px-4">Reset</GhostBtn>
          </div>
        </DCard>

        <div className="grid items-start gap-4 lg:grid-cols-[minmax(0,1.55fr)_minmax(340px,.85fr)]">
          <div>
            <RealMap markers={mapMarkers} loading={mapLoading} error={mapError}
              onRetry={()=>setRefreshKey(value=>value+1)}
              fullscreenTitle="Tracking Lokasi Marketing"
              fullscreenSubtitle={`${selectedTrackingDay} · ${filtered.length} marketing ditampilkan`}
              className="h-[307px] min-h-[307px]! min-[412px]:h-[337px] min-[412px]:min-h-[337px]! sm:h-[420px] sm:min-h-[420px]! lg:h-[520px] lg:min-h-[520px]!"/>
            <div className="mt-3 rounded-xl border border-white/[0.08] bg-[#10161e] px-3 py-2.5">
              <div className="flex flex-wrap gap-x-4 gap-y-2">
                {[["bg-[#22C55E]","Aktif"],["bg-[#EF4444]","Offline"],["bg-[#9CA3AF]","Belum Mulai"],["bg-[#5B6471]","Tidak Dijadwalkan"],["bg-[#F59E0B]","GPS Tidak Aktif"]].map(([color,label])=>(
                  <span key={label} className="flex items-center gap-1.5 text-[11px] text-[#A8B0BD]"><i className={`size-2.5 rounded-full ${color}`}/>{label}</span>
                ))}
              </div>
              <p className="mt-2 border-t border-white/[0.06] pt-2 text-[11px] font-bold text-[#D4AF37]">Diperbarui {lastUpdate} WIB</p>
            </div>
          </div>
          <div>
            <div className="mb-3 flex items-end justify-between gap-3">
              <div><p className="text-[16px] font-extrabold text-[#F8FAFC]">Status Marketing</p><p className="text-[11px] text-[#7E8794]">{filtered.length} marketing ditampilkan</p></div>
            </div>
            <DCard cls="overflow-hidden lg:max-h-[520px] lg:overflow-y-auto">
              {filtered.length===0 && <EmptyState icon={MapPin} title="Data lokasi tidak ditemukan" desc="Coba ubah filter atau pilih hari lain"/>}
              {filtered.map(item => (
                <button key={item.marketingId} onClick={()=>navigate("TrackingDetail",{id:item.marketingId,day:selectedTrackingDay})}
                  className="flex min-h-[82px] w-full items-center gap-3 border-b border-white/5 p-4 text-left transition-colors hover:bg-white/[0.035] last:border-none">
                  <Ava name={item.name} cls="size-10 text-sm"/>
                  <div className="min-w-0 flex-1">
                    <div className="flex items-center justify-between gap-2">
                      <p className="text-[14px] font-bold text-[#F8FAFC]">{item.name} <span className="text-[#D4AF37] text-[12px]">{item.code}</span></p>
                      <StatusBadge status={item.status as TrackStatus}/>
                    </div>
                    <p className="mt-1 truncate text-[12px] text-[#A8B0BD]">{item.area}, Kota Bandung</p>
                    <p className="mt-0.5 text-[11px] text-[#7E8794]">{statusDescription(item)}</p>
                  </div>
                  <ChevronRight size={16} className="shrink-0 text-[#7E8794]"/>
                </button>
              ))}
            </DCard>
          </div>
        </div>
      </div>
      <MarketingPickerSheet
        open={marketingSheetOpen}
        value={marketingFilter}
        items={trackingItems.map(item=>({id:item.marketingId,code:item.code,name:item.name,area:item.area,status:item.status,photo:item.photo}))}
        onClose={()=>setMarketingSheetOpen(false)}
        onSelect={marketingId=>{setMarketingFilter(marketingId);setMarketingSheetOpen(false);}}
      />
      <TrackingFilterSheet
        open={filterSheetOpen}
        value={mobileFilterValue}
        defaultDay={day as DayName}
        days={DAYS as DayName[]}
        areas={areas}
        onClose={()=>setFilterSheetOpen(false)}
        onApply={(value, resetMarketing)=>{
          if (resetMarketing) setMarketingFilter("Semua");
          setSelectedTrackingDay(value.day);
          setStatusFilter(value.status);
          setAreaFilter(value.area);
          setSearch(value.search);
          setFilterSheetOpen(false);
        }}
      />
    </div>
  );
}

// ── Tracking Detail ───────────────────────────────────────────────────────────

function TrackingDetailPage({ id, day, back, navigate }: { id:string; day:string; back:()=>void; navigate:(n:RouteName,p?:Record<string,unknown>)=>void }) {
  const m = MARKETERS.find(x=>x.id===id) || MARKETERS[0];
  const trackingStatus = trackingStatusFor(m,day);
  const activeSchedule = JADWAL_DATA.find(schedule=>schedule.marketingCode===m.code&&schedule.hari===day);
  const [location, setLocation] = useState<LatestTrackingItem|null>(null);
  const [journey, setJourney] = useState<MarketingJourneyResult|null>(null);
  const [loading, setLoading] = useState(true);
  const [mapError, setMapError] = useState<string|null>(null);
  const [retryKey, setRetryKey] = useState(0);

  useEffect(() => {
    let active = true;
    setLoading(true);
    setMapError(null);
    Promise.all([
      getMarketingLocation(m.id, day as DayName),
      getMarketingJourney(m.id, DAY_DATES[day]),
    ])
      .then(([locationResult, journeyResult]) => {
        if (!active) return;
        setLocation(locationResult);
        setJourney(journeyResult);
        if (!locationResult && !journeyResult) setMapError("Data lokasi marketing belum tersedia.");
      })
      .catch(() => { if (active) setMapError("Periksa koneksi atau coba muat ulang data tracking."); })
      .finally(() => { if (active) setLoading(false); });
    return () => { active = false; };
  }, [m.id, day, retryKey]);

  const journeyMarkers: RealMapMarker[] = (journey?.points ?? []).map((point, index, points) => ({
    id:point.id,
    code:point.type==="Mulai" ? "A" : point.type==="Selesai" ? "Z" : `K${index}`,
    name:point.label,
    latitude:point.latitude,
    longitude:point.longitude,
    address:point.type==="Kunjungan" ? `${m.area}, Kota Bandung` : `Rute ${m.name}`,
    status:trackingStatus,
    time:new Date(point.timestamp).toLocaleTimeString("id-ID",{hour:"2-digit",minute:"2-digit",hour12:false}).replace(".",":") + " WIB",
    photo:point===points.at(-1) ? location?.photo : undefined,
  }));
  const pointMarkers: RealMapMarker[] = journeyMarkers.length ? journeyMarkers : location ? [{
    id:location.marketingId,
    code:location.code,
    name:location.name,
    latitude:location.latitude,
    longitude:location.longitude,
    address:`${location.area}, Kota Bandung`,
    status:trackingStatus,
    time:m.time==="—"?"Belum tersedia":`${m.time} WIB`,
    photo:location.photo,
  }] : [];
  const updatedAt = journey?.updatedAt
    ? new Date(journey.updatedAt).toLocaleString("id-ID",{day:"2-digit",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit",hour12:false})
    : "—";
  return (
    <div className="pb-4">
      <PageHdr title={`Tracking — ${m.name}`} sub={`${m.code} · ${m.area} · ${day}`} onBack={back}/>
      <div className="mx-auto max-w-6xl space-y-4 px-4 py-5 sm:px-6 lg:px-8">
        <DCard cls="p-4">
          <div className="flex items-center justify-between mb-3">
            <div className="flex items-center gap-3">
              <Ava name={m.name} cls="size-12 text-base"/>
              <div>
                <p className="text-[16px] font-extrabold text-[#F8FAFC]">{m.name}</p>
                <p className="text-[12px] text-[#D4AF37]">{m.code} · {m.area}</p>
              </div>
            </div>
            <StatusBadge status={trackingStatus}/>
          </div>
          {trackingStatus==="Tidak Dijadwalkan" && (
            <div className="rounded-xl border border-[#5B6471]/25 bg-[#5B6471]/10 p-3">
              <p className="text-[13px] font-bold text-[#9CA3AF]">Marketing tidak memiliki jadwal pada hari {day}.</p>
            </div>
          )}
          {trackingStatus==="Offline" && (
            <div className="rounded-xl bg-red-500/10 border border-red-500/20 p-3">
              <p className="text-[13px] text-red-400 font-bold">Marketing sedang offline. Lokasi terakhir diperbarui pukul {m.time}.</p>
            </div>
          )}
          {trackingStatus==="Belum Mulai" && (
            <div className="rounded-xl bg-[#6B7280]/10 border border-[#6B7280]/20 p-3">
              <p className="text-[13px] text-[#9CA3AF] font-bold">Marketing belum memulai tracking hari ini.</p>
            </div>
          )}
          {trackingStatus==="GPS Tidak Aktif" && (
            <div className="rounded-xl border border-amber-500/20 bg-amber-500/10 p-3">
              <p className="text-[13px] font-bold text-amber-400">GPS marketing tidak aktif. Posisi terbaru belum dapat diperbarui.</p>
            </div>
          )}
          {trackingStatus==="Aktif" && (
            <div className="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-3">
              <p className="text-[13px] text-emerald-400 font-bold">Tracking aktif dan lokasi diperbarui secara berkala.</p>
            </div>
          )}
        </DCard>
        <RealMap
          markers={pointMarkers}
          route={journey?.route ?? []}
          loading={loading}
          error={mapError}
          onRetry={()=>setRetryKey(value=>value+1)}
          className="h-[340px] sm:h-[440px] lg:h-[520px]"
        />
        <DCard cls="p-4">
          <InfoRow label="Status Tracking" value={<StatusBadge status={trackingStatus}/>}/>
          <InfoRow label={`Jadwal ${day}`} value={activeSchedule?`${activeSchedule.jamMulai}–${activeSchedule.jamSelesai}`:"Tidak dijadwalkan"}/>
          <InfoRow label="Lokasi Terakhir" value={`${m.area}, Kota Bandung`}/>
          <InfoRow label="Koordinat" value={`${m.lat}, ${m.lng}`}/>
          <InfoRow label="Rute Terakhir Diperbarui" value={`${updatedAt}${updatedAt==="—"?"":" WIB"}`}/>
          <InfoRow label="Waktu Terakhir Aktif" value={m.time==="—"?"—":`${m.time} WIB`}/>
        </DCard>
        <div className="flex gap-2">
          <GhostBtn onClick={()=>navigate("RiwayatDetail",{id:m.id,day})} cls="flex-1"><Route size={15}/>Riwayat</GhostBtn>
          <GhostBtn onClick={()=>navigate("MarketingDetail",{id:m.id})} cls="flex-1"><UserRound size={15}/>Profil Marketing</GhostBtn>
        </div>
      </div>
    </div>
  );
}

// ── Riwayat ───────────────────────────────────────────────────────────────────

function RiwayatListPage({ day, filterCode, back, navigate }: { day:string; filterCode?:string; back:()=>void; navigate:(n:RouteName,p?:Record<string,unknown>)=>void }) {
  const [marketingFilter,setMarketingFilter] = useState(filterCode||"Semua");
  const [statusFilter,setStatusFilter] = useState("Semua");
  const list = journeys
    .filter(journey=>journey.day===day&&journey.date===DAY_DATES[day])
    .filter(journey=>marketingFilter==="Semua"||journey.marketingId===marketingFilter)
    .filter(journey=>statusFilter==="Semua"||journey.status===statusFilter)
    .map(journey=>({journey,marketing:MARKETERS.find(marketing=>marketing.id===journey.marketingId)??MARKETERS[0]}));
  return (
    <div className="pb-4">
      <PageHdr title={`Riwayat Perjalanan — ${day}`} sub={`${list.length} perjalanan marketing`} onBack={back}/>
      <div className="px-4 py-4 lg:px-8 space-y-3">
        <div className="grid grid-cols-2 gap-2">
          <select value={marketingFilter} onChange={event=>setMarketingFilter(event.target.value)}
            className="min-h-11 min-w-0 rounded-xl border border-white/10 bg-[#121820] px-3 text-[12px] text-[#F8FAFC] outline-none">
            <option value="Semua">Semua Marketing</option>
            {MARKETERS.map(marketing=><option key={marketing.id} value={marketing.id}>{marketing.code} {marketing.name}</option>)}
          </select>
          <select value={statusFilter} onChange={event=>setStatusFilter(event.target.value)}
            className="min-h-11 min-w-0 rounded-xl border border-white/10 bg-[#121820] px-3 text-[12px] text-[#F8FAFC] outline-none">
            <option value="Semua">Semua Status</option>
            <option value="Berjalan">Berjalan</option>
            <option value="Selesai">Selesai</option>
            <option value="Menunggu Sinkronisasi">Menunggu Sinkronisasi</option>
          </select>
        </div>
        {list.length===0 && <EmptyState icon={Route} title={`Belum ada riwayat perjalanan pada hari ${day}.`} desc="Riwayat akan tampil setelah marketing memulai perjalanan."/>}
        {list.map(({journey,marketing:m}) => (
          <button key={journey.id} onClick={()=>navigate("RiwayatDetail",{id:m.id,day})}
            className="flex w-full items-center gap-3 rounded-[16px] border border-white/[0.07] bg-[#111720] p-4 text-left transition hover:bg-[#1A222D]">
            <Ava name={m.name} cls="size-11 text-sm"/>
            <div className="min-w-0 flex-1">
              <p className="text-[14px] font-bold text-[#F8FAFC]">{m.name} <span className="text-[#D4AF37] text-[12px]">{m.code}</span></p>
              <p className="text-[12px] text-[#7E8794]">{m.area} · {journey.day}, {formatDate(journey.date)}</p>
              <p className="text-[11px] text-[#7E8794]">
                {new Date(journey.startedAt).toLocaleTimeString("id-ID",{hour:"2-digit",minute:"2-digit",hour12:false})}
                {"–"}{journey.endedAt?new Date(journey.endedAt).toLocaleTimeString("id-ID",{hour:"2-digit",minute:"2-digit",hour12:false}):"Berjalan"}
                {" · "}{journey.visitCount} kunjungan · {journey.status}
              </p>
            </div>
            <ChevronRight size={16} className="text-[#7E8794] shrink-0"/>
          </button>
        ))}
      </div>
    </div>
  );
}

function RiwayatDetailPage({ id, day, back }: { id:string; day:string; back:()=>void }) {
  const m = MARKETERS.find(x=>x.id===id) || MARKETERS[0];
  const journey = journeys.find(item=>item.marketingId===id&&item.day===day);
  const timeline = (journey?.locationPoints??[]).map((point,index)=>({
    time:new Date(point.timestamp).toLocaleTimeString("id-ID",{hour:"2-digit",minute:"2-digit",hour12:false}).replace(".",":"),
    label:point.type==="Mulai"?"Titik Awal":point.type==="Selesai"?"Titik Akhir":point.type==="Kunjungan"?`Kunjungan ${index}`:"Dalam Perjalanan",
    desc:point.address||`${m.area}, Kota Bandung`,
    type:point.type,
  }));
  if (!journey) return (
    <div className="pb-4">
      <PageHdr title={`Riwayat Perjalanan ${m.code} ${m.name}`} sub={day} onBack={back}/>
      <div className="px-4 py-8 lg:px-8"><EmptyState icon={Route} title={`Belum ada riwayat perjalanan pada hari ${day}.`} desc="Pilih hari lain untuk melihat perjalanan marketing."/></div>
    </div>
  );
  const startedAt = new Date(journey.startedAt);
  const endedAt = journey.endedAt?new Date(journey.endedAt):null;
  const timeText = (value:Date) => value.toLocaleTimeString("id-ID",{hour:"2-digit",minute:"2-digit",hour12:false}).replace(".",":");
  const duration = endedAt?`${Math.round((endedAt.getTime()-startedAt.getTime())/3_600_000)} jam`:"Sedang berjalan";
  const journeyRoute = journey.locationPoints.map(point=>[point.latitude,point.longitude] as [number,number]);
  return (
    <div className="pb-4">
      <PageHdr title={`Riwayat Perjalanan ${m.code} ${m.name}`} sub={`${journey.day} · ${formatDate(journey.date)} · ${journey.status}`} onBack={back}/>
      <div className="mx-auto max-w-5xl space-y-4 px-4 py-5 sm:px-6 lg:px-8">
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
          {[["Waktu Mulai",`${timeText(startedAt)} WIB`],["Waktu Selesai",endedAt?`${timeText(endedAt)} WIB`:"—"],["Durasi",duration],["Kunjungan",`${journey.visitCount}×`],["Jarak",`${journey.distanceKm ?? 0} km`]].map(([l,v])=>(
            <DCard key={l} cls="p-3 text-center"><p className="text-[11px] text-[#7E8794]">{l}</p><p className="mt-1 text-[13px] font-extrabold text-[#F8FAFC]">{v}</p></DCard>
          ))}
        </div>
        <MapPH label={`Rute perjalanan ${m.name} pada ${day}`} lat={journeyRoute[0]?.[0]??Number(m.lat)} lng={journeyRoute[0]?.[1]??Number(m.lng)} route={journeyRoute}/>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-4">Timeline Perjalanan</p>
          <div className="relative space-y-0">
            {timeline.map((t,i)=>(
              <div key={i} className="flex gap-3">
                <div className="flex flex-col items-center">
                  <div className={`size-3 shrink-0 mt-1 rounded-full border-2 ${t.type==="Mulai"?"bg-emerald-500 border-emerald-400":t.type==="Kunjungan"?"bg-[#D4AF37] border-[#E8C65A]":t.type==="Selesai"?"bg-red-500 border-red-400":"bg-[#4B5563] border-[#6B7280]"}`}/>
                  {i<timeline.length-1 && <div className="w-0.5 flex-1 bg-white/10 mt-1 mb-1 min-h-[28px]"/>}
                </div>
                <div className="pb-4">
                  <p className="text-[11px] font-bold text-[#D4AF37]">{t.time} WIB</p>
                  <p className="text-[13px] font-bold text-[#F8FAFC]">{t.label}</p>
                  <p className="text-[12px] text-[#7E8794]">{t.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </DCard>
      </div>
    </div>
  );
}

// ── Jadwal ────────────────────────────────────────────────────────────────────

function JadwalListPage({ day, filterCode, back, navigate }: { day:string; filterCode?:string; back:()=>void; navigate:(n:RouteName,p?:Record<string,unknown>)=>void }) {
  const [mFilter, setMFilter] = useState(filterCode||"Semua");
  const filtered = JADWAL_DATA.filter(j => j.hari===day && (mFilter==="Semua" || j.marketingCode===mFilter));
  return (
    <div className="pb-20 lg:pb-8">
      <PageHdr title="Jadwal Marketing" sub={`${filtered.length} jadwal`} onBack={back}
        action={<GoldBtn onClick={()=>navigate("JadwalForm")} cls="px-3"><Plus size={15}/>Tambah</GoldBtn>}/>
      <div className="px-4 py-4 lg:px-8 space-y-3">
        <select value={mFilter} onChange={e=>setMFilter(e.target.value)}
          className="w-full rounded-xl border border-white/10 bg-[#121820] px-3 py-2.5 text-[13px] text-[#F8FAFC] outline-none">
          <option value="Semua">Semua Marketing</option>
          {MARKETERS.map(m=><option key={m.code} value={m.code}>{m.code} {m.name}</option>)}
        </select>
        {filtered.map(j => (
          <DCard key={j.id} cls="p-4">
            <div className="flex items-start justify-between gap-3">
              <div className="min-w-0">
                <div className="flex flex-wrap items-center gap-2">
                  <p className="text-[14px] font-extrabold text-[#F8FAFC]">{j.marketingName}</p>
                  <span className="text-[11px] font-bold text-[#D4AF37]">{j.marketingCode}</span>
                  <JadwalStatusBadge status={j.status}/>
                </div>
                <p className="mt-1 text-[12px] text-[#A8B0BD]">{j.hari} · {j.tanggal}</p>
                <p className="text-[12px] text-[#7E8794]">{j.jamMulai}–{j.jamSelesai} · {j.area}</p>
                <p className="text-[12px] text-[#7E8794]">{j.tujuan}</p>
              </div>
              <GhostBtn onClick={()=>navigate("JadwalForm",{id:j.id})} cls="size-9 min-h-0 p-0 shrink-0"><Pencil size={14}/></GhostBtn>
            </div>
          </DCard>
        ))}
      </div>
    </div>
  );
}

function JadwalFormPage({ id, back, showToast }: { id?:string; back:()=>void; showToast:(s:string)=>void }) {
  const ex = id ? JADWAL_DATA.find(j=>j.id===id) : null;
  const [mCode, setMCode] = useState(ex?.marketingCode||MARKETERS[0].code);
  const [hari, setHari] = useState(ex?.hari||DAYS[0]);
  const [tanggal, setTanggal] = useState(ex?.tanggal||"2026-07-21");
  const [jamMulai, setJamMulai] = useState(ex?.jamMulai||"08:00");
  const [jamSelesai, setJamSelesai] = useState(ex?.jamSelesai||"16:00");
  const [area, setArea] = useState(ex?.area||"");
  const [tujuan, setTujuan] = useState(ex?.tujuan||"");
  const [catatan, setCatatan] = useState(ex?.catatan||"");
  const [status, setStatus] = useState<string>(ex?.status||"Belum Mulai");
  const [error, setError] = useState("");
  const save = () => {
    if(!mCode||!hari||!tanggal||!jamMulai||!jamSelesai||!area.trim()){setError("Marketing, hari, tanggal, waktu, dan area wajib diisi.");return;}
    if(jamSelesai<=jamMulai){setError("Jam selesai harus setelah jam mulai.");return;}
    setError(""); showToast(`Jadwal berhasil ${id?"diperbarui":"ditambahkan"}`); back();
  };
  return (
    <div className="pb-4">
      <PageHdr title={id?"Edit Jadwal":"Tambah Jadwal"} sub="Atur jadwal operasional marketing" onBack={back}/>
      <div className="mx-auto max-w-5xl space-y-4 px-4 py-5 sm:px-6 lg:px-8">
        <DCard cls="p-4 space-y-4">
          <Sel label="Marketing" opts={MARKETERS.map(m=>`${m.code} ${m.name}`)} value={`${mCode} ${MARKETERS.find(m=>m.code===mCode)?.name}`} onChange={v=>setMCode(v.split(" ")[0])}/>
          <div className="grid gap-4 lg:grid-cols-2">
            <Sel label="Hari" opts={DAYS} value={hari} onChange={setHari}/>
            <Inp label="Tanggal" type="date" value={tanggal} onChange={setTanggal}/>
            <Inp label="Jam Mulai" type="time" value={jamMulai} onChange={setJamMulai}/>
            <Inp label="Jam Selesai" type="time" value={jamSelesai} onChange={setJamSelesai}/>
            <Inp label="Area / Resort" ph="Contoh: Gedebage" value={area} onChange={setArea}/>
            <Sel label="Status" opts={["Belum Mulai","Sedang Berjalan","Selesai","Tidak Aktif"]} value={status} onChange={setStatus}/>
          </div>
          <Inp label="Tujuan" ph="Deskripsi tujuan kunjungan" value={tujuan} onChange={setTujuan}/>
          <Inp label="Catatan" ph="Catatan tambahan (opsional)" value={catatan} onChange={setCatatan}/>
        </DCard>
        {error&&<p role="alert" className="rounded-xl border border-red-500/30 bg-red-500/10 p-3 text-[12px] font-bold text-red-400">{error}</p>}
        <div className="flex gap-3">
          <GhostBtn onClick={back} cls="flex-1">Batal</GhostBtn>
          <GoldBtn onClick={save} cls="flex-1"><Check size={15}/>Simpan</GoldBtn>
        </div>
      </div>
    </div>
  );
}

// ── Profil ────────────────────────────────────────────────────────────────────

function ProfilPage({ day, navigate, onLogout }: { day:string; navigate:(n:RouteName,p?:Record<string,unknown>)=>void; onLogout:()=>void }) {
  const [showLogout, setShowLogout] = useState(false);
  const recapRows = operationalRecaps.find(item=>item.day===day)?.rows ?? [];
  const targetMasuk = recapRows.reduce((total,item)=>total+item.target.incoming,0);
  const targetKeluar = recapRows.reduce((total,item)=>total+item.target.outgoing,0);
  const sirkulasi = recapRows.reduce((total,item)=>total+item.currentCirculation,0);
  useEffect(() => {
    if (!showLogout) return;
    const close = (event: KeyboardEvent) => {
      if (event.key === "Escape") setShowLogout(false);
    };
    window.addEventListener("keydown", close);
    return () => window.removeEventListener("keydown", close);
  }, [showLogout]);
  return (
    <div className="pb-20 lg:pb-8">
      <PageHdr title="Profil Admin" sub="Pengaturan akun administrator"/>
      <div className="mx-auto max-w-5xl space-y-4 px-4 py-5 sm:px-6 lg:px-8">
        <DCard cls="p-5">
          <div className="flex items-center gap-4">
            <img src="/profiles/marketing-13.jpg" alt="Foto profil Admin KSP" className="size-20 shrink-0 rounded-[20px] border border-white/[0.08] object-cover"/>
            <div>
          <p className="text-[18px] font-semibold text-[#F3F5F7]">Admin KSP</p>
              <p className="text-[13px] text-[#7E8794]">admin.ksp · Administrator</p>
              <span className="mt-1.5 inline-flex items-center gap-1 rounded-full bg-emerald-500/[0.12] px-2.5 py-1 text-[11px] font-semibold text-emerald-400"><CheckCircle2 size={11}/>Akun Aktif</span>
            </div>
          </div>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Informasi Akun</p>
          <InfoRow label="Nama" value="Admin KSP"/>
          <InfoRow label="Username" value="admin.ksp"/>
          <InfoRow label="Role" value="Administrator"/>
          <InfoRow label="Instansi" value="KSP Manunggal Makmur Sejahtera"/>
          <InfoRow label="Status" value={<span className="text-emerald-400">Aktif</span>}/>
        </DCard>
        <DCard cls="p-4">
          <p className="text-[14px] font-extrabold text-[#F8FAFC] mb-3">Ringkasan Sistem</p>
          <InfoRow label="Total Marketing" value="13 orang"/>
          <InfoRow label="Total Anggota" value="39 anggota"/>
          <InfoRow label="Total Target" value={fmtFull(TOTAL_TARGET)}/>
          <InfoRow label="Target Masuk" value={fmtFull(targetMasuk)}/>
          <InfoRow label="Target Keluar" value={fmtFull(targetKeluar)}/>
          <InfoRow label="Sirkulasi" value={fmtFull(sirkulasi)}/>
          <InfoRow label="Total Drop" value={fmtFull(TOTAL_DROP)}/>
          <InfoRow label="Total Storting" value={fmtFull(TOTAL_STORTING)}/>
        </DCard>
        <GhostBtn onClick={()=>navigate("UbahPassword")} cls="w-full"><Lock size={15}/>Ubah Password</GhostBtn>
        <GhostBtn onClick={()=>setShowLogout(true)} danger cls="w-full"><LogOut size={15}/>Keluar dari Akun</GhostBtn>
      </div>
      {/* Logout confirm */}
      {showLogout && (
        <>
          <button aria-label="Tutup dialog keluar" onClick={()=>setShowLogout(false)} className="fixed inset-0 z-50 bg-black/60"/>
          <div role="dialog" aria-modal="true" aria-labelledby="logout-dialog-title" className="fixed inset-x-4 top-1/2 z-[60] mx-auto max-w-sm -translate-y-1/2 rounded-[20px] border border-white/[0.07] bg-[#111720] p-6 shadow-[0_16px_48px_rgba(0,0,0,.32)]">
            <p id="logout-dialog-title" className="text-[17px] font-semibold text-[#F3F5F7]">Keluar dari Akun?</p>
            <p className="mt-2 text-[13px] text-[#7E8794]">Anda akan keluar dan kembali ke halaman login.</p>
            <div className="mt-5 flex gap-3">
              <GhostBtn onClick={()=>setShowLogout(false)} cls="flex-1">Batal</GhostBtn>
              <GhostBtn danger onClick={onLogout} cls="flex-1"><LogOut size={15}/>Ya, Keluar</GhostBtn>
            </div>
          </div>
        </>
      )}
    </div>
  );
}

function UbahPasswordPage({ back, showToast }: { back:()=>void; showToast:(s:string)=>void }) {
  const [show, setShow] = useState({ old:false,new:false,conf:false });
  const [values, setValues] = useState({ old:"",new:"",conf:"" });
  const [error, setError] = useState("");
  const save = () => {
    if(!values.old||!values.new||!values.conf){setError("Semua field password wajib diisi.");return;}
    if(values.new.length<8){setError("Password baru minimal 8 karakter.");return;}
    if(values.new!==values.conf){setError("Konfirmasi password tidak sama.");return;}
    setError(""); showToast("Password berhasil diubah"); back();
  };
  return (
    <div className="pb-4">
      <PageHdr title="Ubah Password" sub="Ganti password akun administrator" onBack={back}/>
      <div className="mx-auto max-w-xl px-4 py-5 sm:px-6">
        <DCard cls="p-5 space-y-4">
          {[
            { label:"Password Lama", key:"old" as const },
            { label:"Password Baru", key:"new" as const },
            { label:"Konfirmasi Password", key:"conf" as const },
          ].map(({ label, key }) => (
            <label key={key} className="block">
              <span className="text-[13px] font-bold text-[#A8B0BD]">{label}</span>
              <div className="relative mt-1.5">
                <input type={show[key]?"text":"password"} value={values[key]} onChange={e=>setValues(prev=>({...prev,[key]:e.target.value}))}
                  className="w-full rounded-xl border border-white/10 bg-[#111720] px-3 py-2.5 pr-11 text-[13px] text-[#F8FAFC] outline-none focus:border-[#D4AF37]/50"/>
                <button onClick={()=>setShow(p=>({...p,[key]:!p[key]}))}
                  className="absolute right-2 top-2 grid size-8 place-items-center text-[#7E8794] hover:text-[#A8B0BD]">
                  {show[key] ? <EyeOff size={15}/> : <Eye size={15}/>}
                </button>
              </div>
            </label>
          ))}
          {error&&<p role="alert" className="rounded-xl border border-red-500/30 bg-red-500/10 p-3 text-[12px] font-bold text-red-400">{error}</p>}
          <div className="flex gap-3 pt-2">
            <GhostBtn onClick={back} cls="flex-1">Batal</GhostBtn>
            <GoldBtn onClick={save} cls="flex-1"><Check size={15}/>Simpan</GoldBtn>
          </div>
        </DCard>
      </div>
    </div>
  );
}

// ── App Root ──────────────────────────────────────────────────────────────────

export default function AdminApplication() {
  const [loggedIn, setLoggedIn] = useState(false);
  const [stack, setStack] = useState<Route[]>([{ name: "Dashboard" }]);
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [globalToast, setGlobalToast] = useState("");
  const { selectedDay: day, setSelectedDay, refresh, loading, error } = useAdminData();

  const showToast = (msg: string) => { setGlobalToast(msg); setTimeout(()=>setGlobalToast(""),2500); };
  const current = stack[stack.length - 1];
  const isMain = MAIN_PAGES.includes(current.name);
  const setDay = (value: string) => setSelectedDay(value as DayName);

  useLayoutEffect(() => {
    if (!loggedIn) return;

    window.scrollTo({ top: 0, left: 0, behavior: "auto" });

    const scrollRoot = document.scrollingElement;
    if (scrollRoot) {
      scrollRoot.scrollTop = 0;
      scrollRoot.scrollLeft = 0;
    }

    document.documentElement.scrollTop = 0;
    document.body.scrollTop = 0;
  }, [loggedIn, stack]);

  useEffect(() => {
    if (!loggedIn) return;
    const update = () => void refresh();
    const interval = window.setInterval(update, 30_000);
    const updateWhenVisible = () => {
      if (document.visibilityState === "visible") update();
    };
    document.addEventListener("visibilitychange", updateWhenVisible);
    return () => {
      window.clearInterval(interval);
      document.removeEventListener("visibilitychange", updateWhenVisible);
    };
  }, [loggedIn, refresh]);

  const navigate = (name: RouteName, params?: Record<string, unknown>) => {
    setStack(prev => [...prev, { name, params }]);
  };
  const back = () => setStack(prev => prev.length > 1 ? prev.slice(0,-1) : [{ name:"Dashboard" }]);
  const goMain = (name: RouteName) => { setStack([{ name }]); setSidebarOpen(false); };

  if (!loggedIn) return <LoginPage onLogin={()=>setLoggedIn(true)}/>;

  const renderPage = () => {
    const p = current.params || {};
    switch (current.name) {
      case "Dashboard": return <DashboardPage day={day} setDay={setDay} navigate={navigate}/>;
      case "DailyHub": return <DailyHubPage day={day} setDay={setDay} navigate={navigate}/>;
      case "MarketingList": return <MarketingListPage navigate={navigate}/>;
      case "MarketingDetail": return <MarketingDetailPage id={String(p.id||"M01")} day={day} navigate={navigate} back={back}/>;
      case "MarketingForm": return <MarketingFormPage id={p.id?String(p.id):undefined} navigate={navigate} back={back} showToast={showToast}/>;
      case "RekapOperasional": return <OperationalRecapPage day={day} setDay={setDay} back={back}/>;
      case "RekapAnggota": return <RekapDetailPage type="Anggota" back={back} navigate={navigate}/>;
      case "RekapTarget": return <RekapDetailPage type="Target" back={back} navigate={navigate}/>;
      case "RekapDrop": return <RekapDetailPage type="Drop" back={back} navigate={navigate}/>;
      case "RekapStorting": return <RekapDetailPage type="Storting" back={back} navigate={navigate}/>;
      case "AnggotaList": return <MemberListPage day={day} navigate={navigate} back={back} filterCode={p.code?String(p.code):undefined}/>;
      case "AnggotaDetail": return <MemberDetailPage id={String(p.id||"A001")} back={back} navigate={navigate}/>;
      case "ProspectList": return <ProspectListPage day={day} navigate={navigate} back={back}/>;
      case "ProspectDetail": return <div className="px-4 pb-8 pt-5 sm:px-6 lg:px-8"><ProspectDetailPage id={String(p.id||"P001")} navigate={navigate} back={back}/></div>;
      case "VisitReportList": return <div className="px-4 pb-24 pt-5 sm:px-6 lg:px-8 lg:pb-8"><VisitReportListPage day={day} navigate={navigate} back={back}/></div>;
      case "VisitReportDetail": return <div className="px-4 pb-8 pt-5 sm:px-6 lg:px-8"><VisitReportDetailPage id={String(p.id||"LK001")} navigate={navigate} back={back}/></div>;
      case "LaporanHarianList": return <DailyReportListPage day={day} navigate={navigate} back={back}/>;
      case "LaporanHarianDetail": return <DailyReportDetailPage id={String(p.id||"LH-M01-Senin")} back={back}/>;
      case "TrackingMap": return <TrackingMapPage day={day} navigate={navigate}/>;
      case "TrackingDetail": return <TrackingDetailPage id={String(p.id||"M01")} day={String(p.day||day)} back={back} navigate={navigate}/>;
      case "RiwayatList": return <RiwayatListPage day={day} filterCode={p.code?String(p.code):undefined} back={back} navigate={navigate}/>;
      case "RiwayatDetail": return <RiwayatDetailPage id={String(p.id||"M01")} day={String(p.day||day)} back={back}/>;
      case "JadwalList": return <div className="px-4 pb-24 pt-5 sm:px-6 lg:px-8 lg:pb-8"><ScheduleListPage day={day} back={back}/></div>;
      case "JadwalForm": return <JadwalFormPage id={p.id?String(p.id):undefined} back={back} showToast={showToast}/>;
      case "Profil": return <ProfilPage day={day} navigate={navigate} onLogout={()=>setLoggedIn(false)}/>;
      case "UbahPassword": return <UbahPasswordPage back={back} showToast={showToast}/>;
      default: return <DashboardPage day={day} setDay={setDay} navigate={navigate}/>;
    }
  };

  return (
    <div className="min-h-dvh w-full min-w-0 max-w-full overflow-x-hidden bg-[#080B10] font-sans text-[#F3F5F7]">
      <LayoutSidebar current={current.name} navigate={route=>goMain(route as RouteName)} open={sidebarOpen} onClose={()=>setSidebarOpen(false)} onLogout={()=>setLoggedIn(false)}/>
      <div className="w-full min-w-0 max-w-full lg:pl-[272px]">
        {isMain && <LayoutAppHeader current={current.name} day={day} menuOpen={sidebarOpen} onMenu={()=>setSidebarOpen(true)} onProfile={()=>goMain("Profil")}/>}
        <main className={`mx-auto w-full min-w-0 max-w-full 2xl:max-w-[1680px] ${isMain?"pb-[calc(96px+env(safe-area-inset-bottom))] lg:pb-8":"pb-8"}`}>
          {renderPage()}
        </main>
        {isMain && <LayoutBottomNavigation current={current.name} navigate={route=>goMain(route as RouteName)}/>}
      </div>
      {globalToast && (
        <div className="fixed bottom-20 right-4 z-[1400] rounded-xl border border-white/[0.07] bg-[#1A222D] px-4 py-3 text-[13px] font-semibold text-[#F3F5F7] shadow-[0_8px_24px_rgba(0,0,0,.22)] lg:bottom-6">
          <Check size={14} className="inline text-[#D4AF37] mr-1.5"/>{globalToast}
        </div>
      )}
      {loading&&<div aria-label="Menyinkronkan data" className="fixed inset-x-0 top-0 z-[120] h-0.5 overflow-hidden bg-[#D4AF37]/10"><span className="block h-full w-1/3 animate-pulse rounded-full bg-gradient-to-r from-[#D4AF37] to-[#E8C65A]"/></div>}
      {error&&<div className="fixed inset-x-0 top-16 z-[90] mx-auto max-w-lg"><ErrorState message={error} onRetry={()=>void refresh()}/></div>}
    </div>
  );
}
