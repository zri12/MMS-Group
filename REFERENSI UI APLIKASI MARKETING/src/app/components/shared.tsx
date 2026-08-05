import { ChevronLeft, WifiOff, MapPin, Camera, Image as ImageIcon, X, RefreshCw, AlertCircle } from "lucide-react";
import type { LucideIcon } from "lucide-react";
import type { ReactNode } from "react";
import logoKsp from "@/assets/logo-ksp.jpg";

// ── Navigation Types ──────────────────────────────────

export type Screen =
  | "splash" | "login"
  | "perm-location" | "perm-bg" | "perm-notification" | "perm-camera"
  | "dashboard" | "tracking-detail"
  | "consumers" | "consumer-detail" | "add-consumer" | "edit-consumer"
  | "members" | "member-detail" | "add-member"
  | "jadwal"
  | "setoran"
  | "reports" | "new-report" | "report-detail"
  | "history" | "journey-detail"
  | "sync-status"
  | "profile" | "change-password" | "permission-status" | "account-info" | "usage-guide" | "about";

export interface NavParams {
  consumerId?: string;
  reportId?: string;
  journeyId?: string;
  memberId?: string;
  trackingDisabled?: boolean;
}

export interface NavProps {
  navigate: (screen: Screen, params?: NavParams) => void;
  goBack: () => void;
  resetTo: (screen: Screen, params?: NavParams) => void;
  offline: boolean;
  params?: NavParams;
}

// ── Form Screen Layout ────────────────────────────────
// Screens that render their own full-screen form with a sticky Simpan/Batal
// action bar. This is layout metadata only (spacing/action-bar placement) —
// it must never be used to hide the bottom navigation, which is persistent
// on every screen after login.
export const FORM_SCREENS: Screen[] = [
  "add-consumer",
  "edit-consumer",
  "add-member",
  "setoran",
  "new-report",
  "change-password",
];

// ── Data Types ────────────────────────────────────────

export interface Consumer {
  id: string;
  name: string;
  phone: string;
  address: string;
  business: string;
  status: string;
  lastVisit: string;
  notes: string;
  initials: string;
}

export interface Report {
  id: string;
  consumer: string;
  result: string;
  time: string;
  date: string;
  status: string;
  location: string;
  notes: string;
}

export interface Journey {
  id: string;
  date: string;
  dateLabel: string;
  startTime: string;
  endTime: string | null;
  distance: string;
  points: number;
  visits: number;
  status: string;
  syncStatus: string;
}

export interface Member {
  id: string;
  resort: string;
  tanggal: string;
  nama: string;
  nomorAnggota: string;
  nomorPinjaman: string;
  alamat: string;
  noHp: string;
  usaha: string;
  pinjaman: number;
  angsuran: number;
  asuransi: number;
  jaminan: string;
  accStatus: "Menunggu" | "Disetujui" | "Ditolak";
  hasPhoto: boolean;
  locationAddress: string;
  locationCoords: string;
  locationUpdatedAt: string;
  initials: string;
}

export interface ScheduleItem {
  id: string;
  time: string;
  title: string;
  subtitle: string;
  location: string;
  status: "Belum Dikunjungi" | "Berlangsung" | "Selesai";
}

export interface Deposit {
  id: string;
  memberName: string;
  nominal: number;
  tanggal: string;
  catatan: string;
  status: string;
}

// ── Mock Data ─────────────────────────────────────────

