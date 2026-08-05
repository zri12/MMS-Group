import { useState } from "react";
import { Cloud, CloudOff, MapPin, FileText, Image, RefreshCw, CheckCircle } from "lucide-react";
import { PageHeader, StatusBadge, PrimaryButton } from "./shared";
import type { NavProps } from "./shared";

type SyncState = "pending" | "syncing" | "success" | "no-connection";

interface QueueItem {
  type: string;
  label: string;
  time: string;
  status: string;
  icon: typeof Cloud;
}

const QUEUE: QueueItem[] = [
  { type: "Laporan", label: "Kunjungan — Siti Nurjanah", time: "08:12", status: "Menunggu Sinkronisasi", icon: FileText },
  { type: "Titik GPS", label: "247 titik perjalanan hari ini", time: "09:30", status: "Menunggu Sinkronisasi", icon: MapPin },
  { type: "Foto", label: "Foto laporan — Ahmad Hidayat", time: "08:45", status: "Menunggu Sinkronisasi", icon: Image },
];

export function SyncStatusScreen({ goBack, offline }: NavProps) {
  const [syncState, setSyncState] = useState<SyncState>(offline ? "no-connection" : "pending");

  const handleSync = () => {
    if (offline) { setSyncState("no-connection"); return; }
    setSyncState("syncing");
    setTimeout(() => setSyncState("success"), 2500);
  };

  const allSynced = syncState === "success";

  const summaryCards = [
    { label: "Titik Lokasi", count: allSynced ? "0" : "247", icon: MapPin, color: "text-[#7fa8f2] bg-[#1a2740]" },
    { label: "Laporan", count: allSynced ? "0" : "1", icon: FileText, color: "text-warning bg-[#3a2b0d]" },
    { label: "Foto", count: allSynced ? "0" : "1", icon: Image, color: "text-gold-light bg-[#332a10]" },
  ];

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Status Sinkronisasi" onBack={goBack} />

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        {/* Main status card */}
        <div className="overflow-hidden rounded-[18px] border border-border bg-card shadow-[0_4px_16px_rgba(0,0,0,0.25)]">
          <div className={`h-1 w-full ${allSynced ? "bg-success" : offline ? "bg-warning" : "bg-gold"}`} />
          <div className="p-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-[11px] font-bold uppercase tracking-[0.14em] text-gold-light">Status Sinkronisasi</p>
                <h2 className="mt-1.5 text-[18px] font-extrabold text-foreground tracking-tight">
                  {syncState === "syncing" ? "Sedang Sinkronisasi..." :
                   allSynced ? "Semua Data Tersinkron" :
                   syncState === "no-connection" ? "Tidak Ada Koneksi" :
                   "Data Menunggu Sinkronisasi"}
                </h2>
                <p className="mt-1 text-[13px] text-muted-foreground">
                  {allSynced ? "Sinkronisasi terakhir: 21 Jul 2026, 09:24" :
                   syncState === "syncing" ? "Mengirim data ke server..." :
                   syncState === "no-connection" ? "Pastikan internet aktif lalu coba sinkronkan ulang." :
                   "Sinkronisasi terakhir: 21 Jul 2026, 07:58"}
                </p>
              </div>
              <div className={`flex items-center justify-center w-12 h-12 rounded-full ${allSynced ? "bg-[#0f2a1a] text-success" : offline ? "bg-[#3a2b0d] text-warning" : "bg-card-elevated text-gold-light"}`}>
                {allSynced ? <CheckCircle size={22} /> : offline ? <CloudOff size={22} /> :
                 syncState === "syncing" ? <RefreshCw size={22} className="animate-spin" /> : <Cloud size={22} />}
              </div>
            </div>
          </div>
        </div>

        {/* Summary counters */}
        <div className="grid grid-cols-3 gap-2.5">
          {summaryCards.map(({ label, count, icon: Icon, color }) => (
            <div key={label} className="rounded-2xl border border-border bg-card p-3">
              <div className={`flex items-center justify-center w-8 h-8 rounded-lg ${color}`}>
                <Icon size={15} />
              </div>
              <p className="mt-2 font-['DM_Mono'] text-[20px] font-bold text-foreground leading-none">{count}</p>
              <p className="mt-1 text-[11px] text-muted-foreground leading-snug">{label}</p>
            </div>
          ))}
        </div>

        {/* Queue */}
        {!allSynced && (
          <div className="rounded-2xl border border-border bg-card overflow-hidden">
            <div className="px-4 pt-4 pb-2 flex items-center justify-between">
              <h3 className="text-[14px] font-extrabold text-foreground">Antrean Sinkronisasi</h3>
              <span className="text-[12px] font-bold text-warning">{QUEUE.length} item</span>
            </div>
            {QUEUE.map((item, i) => (
              <div key={i} className="flex items-center gap-3 px-4 py-3.5 border-t border-border">
                <div className="flex items-center justify-center w-9 h-9 rounded-xl bg-card-elevated text-muted-foreground shrink-0">
                  <item.icon size={16} />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-[13px] font-bold text-foreground truncate">{item.label}</p>
                  <div className="mt-0.5 flex items-center gap-1.5">
                    <span className="text-[11px] text-muted-foreground">{item.type}</span>
                    <span className="text-disabled">·</span>
                    <span className="font-['DM_Mono'] text-[11px] text-muted-foreground">{item.time}</span>
                  </div>
                </div>
                <StatusBadge status={syncState === "syncing" ? "Menunggu Sinkronisasi" : item.status} sm />
              </div>
            ))}
          </div>
        )}

        {/* Synced result */}
        {allSynced && (
          <div className="flex flex-col items-center gap-3 rounded-2xl border border-success/30 bg-[#0f2a1a] p-6 text-center">
            <CheckCircle size={32} className="text-success" />
            <p className="text-[15px] font-extrabold text-success">Semua data berhasil dikirim!</p>
            <p className="text-[13px] text-[#86efac] leading-snug">Data lapangan, titik GPS, dan foto telah tersinkron ke server.</p>
          </div>
        )}

        {/* No connection state */}
        {syncState === "no-connection" && (
          <div className="flex items-start gap-2.5 rounded-2xl bg-[#3a2b0d] border border-warning/30 p-3.5">
            <CloudOff size={15} className="text-warning mt-0.5 shrink-0" />
            <p className="text-[13px] text-[#fcd9a4] leading-snug">Tidak ada koneksi internet. Semua data tersimpan di perangkat dan akan dikirim otomatis saat internet aktif.</p>
          </div>
        )}

        {/* Sync button */}
        <div className="pb-[calc(88px+env(safe-area-inset-bottom))]">
          <PrimaryButton onClick={handleSync} disabled={syncState === "syncing" || allSynced}>
            <span className="flex items-center justify-center gap-2">
              {syncState === "syncing"
                ? <><RefreshCw size={16} className="animate-spin" /> Menyinkronkan...</>
                : allSynced
                  ? <><CheckCircle size={16} /> Semua Tersinkron</>
                  : <><RefreshCw size={16} /> Sinkronkan Sekarang</>}
            </span>
          </PrimaryButton>
        </div>
      </div>
    </div>
  );
}
