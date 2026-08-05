import { useState } from "react";
import { MapPin, BellRing, Camera, Navigation } from "lucide-react";
import { PrimaryButton } from "./shared";
import type { NavProps, Screen } from "./shared";
import { featureFlags, isFeatureEnabled } from "@/config/featureFlags";

interface PermConfig {
  icon: typeof MapPin;
  title: string;
  desc: string;
  note: string;
  next: Screen;
}

const PERMS: Record<string, PermConfig> = {
  "perm-location": {
    icon: MapPin,
    title: "Izin Lokasi",
    desc: "Aplikasi membutuhkan akses lokasi untuk mencatat perjalanan marketing.",
    note: "Lokasi digunakan selama Anda login dan tracking perjalanan aktif.",
    next: "perm-bg",
  },
  "perm-bg": {
    icon: Navigation,
    title: "Izin Lokasi Latar Belakang",
    desc: "Izinkan aplikasi mencatat perjalanan saat aplikasi berada di latar belakang.",
    note: "Fitur ini diperlukan agar rute perjalanan tercatat secara akurat sepanjang hari kerja.",
    next: "perm-notification",
  },
  "perm-notification": {
    icon: BellRing,
    title: "Izin Notifikasi",
    desc: "Notifikasi digunakan untuk memberi tahu bahwa tracking sedang aktif.",
    note: "Notifikasi tidak akan mengganggu, hanya tampil saat tracking berjalan.",
    next: "perm-camera",
  },
  "perm-camera": {
    icon: Camera,
    title: "Izin Kamera",
    desc: "Kamera digunakan untuk mengambil foto laporan kunjungan.",
    note: "Foto disimpan bersama laporan dan dikirim ke server saat internet aktif.",
    next: "dashboard",
  },
};

const STEPS: Screen[] = ["perm-location", "perm-bg", "perm-notification", "perm-camera"];

export function PermissionScreen({ navigate, goBack, resetTo, offline, params, screen }: NavProps & { screen: Screen }) {
  const config = PERMS[screen];
  if (!config) return null;

  const Icon = config.icon;
  const stepIndex = STEPS.indexOf(screen);
  const isLast = screen === "perm-camera";
  const [showTrackingModal, setShowTrackingModal] = useState(false);
  const needsLocation = screen === "perm-location" || screen === "perm-bg";

  const handleAllow = () => {
    if (!isFeatureEnabled(featureFlags.mobile.separatePermissionOnboarding)) { resetTo("dashboard"); return; }
    if (isLast) resetTo("dashboard"); else navigate(config.next);
  };

  const handleSkip = () => {
    if (needsLocation) { setShowTrackingModal(true); return; }
    if (isLast) resetTo("dashboard"); else navigate(config.next);
  };

  return (
    <div className="flex flex-col h-full bg-background">
      {isFeatureEnabled(featureFlags.mobile.separatePermissionOnboarding) && <div className="flex justify-center gap-1.5 pt-10 shrink-0">
        {STEPS.map((s, i) => <div key={s} className={`h-1.5 rounded-full transition-all ${i === stepIndex ? "w-6 bg-gold" : i < stepIndex ? "w-1.5 bg-gold/50" : "w-1.5 bg-gold/20"}`} />)}
      </div>}

      {/* Content */}
      <div className="flex-1 flex flex-col items-center justify-center px-8 text-center">
        {/* Icon */}
        <div className="relative mb-8">
          <div className="absolute inset-0 scale-125 rounded-full bg-gold/8" />
          <div className="relative flex items-center justify-center w-24 h-24 rounded-full bg-card-elevated">
            <Icon size={40} className="text-gold" />
          </div>
        </div>

        <span className="text-[11px] font-bold uppercase tracking-[0.18em] text-gold-light">Izin Diperlukan</span>
        <h1 className="mt-2 text-[22px] font-extrabold text-foreground leading-snug">{config.title}</h1>
        <p className="mt-3 text-[14px] text-muted-foreground leading-relaxed max-w-[280px]">{config.desc}</p>
        <p className="mt-3 text-[12px] text-muted-foreground/80 leading-relaxed max-w-[260px]">{config.note}</p>
      </div>

      {/* Actions */}
      <div className="px-6 pb-10 space-y-3 shrink-0">
        <PrimaryButton onClick={handleAllow}>Izinkan</PrimaryButton>
        <button onClick={handleSkip} className="w-full py-3 min-h-11 text-[14px] font-semibold text-muted-foreground">
          Nanti Saja
        </button>
      </div>
      {showTrackingModal && <div className="absolute inset-0 z-20 flex items-end bg-black/60 p-4"><div className="w-full rounded-t-[24px] border border-border bg-card-elevated p-5 text-left"><h2 className="text-[18px] font-extrabold text-foreground">Tracking belum aktif</h2><p className="mt-2 text-[14px] leading-relaxed text-muted-foreground">Aplikasi membutuhkan izin lokasi untuk mencatat perjalanan marketing.</p><div className="mt-5 space-y-2"><PrimaryButton onClick={() => { setShowTrackingModal(false); handleAllow(); }}>Izinkan Sekarang</PrimaryButton><button onClick={() => resetTo("dashboard", { trackingDisabled: true })} className="w-full py-3 min-h-11 text-[14px] font-bold text-muted-foreground">Lanjutkan Tanpa Tracking</button></div></div></div>}
    </div>
  );
}