export const mockConsumers: Consumer[] = [
  { id: "1", name: "Ahmad Hidayat", phone: "0812-3456-7890", address: "Gedebage, Kota Bandung", business: "Toko Kelontong", status: "Tertarik", lastVisit: "21 Jul 2026", notes: "Tertarik dengan produk tabungan", initials: "AH" },
  { id: "2", name: "Siti Nurjanah", phone: "0856-7890-1234", address: "Rancasari, Kota Bandung", business: "Ibu Rumah Tangga", status: "Perlu Follow Up", lastVisit: "20 Jul 2026", notes: "Perlu diskusi lebih lanjut", initials: "SN" },
  { id: "3", name: "Dedi Kurniawan", phone: "0878-2345-6789", address: "Buahbatu, Kota Bandung", business: "Petani", status: "Selesai", lastVisit: "19 Jul 2026", notes: "Sudah bergabung sebagai anggota", initials: "DK" },
  { id: "4", name: "Toko Berkah Jaya", phone: "0821-9876-5432", address: "Gedebage, Kota Bandung", business: "Toko Sembako", status: "Tertarik", lastVisit: "21 Jul 2026", notes: "Tertarik produk pinjaman modal usaha", initials: "TB" },
  { id: "5", name: "Rina Wahyuni", phone: "0815-4567-8901", address: "Cibiru, Kota Bandung", business: "Warung Makan", status: "Baru", lastVisit: "21 Jul 2026", notes: "Kunjungan pertama hari ini", initials: "RW" },
];

export const mockReports: Report[] = [
  { id: "1", consumer: "Toko Berkah Jaya", result: "Tertarik", time: "09:18", date: "21 Jul 2026", status: "Terkirim", location: "Gedebage, Bandung", notes: "Konsumen tertarik dengan produk pinjaman modal usaha." },
  { id: "2", consumer: "Ahmad Hidayat", result: "Berhasil Bertemu", time: "08:45", date: "21 Jul 2026", status: "Terkirim", location: "Gedebage, Bandung", notes: "Berhasil bertemu dan presentasi produk tabungan." },
  { id: "3", consumer: "Siti Nurjanah", result: "Perlu Follow Up", time: "08:12", date: "21 Jul 2026", status: "Menunggu Sinkronisasi", location: "Rancasari, Bandung", notes: "Perlu kunjungan lanjutan minggu depan." },
  { id: "4", consumer: "Dedi Kurniawan", result: "Transaksi Selesai", time: "16:30", date: "20 Jul 2026", status: "Terkirim", location: "Buahbatu, Bandung", notes: "Proses pendaftaran anggota selesai." },
  { id: "5", consumer: "Rina Wahyuni", result: "Tidak Bertemu", time: "10:00", date: "19 Jul 2026", status: "Draft", location: "Cibiru, Bandung", notes: "Konsumen sedang tidak di tempat." },
];

export const mockJourneys: Journey[] = [
  { id: "1", date: "Hari ini", dateLabel: "21 Jul 2026", startTime: "08:05", endTime: null, distance: "8,4 km", points: 247, visits: 3, status: "Aktif", syncStatus: "Menunggu Sinkronisasi" },
  { id: "2", date: "20 Juli 2026", dateLabel: "20 Jul 2026", startTime: "08:12", endTime: "17:04", distance: "21,7 km", points: 892, visits: 7, status: "Selesai", syncStatus: "Terkirim" },
  { id: "3", date: "19 Juli 2026", dateLabel: "19 Jul 2026", startTime: "08:08", endTime: "16:49", distance: "18,2 km", points: 756, visits: 5, status: "Selesai", syncStatus: "Terkirim" },
];

