import {
  ChevronRight, Cloud, CloudOff, FileText, MapPin, Navigation,
  Plus, Route, Send, UsersRound, Wifi, Check, CalendarDays, Wallet,
} from "lucide-react";
import { KspLogo, StatusDot, mockSchedule } from "./shared";
import type { NavProps } from "./shared";
import { featureFlags, isFeatureEnabled } from "@/config/featureFlags";

export function DashboardScreen({ navigate, offline, params }: NavProps) {
  const trackingDisabled = Boolean(params?.trackingDisabled);
  const stats = [
    { count: "03", label: "Kunjungan", icon: MapPin, color: "text-[#7fa8f2] bg-[#1a2740]" },
    { count: "02", label: "Laporan Terkirim", icon: Send, color: "text-success bg-[#0f2a1a]" },
    { count: "05", label: "Data Konsumen", icon: UsersRound, color: "text-gold-light bg-[#332a10]" },
    { count: "01", label: "Belum Tersinkron", icon: CloudOff, color: "text-warning bg-[#3a2b0d]" },
  ];

  const todayExtra = [
    { count: "01", label: "Anggota Baru Hari Ini", icon: UsersRound, color: "text-gold-light bg-[#332a10]" },
    { count: "Rp950rb", label: "Setoran Hari Ini", icon: Wallet, color: "text-success bg-[#0f2a1a]" },
  ];

  const quickMenu = [
    { icon: Navigation, top: "Mulai", bottom: "Tracking", action: () => navigate("tracking-detail") },
    { icon: UsersRound, top: "Tambah", bottom: "Anggota", action: () => navigate("add-member") },
    { icon: CalendarDays, top: "Lihat", bottom: "Jadwal", action: () => navigate("jadwal") },
    { icon: Wallet, top: "Input", bottom: "Setoran", action: () => navigate("setoran") },
    { icon: Plus, top: "Tambah", bottom: "Konsumen", action: () => navigate("add-consumer") },
    { icon: FileText, top: "Buat", bottom: "Laporan", action: () => navigate("new-report") },
    { icon: Route, top: "Lihat", bottom: "Riwayat", action: () => navigate("history") },
    ...(isFeatureEnabled(featureFlags.mobile.detailedSyncStatus) ? [{ icon: Cloud, top: "Status", bottom: "Sinkronisasi", action: () => navigate("sync-status") }] : []),
  ];

  const activities = [
    { title: "Laporan kunjungan berhasil dikirim", sub: "Toko Berkah Jaya", time: "09.18", icon: Check, color: "bg-[#0f2a1a] text-success" },
    { title: "Data konsumen baru ditambahkan", sub: "Siti Nurjanah", time: "08.42", icon: UsersRound, color: "bg-[#332a10] text-gold-light" },
    { title: isFeatureEnabled(featureFlags.mobile.detailedGpsStatistics) ? "12 titik lokasi berhasil disinkronkan" : "Data perjalanan berhasil disinkronkan", sub: "Perjalanan pagi ini", time: "08.15", icon: Cloud, color: "bg-[#1a2740] text-[#7fa8f2]" },
  ];

  const scheduleLeft = mockSchedule.filter(s => s.status !== "Selesai").length;

  return (
    <div className="flex flex-col h-full">
      {/* Sticky dark header */}
      <header className="relative overflow-hidden bg-background-secondary px-5 pb-6 pt-5 shrink-0">
        <div className="pointer-events-none absolute -right-16 top-14 h-32 w-[460px] rotate-[-7deg] opacity-40 [background-image:radial-gradient(#d4af37_1px,transparent_1px)] [background-size:7px_7px]" />

        <div className="relative flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <KspLogo size="sm" />
            <div>
              <p className="text-[11px] font-bold tracking-tight text-gold-light leading-snug max-w-[160px]">KSP Manunggal Makmur Sejahtera</p>
              <p className="mt-0.5 text-[10px] text-muted-foreground leading-none">Marketing Monitoring</p>
            </div>
          </div>
          <button onClick={() => navigate("profile")} aria-label="Buka profil" className="flex items-center justify-center w-11 h-11 rounded-full bg-gold text-[13px] font-extrabold text-primary-foreground">BS</button>
        </div>

        <div className="relative mt-5 flex items-end justify-between">
          <div>
            <p className="text-[13px] text-muted-foreground">Selamat pagi,</p>
            <h1 className="mt-0.5 text-[24px] font-extrabold leading-none tracking-tight text-foreground">Budi Santoso</h1>
            <div className="mt-2 flex items-center gap-1.5">
              <StatusDot type={offline ? "warning" : "online"} />
              <p className="text-[12px] text-gold-light">Kode KSP-0318 · Resort 3, Bandung</p>
            </div>
          </div>
          <button onClick={() => navigate("profile")} className="mb-1 flex items-center gap-1 text-[12px] text-muted-foreground">
            Profil <ChevronRight size={14} />
          </button>
        </div>
      </header>

      {/* Scrollable content */}
      <main className="hide-scrollbar flex-1 overflow-y-auto overscroll-contain space-y-5 px-4 py-4">
        {/* Tracking Card */}
        <button
          onClick={() => navigate("tracking-detail")}
          className="w-full overflow-hidden rounded-[18px] border border-border-gold bg-card text-left shadow-[0_6px_20px_rgba(0,0,0,0.3)] active:scale-[0.99] transition"
        >
          <div className="h-1 w-full bg-gold" />
          <div className="p-4">
            <div className="flex items-start justify-between gap-3">
              <div className="flex-1 min-w-0">
                <div className="flex items-center gap-2">
                  <StatusDot type={offline ? "warning" : "online"} />
                  <p className="text-[11px] font-bold uppercase tracking-[0.14em] text-gold-light">Tracking Perjalanan</p>
                </div>
                <h2 className="mt-2 text-[18px] font-extrabold tracking-tight text-foreground">
                  {trackingDisabled ? "Tracking Tidak Aktif" : offline ? "Sedang Offline" : "Tracking Aktif"}
                </h2>
                <p className="mt-1 text-[13px] text-muted-foreground leading-snug">
                  {trackingDisabled ? "Izin lokasi belum diberikan. Aktifkan agar perjalanan dapat dicatat." : offline ? "Lokasi tetap disimpan di perangkat secara lokal." : "Aktif sejak 08.05 · Lokasi dicatat otomatis."}
                </p>
              </div>
              <div className="flex items-center justify-center w-11 h-11 rounded-full bg-card-elevated text-gold shrink-0">
                <Navigation size={20} fill="currentColor" />
              </div>
            </div>

            {trackingDisabled && <button onClick={() => navigate("perm-location")} className="mt-3 w-full min-h-11 rounded-xl bg-gold py-2.5 text-[13px] font-bold text-primary-foreground">Aktifkan Izin Lokasi</button>}
            <div className="mt-4 grid grid-cols-2 gap-3 border-t border-border pt-3">
              <span className="flex items-center gap-1.5 text-[13px] text-foreground">
                <Wifi size={14} className={offline ? "text-warning" : "text-success"} />
                {offline ? "Internet terputus" : "Internet tersambung"}
              </span>
              <span className="flex items-center justify-end gap-1.5 text-[13px] text-foreground">
                <Cloud size={14} className="text-gold" />
                {offline ? "3 data antre" : "Tersinkron"}
              </span>
            </div>

            <div className="mt-2.5 flex items-center justify-end gap-1 text-[12px] text-gold-light font-semibold">
              Lihat detail <ChevronRight size={14} />
            </div>
          </div>
        </button>

        {/* Jadwal Hari Ini */}
        <button
          onClick={() => navigate("jadwal")}
          className="flex w-full items-center gap-3 rounded-2xl border border-border bg-card p-3.5 text-left active:scale-[0.99] transition"
        >
          <div className="flex items-center justify-center w-11 h-11 rounded-xl bg-card-elevated text-gold shrink-0">
            <CalendarDays size={19} />
          </div>
          <div className="min-w-0 flex-1">
            <p className="text-[13px] font-extrabold text-foreground">Jadwal Hari Ini</p>
            <p className="mt-0.5 text-[12px] text-muted-foreground">{scheduleLeft} kunjungan belum diselesaikan</p>
          </div>
          <ChevronRight size={16} className="text-disabled shrink-0" />
        </button>

        {/* Stat Cards */}
        <section>
          <div className="mb-3 flex items-center justify-between">
            <h2 className="text-[16px] font-extrabold tracking-tight text-foreground">Statistik Hari Ini</h2>
            <span className="font-['DM_Mono'] text-[11px] text-muted-foreground">21 JUL 2026</span>
          </div>
          <div className="grid grid-cols-2 gap-2.5">
            {stats.map(({ count, label, icon: Icon, color }) => (
              <div key={label} className="rounded-2xl border border-border bg-card p-3.5 shadow-[0_2px_10px_rgba(0,0,0,0.2)]">
                <div className={`flex items-center justify-center w-8 h-8 rounded-lg ${color}`}>
                  <Icon size={15} />
                </div>
                <p className="mt-3 font-['DM_Mono'] text-[24px] font-bold leading-none tracking-tight text-foreground">{count}</p>
                <p className="mt-1.5 text-[12px] leading-snug text-muted-foreground">{label}</p>
              </div>
            ))}
          </div>
          <p className="mt-2 text-center text-[12px] text-gold-light">2 laporan terkirim, 1 menunggu sinkronisasi.</p>
        </section>

        {/* Anggota & Setoran Hari Ini */}
        <section>
          <div className="grid grid-cols-2 gap-2.5">
            {todayExtra.map(({ count, label, icon: Icon, color }) => (
              <div key={label} className="rounded-2xl border border-border bg-card p-3.5 shadow-[0_2px_10px_rgba(0,0,0,0.2)]">
                <div className={`flex items-center justify-center w-8 h-8 rounded-lg ${color}`}>
                  <Icon size={15} />
                </div>
                <p className="mt-3 text-[18px] font-bold leading-none tracking-tight text-foreground font-['DM_Mono']">{count}</p>
                <p className="mt-1.5 text-[12px] leading-snug text-muted-foreground">{label}</p>
              </div>
            ))}
          </div>
        </section>

        {/* Quick Menu */}
        <section>
          <h2 className="mb-3 text-[16px] font-extrabold tracking-tight text-foreground">Menu Cepat</h2>
          <div className="grid grid-cols-4 gap-2">
            {quickMenu.map(({ icon: Icon, top, bottom, action }) => (
              <button
                key={bottom}
                onClick={action}
                className="flex flex-col items-center rounded-2xl border border-border bg-card px-1 py-3.5 text-center transition active:scale-95 active:bg-card-elevated"
              >
                <span className="flex items-center justify-center w-10 h-10 rounded-xl bg-card-elevated text-gold">
                  <Icon size={18} />
                </span>
                <span className="mt-2 text-[11px] font-bold leading-snug text-foreground">
                  {top}<br />{bottom}
                </span>
              </button>
            ))}
          </div>
        </section>

        {/* Recent Activity */}
        <section className="pb-4">
          <div className="mb-3 flex items-center justify-between">
            <h2 className="text-[16px] font-extrabold tracking-tight text-foreground">Aktivitas Terbaru</h2>
            <button onClick={() => navigate("history")} className="text-[13px] font-bold text-gold-light">Lihat Semua</button>
          </div>
          <div className="overflow-hidden rounded-2xl border border-border bg-card divide-y divide-border">
            {activities.map(({ title, sub, time, icon: Icon, color }) => (
              <div key={title} className="flex items-center gap-3 px-4 py-3.5">
                <span className={`flex items-center justify-center w-8 h-8 rounded-full ${color} shrink-0`}>
                  <Icon size={14} />
                </span>
                <div className="min-w-0 flex-1">
                  <p className="truncate text-[13px] font-bold text-foreground">{title}</p>
                  <p className="mt-0.5 text-[12px] text-muted-foreground">{sub}</p>
                </div>
                <span className="font-['DM_Mono'] text-[11px] text-muted-foreground shrink-0">{time}</span>
              </div>
            ))}
          </div>
        </section>
      </main>
    </div>
  );
}
