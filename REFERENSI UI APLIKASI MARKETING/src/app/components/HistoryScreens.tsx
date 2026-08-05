import { useState } from "react";
import { Route, Navigation, ChevronRight, Clock, MapPin, UsersRound } from "lucide-react";
import { PageHeader, StatusBadge, FilterChip, EmptyState, InfoRow, mockJourneys } from "./shared";
import type { NavProps, Journey } from "./shared";
import { featureFlags, isFeatureEnabled } from "@/config/featureFlags";

const FILTERS = ["Hari Ini", "Minggu Ini", "Bulan Ini", "Pilih Tanggal"];

// ── History List ──────────────────────────────────────

export function HistoryScreen({ navigate, offline }: NavProps) {
  const [filter, setFilter] = useState("Bulan Ini");

  return (
    <div className="flex flex-col h-full">
      {/* Header */}
      <div className="bg-background px-4 pt-5 pb-3 shrink-0 border-b border-border">
        <div className="mb-4">
          <p className="text-[11px] font-bold uppercase tracking-[0.16em] text-gold-light">Perjalanan Anda</p>
          <h1 className="mt-0.5 text-[23px] font-extrabold tracking-tight text-foreground">Riwayat</h1>
        </div>

        {/* Today summary */}
        <div className="rounded-2xl bg-card-elevated p-4 text-foreground">
          <div className="flex items-center gap-2">
            <span className="h-2 w-2 rounded-full bg-success shadow-[0_0_0_3px_rgba(34,197,94,0.2)]" />
            <span className="text-[12px] font-bold text-gold-light">Tracking Aktif</span>
          </div>
          <div className="mt-3 flex items-end justify-between">
            <div>
              <p className="font-['DM_Mono'] text-[26px] font-bold tracking-tight text-foreground leading-none">8,4 km</p>
              <p className="mt-1 text-[12px] text-muted-foreground">Jarak tempuh hari ini</p>
            </div>
            <Navigation size={28} className="text-gold mb-1" />
          </div>
          <div className={`mt-3 grid gap-2 border-t border-border pt-3 ${isFeatureEnabled(featureFlags.mobile.detailedGpsStatistics) ? "grid-cols-3" : "grid-cols-2"}`}>
            {(isFeatureEnabled(featureFlags.mobile.detailedGpsStatistics) ? [["3", "Kunjungan"], ["247", "Titik GPS"], ["08:05", "Mulai"]] : [["3", "Kunjungan"], ["08:05", "Mulai"]]).map(([val, lbl]) => (
              <div key={lbl}>
                <p className="font-['DM_Mono'] text-[15px] font-bold text-foreground leading-none">{val}</p>
                <p className="mt-0.5 text-[11px] text-muted-foreground">{lbl}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Filter chips */}
        <div className="hide-scrollbar flex gap-2 mt-3 overflow-x-auto overscroll-x-contain pb-0.5">
          {FILTERS.map(f => (
            <FilterChip key={f} label={f} active={filter === f} onClick={() => setFilter(f)} />
          ))}
        </div>
      </div>

      {/* List */}
      <div className="hide-scrollbar flex-1 overflow-y-auto overscroll-contain px-4 pt-3 pb-4 space-y-2.5">
        <h2 className="text-[14px] font-extrabold text-foreground">Perjalanan Terakhir</h2>
        {mockJourneys.map(j => (
          <JourneyCard key={j.id} journey={j} onClick={() => navigate("journey-detail", { journeyId: j.id })} />
        ))}
      </div>
    </div>
  );
}

function JourneyCard({ journey, onClick }: { journey: Journey; onClick: () => void }) {
  const isActive = journey.status === "Aktif";
  return (
    <button
      onClick={onClick}
      className="flex w-full items-start gap-3 rounded-2xl border border-border bg-card p-3.5 text-left shadow-[0_2px_10px_rgba(0,0,0,0.2)] active:scale-[0.99] transition"
    >
      <div className={`flex items-center justify-center w-10 h-10 rounded-xl shrink-0 ${isActive ? "bg-card-elevated text-gold-light" : "bg-[#1a2740] text-[#7fa8f2]"}`}>
        <Route size={17} />
      </div>
      <div className="flex-1 min-w-0">
        <div className="flex items-start justify-between gap-2">
          <p className="text-[14px] font-extrabold text-foreground">{journey.date}</p>
          <StatusBadge status={journey.syncStatus} sm />
        </div>
        <div className="mt-1 flex items-center gap-1.5 text-[12px] text-muted-foreground">
          <Clock size={11} className="shrink-0" />
          <span>{journey.startTime} {journey.endTime ? `— ${journey.endTime}` : "— berjalan"}</span>
        </div>
        <div className="mt-1.5 grid grid-cols-3 gap-2">
          {[
            { val: journey.distance, label: "Jarak" },
            ...(isFeatureEnabled(featureFlags.mobile.detailedGpsStatistics) ? [{ val: String(journey.points), label: "Titik" }] : []),
            { val: String(journey.visits), label: "Kunjungan" },
          ].map(({ val, label }) => (
            <div key={label} className="rounded-lg bg-background px-2 py-1 text-center">
              <p className="font-['DM_Mono'] text-[13px] font-bold text-foreground">{val}</p>
              <p className="text-[10px] text-muted-foreground">{label}</p>
            </div>
          ))}
        </div>
      </div>
      <ChevronRight size={16} className="text-disabled shrink-0 mt-1" />
    </button>
  );
}

// ── Journey Detail ────────────────────────────────────

export function JourneyDetailScreen({ goBack, params }: NavProps) {
  const journey = mockJourneys.find(j => j.id === params?.journeyId) ?? mockJourneys[0];
  const isActive = journey.status === "Aktif";

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader
        title="Detail Perjalanan"
        subtitle={journey.dateLabel}
        onBack={goBack}
      />

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        {/* Status */}
        <div className="flex items-center justify-between rounded-2xl border border-border bg-card px-4 py-4">
          <div>
            <p className="text-[12px] text-muted-foreground">Status Perjalanan</p>
            <div className="mt-1 flex items-center gap-2">
              {isActive && <span className="h-2 w-2 rounded-full bg-success animate-pulse shrink-0" />}
              <StatusBadge status={isActive ? "Aktif" : "Selesai"} />
            </div>
          </div>
          <div className={`flex items-center justify-center w-11 h-11 rounded-xl ${isActive ? "bg-card-elevated" : "bg-[#1a2740]"}`}>
            <Navigation size={20} className={isActive ? "text-gold-light" : "text-[#7fa8f2]"} />
          </div>
        </div>

        {isFeatureEnabled(featureFlags.mobile.mobileJourneyMap) && (<>
        {/* Map Placeholder */}
        <div className="relative overflow-hidden rounded-2xl border border-border bg-card-elevated h-48 flex items-center justify-center">
          <div className="absolute inset-0 opacity-20 [background-image:linear-gradient(#888_1px,transparent_1px),linear-gradient(to_right,#888_1px,transparent_1px)] [background-size:24px_24px]" />
          <div className="relative flex flex-col items-center gap-2 text-muted-foreground">
            <Route size={32} className="text-gold" />
            <p className="text-[12px] font-bold">Peta Rute Perjalanan</p>
            <p className="text-[11px] text-muted-foreground">Gedebage → Rancasari → Buahbatu</p>
          </div>
          <div className="absolute bottom-3 right-3 flex items-center gap-1 rounded-full bg-card/90 px-2.5 py-1 text-[11px] font-bold text-foreground">
            <Route size={11} className="text-gold" /> {journey.distance}
          </div>
        </div>
        </>)}

        {/* Stats */}
        {(() => {
          const showMap = isFeatureEnabled(featureFlags.mobile.mobileJourneyMap);
          const stats = showMap
            ? [{ val: journey.distance, label: "Total Jarak", icon: Route }, { val: isActive ? journey.startTime + " — sekarang" : `${journey.startTime} — ${journey.endTime}`, label: "Waktu", icon: Clock }, ...(isFeatureEnabled(featureFlags.mobile.detailedGpsStatistics) ? [{ val: String(journey.points) + " titik", label: "Titik GPS", icon: MapPin }] : []), { val: String(journey.visits) + " tempat", label: "Kunjungan", icon: UsersRound }]
            : [{ val: `${journey.startTime} — ${journey.endTime ?? "berjalan"}`, label: "Waktu", icon: Clock }, { val: journey.id === "1" ? "1 jam 19 menit" : journey.id === "2" ? "8 jam 52 menit" : "8 jam 41 menit", label: "Durasi", icon: Route }, { val: String(journey.visits) + " tempat", label: "Kunjungan", icon: UsersRound }];
          return <div className={`grid gap-2.5 ${stats.length === 3 ? "grid-cols-3" : "grid-cols-2"}`}>{stats.map(({ val, label, icon: Icon }) => (
            <div key={label} className="rounded-2xl border border-border bg-card p-3.5">
              <div className="flex items-center justify-center w-8 h-8 rounded-lg bg-card-elevated text-muted-foreground"><Icon size={15} /></div>
              <p className="mt-2.5 text-[12px] text-muted-foreground">{label}</p><p className="mt-0.5 text-[13px] font-extrabold text-foreground leading-snug">{val}</p>
            </div>
          ))}</div>;
        })()}

        {!isFeatureEnabled(featureFlags.mobile.mobileJourneyMap) && <div className="rounded-2xl border border-border bg-card px-4"><InfoRow label="Status Sinkronisasi" value={journey.syncStatus} /></div>}

        {isFeatureEnabled(featureFlags.mobile.mobileJourneyMap) && <>
        {/* Detail Info */}
        <div className="rounded-2xl border border-border bg-card overflow-hidden">
          <div className="px-4 pt-4 pb-2">
            <h3 className="text-[14px] font-extrabold text-foreground">Informasi Rute</h3>
          </div>
          <div className="px-4 pb-2">
            <InfoRow label="Titik Awal" value="Gedebage, Kota Bandung" />
            <InfoRow label="Titik Akhir" value={isActive ? "—" : "Buahbatu, Kota Bandung"} />
            <InfoRow label="Waktu Mulai" value={`${journey.startTime} WIB`} />
            <InfoRow label="Waktu Selesai" value={journey.endTime ? `${journey.endTime} WIB` : "Sedang berjalan"} />
            <InfoRow label="Status Sinkronisasi" value={journey.syncStatus} />
          </div>
        </div>

        </>}

        {/* Visited Consumers */}
        <div className="rounded-2xl border border-border bg-card overflow-hidden pb-2">
          <div className="px-4 pt-4 pb-2">
            <h3 className="text-[14px] font-extrabold text-foreground">Konsumen yang Dikunjungi</h3>
          </div>
          {[
            { name: "Toko Berkah Jaya", time: "09:18", result: "Tertarik" },
            { name: "Ahmad Hidayat", time: "08:45", result: "Berhasil Bertemu" },
            { name: "Siti Nurjanah", time: "08:12", result: isFeatureEnabled(featureFlags.mobile.advancedCustomerStatus) ? "Perlu Follow Up" : "Follow Up" },
          ].slice(0, journey.visits).map((v, i) => (
            <div key={i} className="flex items-center gap-3 px-4 py-3 border-t border-border">
              <div className="flex items-center justify-center w-8 h-8 rounded-full bg-card-elevated text-muted-foreground shrink-0">
                <UsersRound size={14} />
              </div>
              <div className="flex-1 min-w-0">
                <p className="text-[13px] font-bold text-foreground truncate">{v.name}</p>
                <p className="text-[11px] text-muted-foreground">Pukul {v.time}</p>
              </div>
              <StatusBadge status={v.result} sm />
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