export const mockMembers: Member[] = [
  { id: "1", resort: "Resort 3", tanggal: "18 Jul 2026", nama: "Wawan Setiawan", nomorAnggota: "0453", nomorPinjaman: "P-11029", alamat: "Jl. Soekarno Hatta No. 24, Gedebage, Kota Bandung", noHp: "0813-2211-0098", usaha: "Warung sembako", pinjaman: 10000000, angsuran: 950000, asuransi: 150000, jaminan: "BPKB Motor", accStatus: "Disetujui", hasPhoto: true, locationAddress: "Gedebage, Kota Bandung", locationCoords: "-6.9500, 107.6800", locationUpdatedAt: "18 Jul 2026, 09:05", initials: "WS" },
  { id: "2", resort: "Resort 3", tanggal: "20 Jul 2026", nama: "Yuli Astuti", nomorAnggota: "0461", nomorPinjaman: "P-11035", alamat: "Jl. Rancasari Indah No. 7, Kota Bandung", noHp: "0857-4433-2210", usaha: "Jahit pakaian", pinjaman: 5000000, angsuran: 475000, asuransi: 75000, jaminan: "BPKB Motor", accStatus: "Menunggu", hasPhoto: true, locationAddress: "Rancasari, Kota Bandung", locationCoords: "-6.9611, 107.7089", locationUpdatedAt: "20 Jul 2026, 10:40", initials: "YA" },
  { id: "3", resort: "Resort 3", tanggal: "21 Jul 2026", nama: "Herman Malik", nomorAnggota: "0468", nomorPinjaman: "P-11042", alamat: "Jl. Buahbatu Baru No. 12, Kota Bandung", noHp: "0821-5567-8890", usaha: "Bengkel motor", pinjaman: 15000000, angsuran: 1425000, asuransi: 225000, jaminan: "BPKB Mobil", accStatus: "Menunggu", hasPhoto: false, locationAddress: "Buahbatu, Kota Bandung", locationCoords: "-6.9725, 107.6472", locationUpdatedAt: "21 Jul 2026, 08:52", initials: "HM" },
];

export const mockSchedule: ScheduleItem[] = [
  { id: "1", time: "08.30", title: "Kunjungan Ahmad Hidayat", subtitle: "Presentasi produk tabungan", location: "Gedebage, Kota Bandung", status: "Selesai" },
  { id: "2", time: "10.00", title: "Survei calon anggota baru", subtitle: "Herman Malik — pengajuan pinjaman", location: "Buahbatu, Kota Bandung", status: "Berlangsung" },
  { id: "3", time: "13.30", title: "Follow up Siti Nurjanah", subtitle: "Diskusi lanjutan produk simpanan", location: "Rancasari, Kota Bandung", status: "Belum Dikunjungi" },
  { id: "4", time: "15.30", title: "Penagihan setoran Wawan Setiawan", subtitle: "Setoran angsuran bulanan", location: "Gedebage, Kota Bandung", status: "Belum Dikunjungi" },
];

export const mockDeposits: Deposit[] = [
  { id: "1", memberName: "Wawan Setiawan", nominal: 950000, tanggal: "21 Jul 2026", catatan: "Angsuran bulan Juli", status: "Menunggu Sinkronisasi" },
];

// ── Status Map ────────────────────────────────────────

const BADGE_MAP: Record<string, string> = {
  Baru: "bg-[#1a2740] text-[#7fa8f2]",
  Tertarik: "bg-[#332a10] text-[#f3d97a]",
  "Perlu Follow Up": "bg-[#3a2612] text-[#f0a869]",
  "Tidak Tertarik": "bg-[#1b2128] text-[#b5bbc3]",
  Selesai: "bg-[#0f2a1a] text-[#4ade80]",
  Terkirim: "bg-[#0f2a1a] text-[#4ade80]",
  "Menunggu Sinkronisasi": "bg-[#3a2b0d] text-[#f59e0b]",
  "Belum Dikunjungi": "bg-[#1b2128] text-[#b5bbc3]",
  Berlangsung: "bg-[#332a10] text-[#f3d97a]",
  Draft: "bg-[#1b2128] text-[#b5bbc3]",
  Gagal: "bg-[#3a1414] text-[#f87171]",
  Aktif: "bg-[#0f2a1a] text-[#4ade80]",
  Diizinkan: "bg-[#0f2a1a] text-[#4ade80]",
  "Berhasil Bertemu": "bg-[#0f2a1a] text-[#4ade80]",
  "Tidak Bertemu": "bg-[#3a1414] text-[#f87171]",
  "Transaksi Selesai": "bg-[#0f2a1a] text-[#4ade80]",
  Menunggu: "bg-[#3a2b0d] text-[#f59e0b]",
  Disetujui: "bg-[#0f2a1a] text-[#4ade80]",
  Ditolak: "bg-[#3a1414] text-[#f87171]",
};

