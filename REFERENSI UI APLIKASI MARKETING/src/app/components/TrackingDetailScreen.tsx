import { useState } from "react";
import { RefreshCw, Settings, RotateCw, Navigation, Wifi, Cloud, Shield, MapPin, Battery } from "lucide-react";
import { PageHeader, StatusDot, StatusBadge, PrimaryButton, SecondaryButton, InfoRow } from "./shared";
import type { NavProps } from "./shared";
import { featureFlags, isFeatureEnabled } from "@/config/featureFlags";

export function TrackingDetailScreen({ goBack, offline }: NavProps) {
  const [lastUpdated, setLastUpdated] = useState("09:24 WIB");
  const [updating, setUpdating] = useState(false);
  const [notice, setNotice] = useState("");
  const gpsActive = true;
  const permOk = true;

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Detail Tracking" subtitle="Perjalanan Hari Ini" onBack={goBack} />

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        {/* Status Banner */}
        <div className="overflow-hidden rounded-[18px] border border-border-gold bg-card shadow-[0_6px_20px_rgba(0,0,0,0.3)]">
          <div className="h-1 w-full bg-gold" />
          <div className="p-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <StatusDot type={offline ? "warning" : "online"} />
                <p className="text-[11px] font-bold uppercase tracking-[0.14em] text-gold-light">Status Tracking</p>
              </div>
              <StatusBadge status={offline ? "Menunggu Sinkronisasi" : "Aktif"} />
            </div>
            <h2 className="mt-2 text-[20px] font-extrabold text-foreground tracking-tight">
              {offline ? "Rekam Offline" : "Tracking Aktif"}
            </h2>
            <p className="mt-1 text-[13px] text-muted-foreground">Dimulai pukul 08.05 · Lokasi dicatat secara berkala selama tracking aktif.</p>
          </div>
        </div>

        {isFeatureEnabled(featureFlags.mobile.mobileJourneyMap) && (<>
        {/* Dummy route map */}
        <div className="relative h-52 overflow-hidden rounded-2xl border border-border bg-card-elevated">
          <div className="absolute inset-0 opacity-40 [background-image:linear-gradient(25deg,transparent_47%,#20262d_48%,#20262d_53%,transparent_54%),linear-gradient(110deg,transparent_45%,#20262d_46%,#20262d_52%,transparent_53%),linear-gradient(0deg,transparent_48%,#1b2128_49%,#1b2128_51%,transparent_52%)] [background-size:90px_70px,120px_100px,100%_52px]" />
          <svg viewBox="0 0 360 208" className="absolute inset-0 h-full w-full" aria-label="Peta rute perjalanan dummy">
            <path d="M28 164 C80 145 71 93 126 106 S176 39 220 64 S263 143 326 54" fill="none" stroke="#d4af37" strokeWidth="5" strokeLinecap="round" />
            <path d="M28 164 C80 145 71 93 126 106 S176 39 220 64 S263 143 326 54" fill="none" stroke="#f3d97a" strokeWidth="1.5" strokeDasharray="3 7" />
            <circle cx="28" cy="164" r="8" fill="#07090c" stroke="#d4af37" strokeWidth="4" />
            <circle cx="126" cy="106" r="7" fill="#151a20" stroke="#d4af37" strokeWidth="4" />
            <circle cx="220" cy="64" r="9" fill="#d4af37" stroke="#07090c" strokeWidth="3" />
            <circle cx="326" cy="54" r="8" fill="#ef4444" stroke="#151a20" strokeWidth="3" />
          </svg>
          <span className="absolute bottom-4 left-4 rounded-lg bg-card/95 px-2.5 py-1.5 text-[11px] font-bold text-foreground shadow-sm">Mulai · Gedebage</span>
          <span className="absolute left-[31%] top-[45%] rounded-lg bg-card/95 px-2 py-1 text-[10px] font-bold text-muted-foreground shadow-sm">Toko Berkah Jaya</span>
          <span className="absolute right-3 top-3 rounded-full bg-card-elevated px-3 py-1.5 text-[11px] font-bold text-gold-light">8,4 km · 1j 19m</span>
          <span className="absolute bottom-4 right-4 rounded-lg bg-card/95 px-2.5 py-1.5 text-[11px] font-bold text-foreground shadow-sm">Akhir · Rancasari</span>
        </div>
        </>)}

        {/* Indicator Cards */}
        <div className="grid grid-cols-2 gap-2.5">
          {[
            { icon: Navigation, label: "GPS", value: gpsActive ? "Aktif" : "Tidak Aktif", ok: gpsActive },
            { icon: Wifi, label: "Internet", value: offline ? "Terputus" : "Tersambung", ok: !offline },
            { icon: Shield, label: "Izin Lokasi", value: permOk ? "Diizinkan" : "Ditolak", ok: permOk },
            { icon: Cloud, label: "Sinkronisasi", value: offline ? "Menunggu" : "Tersinkron", ok: !offline },
          ].map(({ icon: Icon, label, value, ok }) => (
            <div key={label} className="rounded-2xl border border-border bg-card p-3.5">
              <div className={`flex items-center justify-center w-8 h-8 rounded-lg ${ok ? "bg-[#0f2a1a] text-success" : "bg-[#3a2b0d] text-warning"}`}>
                <Icon size={16} />
              </div>
              <p className="mt-2.5 text-[12px] text-muted-foreground">{label}</p>
              <p className={`mt-0.5 text-[13px] font-extrabold ${ok ? "text-success" : "text-warning"}`}>{value}</p>
            </div>
          ))}
        </div>

        {/* Info List */}
        <div className="rounded-2xl border border-border bg-card overflow-hidden">
          <div className="px-4 pt-4 pb-2">
            <h3 className="text-[14px] font-extrabold text-foreground">Informasi Perjalanan</h3>
          </div>
          <div className="px-4 pb-2">
            <InfoRow label="Waktu Mulai" value="08:05 WIB" />
            <InfoRow label="Lokasi Terakhir" value="Gedebage, Bandung" />
            <InfoRow label="Diperbarui" value={lastUpdated} />
            {isFeatureEnabled(featureFlags.mobile.detailedGpsStatistics) && <><InfoRow label="Titik Lokasi Tersimpan" value="247 titik" /><InfoRow label="Titik Belum Tersinkron" value={offline ? "3 titik" : "0 titik"} /></>}
          </div>
        </div>

        {isFeatureEnabled(featureFlags.mobile.batteryStatistics) && (<>
        {/* Battery Info */}
        <div className="flex items-center gap-3 rounded-2xl border border-border bg-card p-4">
          <div className="flex items-center justify-center w-9 h-9 rounded-xl bg-[#0f2a1a] text-success">
            <Battery size={18} />
          </div>
          <div>
            <p className="text-[13px] font-bold text-foreground">Penggunaan Baterai</p>
            <p className="text-[12px] text-muted-foreground mt-0.5">Tracking dapat meningkatkan penggunaan baterai selama aktif.</p>
          </div>
        </div>
        </>)}

        {/* Offline Map Info */}
        <div className="flex items-start gap-2.5 rounded-2xl bg-[#1a2740] border border-[#2b3f66] p-3.5">
          <MapPin size={15} className="text-[#7fa8f2] mt-0.5 shrink-0" />
          <p className="text-[12px] text-[#c3d4f5] leading-snug">Lokasi dicatat secara berkala selama tracking aktif dan disimpan di perangkat. Data dikirim ke server saat internet aktif.</p>
        </div>

        {/* Actions */}
        <div className="space-y-2.5 pb-[calc(88px+env(safe-area-inset-bottom))]">
          {notice && <p className="rounded-xl bg-[#0f2a1a] px-3 py-2 text-center text-[12px] font-semibold text-success">{notice}</p>}
          <PrimaryButton onClick={() => { setUpdating(true); setNotice(""); setTimeout(() => { setLastUpdated("09:30 WIB"); setUpdating(false); setNotice("Lokasi berhasil diperbarui."); }, 700); }} disabled={updating}>
            <span className="flex items-center justify-center gap-2">
              <RefreshCw size={16} className={updating ? "animate-spin" : ""} /> {updating ? "Memperbarui..." : "Perbarui Lokasi"}
            </span>
          </PrimaryButton>
          {offline ? (
            <SecondaryButton onClick={() => {}}>
              <span className="flex items-center justify-center gap-2">
                <RotateCw size={16} /> Coba Sinkronkan
              </span>
            </SecondaryButton>
          ) : null}
          {!gpsActive && (
            <SecondaryButton onClick={() => {}}>
              <span className="flex items-center justify-center gap-2">
                <Settings size={16} /> Buka Pengaturan GPS
              </span>
            </SecondaryButton>
          )}
        </div>
      </div>
    </div>
  );
}
