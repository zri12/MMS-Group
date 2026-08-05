import { useState } from "react";
import { Clock, MapPin, CalendarDays, Save, CheckCircle } from "lucide-react";
import {
  PageHeader, StatusBadge, PrimaryButton, SecondaryButton, EmptyState,
  CurrencyField, InputField, TextareaField, SectionCard, InfoRow,
  mockSchedule,
} from "./shared";
import type { NavProps, ScheduleItem } from "./shared";

// ── Jadwal ────────────────────────────────────────────

export function JadwalScreen({ goBack }: NavProps) {
  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Jadwal Kunjungan" subtitle="Hari ini · 21 Jul 2026" onBack={goBack} />

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-2.5">
        {mockSchedule.length === 0 ? (
          <EmptyState icon={CalendarDays} title="Tidak ada jadwal" desc="Belum ada jadwal kunjungan untuk hari ini" />
        ) : (
          mockSchedule.map(item => <ScheduleCard key={item.id} item={item} />)
        )}
      </div>
    </div>
  );
}

function ScheduleCard({ item }: { item: ScheduleItem }) {
  return (
    <div className="flex items-start gap-3 rounded-2xl border border-border bg-card p-3.5">
      <div className="flex flex-col items-center justify-center w-14 shrink-0">
        <Clock size={14} className="text-gold mb-1" />
        <span className="font-['DM_Mono'] text-[13px] font-bold text-foreground">{item.time}</span>
      </div>
      <div className="min-w-0 flex-1 border-l border-border pl-3">
        <div className="flex items-start justify-between gap-2">
          <p className="text-[14px] font-extrabold text-foreground truncate">{item.title}</p>
          <StatusBadge status={item.status} sm />
        </div>
        <p className="mt-0.5 text-[12px] text-muted-foreground">{item.subtitle}</p>
        <p className="mt-1.5 flex items-center gap-1 text-[12px] text-muted-foreground">
          <MapPin size={11} className="shrink-0" /> {item.location}
        </p>
      </div>
    </div>
  );
}

// ── Setoran (Laporan Setoran Harian) ──────────────────

interface TargetValue { rp: string; orang: string; }

const EMPTY_TARGET: TargetValue = { rp: "", orang: "" };

function digitsOnly(raw: string) {
  return raw.replace(/[^0-9]/g, "").replace(/^0+(?=\d)/, "");
}

function TargetField({ label, value, onChange }: { label: string; value: TargetValue; onChange: (v: TargetValue) => void }) {
  const formattedRp = value.rp ? Number(value.rp).toLocaleString("id-ID") : "";
  return (
    <div>
      <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">{label}</label>
      <div className="flex gap-2">
        <div className="flex h-12 min-h-11 flex-1 items-center rounded-xl border border-border bg-input-background px-4 transition focus-within:border-gold">
          <span className="mr-1.5 text-[14px] font-bold text-muted-foreground">Rp</span>
          <input
            type="text"
            inputMode="numeric"
            value={formattedRp}
            onChange={e => onChange({ ...value, rp: digitsOnly(e.target.value) })}
            placeholder="0"
            className="w-full bg-transparent text-[14px] text-foreground outline-none placeholder:text-disabled"
          />
        </div>
        <div className="flex h-12 min-h-11 w-[104px] shrink-0 items-center rounded-xl border border-border bg-input-background px-3 transition focus-within:border-gold">
          <input
            type="text"
            inputMode="numeric"
            value={value.orang}
            onChange={e => onChange({ ...value, orang: digitsOnly(e.target.value) })}
            placeholder="0"
            className="w-8 shrink-0 bg-transparent text-[14px] text-foreground outline-none placeholder:text-disabled"
          />
          <span className="ml-1 text-[12px] text-muted-foreground">orang</span>
        </div>
      </div>
    </div>
  );
}