// ── Components ────────────────────────────────────────

export function KspLogo({ size = "md" }: { size?: "sm" | "md" | "lg" }) {
  const s = { sm: "w-9 h-9", md: "w-12 h-12", lg: "w-16 h-16" }[size];
  return (
    <div className={`${s} shrink-0 overflow-hidden rounded-full border-2 border-gold bg-[#fdf8ec] p-1`}>
      <img src={logoKsp} alt="Logo KSP Manunggal Makmur Sejahtera" className="h-full w-full rounded-full object-contain" />
    </div>
  );
}

export function OfflineBanner({ offline }: { offline: boolean }) {
  if (!offline) return null;
  return (
    <div className="flex items-center gap-2.5 bg-[#3a2b0d] px-4 py-3 border-b border-warning/30 shrink-0">
      <WifiOff size={15} className="text-warning shrink-0" />
      <p className="text-[13px] font-semibold text-[#fcd9a4] leading-snug">Tidak ada koneksi internet. Data tetap disimpan di perangkat.</p>
    </div>
  );
}

export function PageHeader({ title, subtitle, onBack, action }: {
  title: string; subtitle?: string; onBack: () => void; action?: ReactNode;
}) {
  return (
    <header className="flex items-center gap-2 border-b border-border bg-background px-3 py-3 shrink-0">
      <button onClick={onBack} className="flex items-center justify-center w-11 h-11 rounded-xl active:bg-card shrink-0" aria-label="Kembali">
        <ChevronLeft size={22} className="text-foreground" />
      </button>
      <div className="min-w-0 flex-1">
        <h1 className="text-[18px] font-extrabold tracking-tight leading-tight truncate text-foreground">{title}</h1>
        {subtitle && <p className="text-[12px] text-muted-foreground mt-0.5">{subtitle}</p>}
      </div>
      {action && <div className="shrink-0">{action}</div>}
    </header>
  );
}

export function StatusBadge({ status, sm = false }: { status: string; sm?: boolean }) {
  return (
    <span className={`inline-flex rounded-full font-bold whitespace-nowrap ${sm ? "px-2 py-0.5 text-[11px]" : "px-2.5 py-1 text-[12px]"} ${BADGE_MAP[status] ?? "bg-card-elevated text-muted-foreground"}`}>
      {status}
    </span>
  );
}

export function StatusDot({ type = "online" }: { type?: "online" | "offline" | "warning" }) {
  const c = { online: "bg-success shadow-[0_0_0_3px_rgba(34,197,94,0.18)]", offline: "bg-destructive", warning: "bg-warning" }[type];
  return <span className={`inline-flex size-2 rounded-full shrink-0 ${c}`} />;
}

export function PrimaryButton({ children, onClick, disabled = false, danger = false, loading = false, type = "button" }: {
  children: ReactNode; onClick?: () => void; disabled?: boolean; danger?: boolean; loading?: boolean; type?: "button" | "submit";
}) {
  return (
    <button type={type} onClick={onClick} disabled={disabled || loading}
      className={`h-12 min-h-11 w-full rounded-xl text-[14px] font-extrabold tracking-wide transition active:scale-[0.98] disabled:opacity-50 ${danger ? "bg-destructive text-white" : "bg-gold text-primary-foreground"}`}>
      <span className="flex items-center justify-center gap-2">
        {loading && <RefreshCw size={16} className="animate-spin" />}
        {children}
      </span>
    </button>
  );
}

export function SecondaryButton({ children, onClick }: { children: ReactNode; onClick?: () => void }) {
  return (
    <button onClick={onClick}
      className="h-12 min-h-11 w-full rounded-xl border border-border-gold text-[14px] font-bold text-foreground transition active:bg-card">
      {children}
    </button>
  );
}

export function InputField({ label, type = "text", value, onChange, placeholder = "", required = false, error = "", suffix, inputMode, fieldRef }: {
  label: string; type?: string; value: string; onChange: (v: string) => void; placeholder?: string;
  required?: boolean; error?: string; suffix?: ReactNode; inputMode?: "text" | "tel" | "numeric" | "decimal";
  fieldRef?: (el: HTMLDivElement | null) => void;
}) {
  return (
    <div ref={fieldRef}>
      <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">
        {label}{required && <span className="text-destructive ml-0.5">*</span>}
      </label>
      <div className="relative">
        <input type={type} value={value} onChange={e => onChange(e.target.value)} placeholder={placeholder} inputMode={inputMode}
          className={`h-12 min-h-11 w-full rounded-xl border px-4 ${suffix ? "pr-12" : ""} text-[14px] text-foreground outline-none placeholder:text-disabled focus:border-gold transition ${error ? "border-destructive bg-[#1f1414]" : "border-border bg-input-background"}`} />
        {suffix && <div className="absolute right-0 top-0 flex items-center justify-center w-12 h-12">{suffix}</div>}
      </div>
      {error && <p className="mt-1 flex items-center gap-1 text-[12px] text-destructive"><AlertCircle size={12} className="shrink-0" />{error}</p>}
    </div>
  );
}

export function CurrencyField({ label, value, onChange, required = false, error = "", placeholder = "0", fieldRef }: {
  label: string; value: string; onChange: (v: string) => void; required?: boolean; error?: string; placeholder?: string;
  fieldRef?: (el: HTMLDivElement | null) => void;
}) {
  const formatted = value ? Number(value).toLocaleString("id-ID") : "";
  const handleChange = (raw: string) => {
    const digitsOnly = raw.replace(/[^0-9]/g, "");
    onChange(digitsOnly.replace(/^0+(?=\d)/, ""));
  };
  return (
    <div ref={fieldRef}>
      <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">
        {label}{required && <span className="text-destructive ml-0.5">*</span>}
      </label>
      <div className={`flex h-12 min-h-11 w-full items-center rounded-xl border px-4 transition focus-within:border-gold ${error ? "border-destructive bg-[#1f1414]" : "border-border bg-input-background"}`}>
        <span className="mr-1.5 text-[14px] font-bold text-muted-foreground">Rp</span>
        <input
          type="text"
          inputMode="numeric"
          value={formatted}
          onChange={e => handleChange(e.target.value)}
          placeholder={placeholder}
          className="w-full bg-transparent text-[14px] text-foreground outline-none placeholder:text-disabled"
        />
      </div>
      {error && <p className="mt-1 flex items-center gap-1 text-[12px] text-destructive"><AlertCircle size={12} className="shrink-0" />{error}</p>}
    </div>
  );
}

export function SelectField({ label, value, onChange, options, required = false, error = "", disabled = false, fieldRef }: {
  label: string; value: string; onChange: (v: string) => void; options: string[]; required?: boolean; error?: string; disabled?: boolean;
  fieldRef?: (el: HTMLDivElement | null) => void;
}) {
  return (
    <div ref={fieldRef}>
      <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">
        {label}{required && <span className="text-destructive ml-0.5">*</span>}
      </label>
      <select value={value} onChange={e => onChange(e.target.value)} disabled={disabled}
        className={`h-12 min-h-11 w-full rounded-xl border bg-input-background px-4 text-[14px] text-foreground outline-none focus:border-gold disabled:opacity-60 ${error ? "border-destructive bg-[#1f1414]" : "border-border"}`}>
        <option value="">Pilih...</option>
        {options.map(o => <option key={o} value={o}>{o}</option>)}
      </select>
      {error && <p className="mt-1 flex items-center gap-1 text-[12px] text-destructive"><AlertCircle size={12} className="shrink-0" />{error}</p>}
    </div>
  );
}