export function SetoranScreen({ goBack, navigate, offline }: NavProps) {
  const [tanggal, setTanggal] = useState("21 Juli 2026");
  const [storting, setStorting] = useState("");
  const [asuransi, setAsuransi] = useState("");
  const [drop, setDrop] = useState("");
  const [tabunganKeluar, setTabunganKeluar] = useState("");
  const [targetLama, setTargetLama] = useState<TargetValue>(EMPTY_TARGET);
  const [targetMasuk, setTargetMasuk] = useState<TargetValue>(EMPTY_TARGET);
  const [targetKeluar, setTargetKeluar] = useState<TargetValue>(EMPTY_TARGET);
  const [jumlah, setJumlah] = useState<TargetValue>(EMPTY_TARGET);
  const [dropBaru, setDropBaru] = useState("");
  const [dropLanjut, setDropLanjut] = useState("");
  const [catatan, setCatatan] = useState("");
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);

  const validate = () => {
    const e: Record<string, string> = {};
    if (!tanggal.trim()) e.tanggal = "Tanggal wajib diisi.";
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSave = () => {
    if (saving || !validate()) return;
    setSaving(true);
    setTimeout(() => {
      setSaving(false);
      setSaved(true);
      setTimeout(() => navigate("dashboard"), 1400);
    }, 900);
  };

  if (saved) {
    return (
      <div className="flex flex-col h-full bg-background">
        <PageHeader title="Input Setoran" onBack={() => navigate("dashboard")} />
        <div className="flex-1 flex flex-col items-center justify-center gap-4 px-8 text-center">
          <div className="flex items-center justify-center w-16 h-16 rounded-full bg-[#0f2a1a]">
            <CheckCircle size={30} className="text-success" />
          </div>
          <p className="text-[17px] font-extrabold text-foreground">Setoran berhasil disimpan</p>
          <p className="text-[13px] text-muted-foreground">
            {offline ? "Data disimpan di perangkat dan akan tersinkron saat internet aktif." : "Data setoran telah tersimpan."}
          </p>
        </div>
      </div>
    );
  }

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Input Setoran" subtitle="Laporan setoran harian" onBack={goBack} />

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        {offline && (
          <div className="rounded-xl bg-[#3a2b0d] border border-warning/30 p-3.5 text-[13px] text-[#fcd9a4]">
            Mode offline: data akan disimpan di perangkat dan dikirim saat internet aktif.
          </div>
        )}

        <SectionCard title="Detail Setoran">
          <InputField label="Tanggal" value={tanggal} onChange={setTanggal} required error={errors.tanggal} />
        </SectionCard>

        <SectionCard title="Setoran & Keuangan">
          <CurrencyField label="Storting" value={storting} onChange={setStorting} />
          <CurrencyField label="Asuransi" value={asuransi} onChange={setAsuransi} />
          <CurrencyField label="Drop" value={drop} onChange={setDrop} />
          <CurrencyField label="Tabungan Keluar" value={tabunganKeluar} onChange={setTabunganKeluar} />
        </SectionCard>

        <SectionCard title="Target Anggota">
          <TargetField label="Target Lama" value={targetLama} onChange={setTargetLama} />
          <TargetField label="Target Masuk" value={targetMasuk} onChange={setTargetMasuk} />
          <TargetField label="Target Keluar" value={targetKeluar} onChange={setTargetKeluar} />
          <TargetField label="Jumlah" value={jumlah} onChange={setJumlah} />
        </SectionCard>

        <SectionCard title="Rincian Drop">
          <CurrencyField label="Drop Baru" value={dropBaru} onChange={setDropBaru} />
          <CurrencyField label="Drop Lanjut" value={dropLanjut} onChange={setDropLanjut} />
        </SectionCard>

        <SectionCard title="Catatan">
          <TextareaField label="Catatan" value={catatan} onChange={setCatatan} placeholder="Catatan setoran (opsional)" rows={2} />
        </SectionCard>

        <SectionCard title="Info Otomatis">
          <InfoRow label="Marketing" value="Budi Santoso" />
          <InfoRow label="Kode Marketing" value="KSP-0318" />
          <InfoRow label="Resort" value="Resort 3" />
          <InfoRow label="Waktu Input" value="21 Jul 2026, 09:30" />
        </SectionCard>
      </div>

      {/* Sticky form action bar — pinned above the persistent bottom nav, never inside the scroll area */}
      <div className="shrink-0 border-t border-border bg-background px-4 py-3 space-y-2.5">
        <PrimaryButton onClick={handleSave} loading={saving}>
          <span className="flex items-center justify-center gap-2"><Save size={16} /> Simpan Setoran</span>
        </PrimaryButton>
        <SecondaryButton onClick={goBack}>Batal</SecondaryButton>
      </div>
    </div>
  );
}