export function TextareaField({ label, value, onChange, placeholder = "", rows = 3, required = false, error = "", fieldRef }: {
  label: string; value: string; onChange: (v: string) => void; placeholder?: string; rows?: number; required?: boolean; error?: string;
  fieldRef?: (el: HTMLDivElement | null) => void;
}) {
  return (
    <div ref={fieldRef}>
      <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">
        {label}{required && <span className="text-destructive ml-0.5">*</span>}
      </label>
      <textarea value={value} onChange={e => onChange(e.target.value)} placeholder={placeholder} rows={rows}
        className={`w-full rounded-xl border bg-input-background px-4 py-3 text-[14px] text-foreground outline-none placeholder:text-disabled focus:border-gold resize-none leading-relaxed ${error ? "border-destructive bg-[#1f1414]" : "border-border"}`} />
      {error && <p className="mt-1 flex items-center gap-1 text-[12px] text-destructive"><AlertCircle size={12} className="shrink-0" />{error}</p>}
    </div>
  );
}

export function Skeleton({ className = "" }: { className?: string }) {
  return <div className={`animate-pulse rounded-xl bg-card-elevated ${className}`} />;
}

export function EmptyState({ icon: Icon, title, desc }: { icon: LucideIcon; title: string; desc: string }) {
  return (
    <div className="flex flex-col items-center justify-center py-16 px-8 text-center">
      <div className="flex items-center justify-center w-16 h-16 rounded-2xl bg-card-elevated">
        <Icon size={28} className="text-gold" />
      </div>
      <p className="mt-4 text-[15px] font-extrabold text-foreground">{title}</p>
      <p className="mt-2 text-[13px] text-muted-foreground leading-relaxed">{desc}</p>
    </div>
  );
}

export function ModalConfirm({ title, desc, confirmLabel, onConfirm, onCancel, cancelLabel = "Batal", danger = false }: {
  title: string; desc: string; confirmLabel: string; onConfirm: () => void; onCancel: () => void; cancelLabel?: string; danger?: boolean;
}) {
  return (
    <div className="absolute inset-0 z-50 flex items-center justify-center bg-black/60 p-6">
      <div className="w-full max-w-[320px] rounded-[18px] border border-border bg-card-elevated p-6 shadow-2xl">
        <h2 className="text-[17px] font-extrabold text-foreground">{title}</h2>
        <p className="mt-2 text-[13px] leading-relaxed text-muted-foreground">{desc}</p>
        <div className="mt-6 flex flex-col gap-2">
          <PrimaryButton danger={danger} onClick={onConfirm}>{confirmLabel}</PrimaryButton>
          <SecondaryButton onClick={onCancel}>{cancelLabel}</SecondaryButton>
        </div>
      </div>
    </div>
  );
}

export function FilterChip({ label, active, onClick }: { label: string; active: boolean; onClick: () => void }) {
  return (
    <button onClick={onClick}
      className={`whitespace-nowrap rounded-full px-3.5 py-2 text-[13px] font-bold transition min-h-9 ${active ? "bg-gold text-primary-foreground" : "bg-card border border-border text-muted-foreground"}`}>
      {label}
    </button>
  );
}

export function InfoRow({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex items-start justify-between gap-4 py-2.5 border-b border-border last:border-0">
      <span className="text-[13px] text-muted-foreground shrink-0">{label}</span>
      <span className="text-[13px] font-bold text-foreground text-right">{value}</span>
    </div>
  );
}

export function SectionCard({ title, children }: { title?: string; children: ReactNode }) {
  return (
    <div className="rounded-2xl border border-border bg-card p-4 space-y-4">
      {title && <h3 className="text-[15px] font-extrabold text-foreground">{title}</h3>}
      {children}
    </div>
  );
}

// ── Photo Picker ──────────────────────────────────────

export function PhotoPickerField({ label, hasPhoto, loading = false, error = "", onTake, onPick, onRemove, fieldRef }: {
  label: string; hasPhoto: boolean; loading?: boolean; error?: string;
  onTake: () => void; onPick: () => void; onRemove: () => void;
  fieldRef?: (el: HTMLDivElement | null) => void;
}) {
  return (
    <div ref={fieldRef}>
      <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">{label}<span className="text-destructive ml-0.5">*</span></label>
      <div className={`overflow-hidden rounded-xl border ${error ? "border-destructive" : "border-border"}`}>
        {loading ? (
          <div className="flex aspect-[3/4] w-full max-w-[180px] flex-col items-center justify-center gap-2 bg-card-elevated">
            <RefreshCw size={22} className="animate-spin text-gold" />
            <p className="text-[12px] text-muted-foreground">Memproses foto...</p>
          </div>
        ) : hasPhoto ? (
          <div className="relative flex aspect-[3/4] w-full max-w-[180px] items-center justify-center bg-card-elevated">
            <div className="flex flex-col items-center gap-1.5 text-muted-foreground">
              <ImageIcon size={28} className="text-gold" />
              <span className="text-[11px]">Pratinjau Foto</span>
            </div>
            <button onClick={onRemove} aria-label="Hapus foto" className="absolute top-2 right-2 flex items-center justify-center w-8 h-8 rounded-full bg-destructive text-white">
              <X size={14} />
            </button>
          </div>
        ) : (
          <div className="flex aspect-[3/4] w-full max-w-[180px] flex-col items-center justify-center gap-2 bg-card-elevated text-muted-foreground">
            <Camera size={26} className="text-disabled" />
            <span className="px-3 text-center text-[11px] leading-snug">Belum ada foto</span>
          </div>
        )}
      </div>
      <div className="mt-2.5 flex gap-2">
        <button onClick={onTake} className="flex flex-1 min-h-11 items-center justify-center gap-1.5 rounded-xl border border-border-gold px-3 py-2.5 text-[13px] font-bold text-foreground active:bg-card">
          <Camera size={15} /> {hasPhoto ? "Ganti" : "Ambil Foto"}
        </button>
        <button onClick={onPick} className="flex flex-1 min-h-11 items-center justify-center gap-1.5 rounded-xl border border-border-gold px-3 py-2.5 text-[13px] font-bold text-foreground active:bg-card">
          <ImageIcon size={15} /> Galeri
        </button>
      </div>
      {hasPhoto && !loading && (
        <button onClick={onRemove} className="mt-2 text-[12px] font-semibold text-destructive">Hapus Foto</button>
      )}
      {error && <p className="mt-1.5 flex items-center gap-1 text-[12px] text-destructive"><AlertCircle size={12} className="shrink-0" />{error}</p>}
    </div>
  );
}

// ── Location Card ─────────────────────────────────────

export function LocationCard({ status, address, coords, updatedAt, onRefresh, refreshing = false }: {
  status: "ok" | "loading" | "error"; address: string; coords: string; updatedAt: string; onRefresh: () => void; refreshing?: boolean;
}) {
  return (
    <SectionCard title="Lokasi Pengambilan Data">
      <div className="flex items-start gap-3">
        <div className="flex items-center justify-center w-9 h-9 rounded-xl bg-card-elevated text-gold shrink-0">
          <MapPin size={17} />
        </div>
        <div className="min-w-0 flex-1 space-y-2">
          <div className="flex items-center gap-2">
            <StatusDot type={status === "ok" ? "online" : status === "error" ? "offline" : "warning"} />
            <span className="text-[13px] font-bold text-foreground">
              {status === "ok" ? "Lokasi berhasil didapatkan" : status === "error" ? "Lokasi belum berhasil didapatkan" : "Mendapatkan lokasi..."}
            </span>
          </div>
          <InfoRow label="Alamat" value={address || "-"} />
          <InfoRow label="Koordinat" value={coords || "-"} />
          <InfoRow label="Diperbarui" value={updatedAt || "-"} />
        </div>
      </div>
      <SecondaryButton onClick={onRefresh}>
        <span className="flex items-center justify-center gap-2">
          <RefreshCw size={15} className={refreshing ? "animate-spin" : ""} /> Perbarui Lokasi
        </span>
      </SecondaryButton>
    </SectionCard>
  );
}

export function formatRupiah(n: number): string {
  return `Rp${n.toLocaleString("id-ID")}`;
}
