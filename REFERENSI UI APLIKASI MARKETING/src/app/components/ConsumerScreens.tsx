import { useState } from "react";
import { Search, SlidersHorizontal, Plus, MapPin, Phone, ChevronRight, FileText, Pencil, UsersRound } from "lucide-react";
import {
  PageHeader, StatusBadge, PrimaryButton, SecondaryButton,
  InputField, SelectField, TextareaField, FilterChip, EmptyState, InfoRow,
  mockConsumers,
} from "./shared";
import type { NavProps, Consumer } from "./shared";
import { featureFlags, isFeatureEnabled, consumerStatusOptions } from "@/config/featureFlags";

const STATUS_OPTIONS = consumerStatusOptions();
const FILTER_OPTIONS = ["Semua", "Hari Ini", "Perlu Follow Up", "Selesai"];

// ── Consumers List ────────────────────────────────────

export function ConsumersScreen({ navigate }: NavProps) {
  const [search, setSearch] = useState("");
  const [filter, setFilter] = useState("Semua");

  const filtered = mockConsumers.filter(c => {
    const matchSearch = c.name.toLowerCase().includes(search.toLowerCase()) || c.address.toLowerCase().includes(search.toLowerCase());
    const matchFilter =
      filter === "Semua" ? true :
      filter === "Hari Ini" ? c.lastVisit === "21 Jul 2026" :
      c.status === filter;
    return matchSearch && matchFilter;
  });

  return (
    <div className="flex flex-col h-full">
      {/* Sticky header */}
      <div className="bg-background px-4 pt-5 pb-3 shrink-0 border-b border-border">
        <div className="flex items-center justify-between mb-4">
          <div>
            <p className="text-[11px] font-bold uppercase tracking-[0.16em] text-gold-light">Data Lapangan</p>
            <h1 className="mt-0.5 text-[23px] font-extrabold tracking-tight text-foreground">Konsumen</h1>
          </div>
          <button
            onClick={() => navigate("add-consumer")}
            className="flex items-center justify-center w-11 h-11 rounded-xl bg-gold text-primary-foreground active:scale-95 transition"
          >
            <Plus size={21} />
          </button>
        </div>

        {/* Search */}
        <div className="flex gap-2">
          <div className="flex flex-1 items-center gap-2 rounded-xl border border-border bg-input-background px-3 h-11">
            <Search size={16} className="text-muted-foreground shrink-0" />
            <input
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="Cari nama atau lokasi"
              className="flex-1 bg-transparent text-[14px] text-foreground outline-none placeholder:text-disabled"
            />
          </div>
          <button className="flex items-center justify-center w-11 h-11 rounded-xl border border-border bg-input-background">
            <SlidersHorizontal size={17} className="text-foreground" />
          </button>
        </div>

        {/* Filter chips */}
        <div className="flex gap-2 mt-3 overflow-x-auto pb-0.5 hide-scrollbar">
          {FILTER_OPTIONS.map(f => (
            <FilterChip key={f} label={f} active={filter === f} onClick={() => setFilter(f)} />
          ))}
        </div>
      </div>

      {/* List */}
      <div className="flex-1 overflow-y-auto px-4 pt-3 pb-4 space-y-2.5">
        {filtered.length === 0 ? (
          <EmptyState icon={UsersRound} title="Tidak ada konsumen" desc="Coba ubah filter atau kata kunci pencarian Anda" />
        ) : (
          filtered.map(c => (
            <ConsumerCard key={c.id} consumer={c} onClick={() => navigate("consumer-detail", { consumerId: c.id })} />
          ))
        )}
      </div>
    </div>
  );
}

function ConsumerCard({ consumer, onClick }: { consumer: Consumer; onClick: () => void }) {
  return (
    <button
      onClick={onClick}
      className="flex w-full items-center gap-3 rounded-2xl border border-border bg-card p-3.5 text-left shadow-[0_2px_10px_rgba(0,0,0,0.2)] active:scale-[0.99] transition"
    >
      <div className="flex items-center justify-center w-11 h-11 rounded-full bg-card-elevated text-[13px] font-extrabold text-gold-light shrink-0">
        {consumer.initials}
      </div>
      <div className="min-w-0 flex-1">
        <p className="text-[14px] font-extrabold text-foreground truncate">{consumer.name}</p>
        <p className="mt-0.5 flex items-center gap-1 text-[12px] text-muted-foreground truncate">
          <MapPin size={11} className="shrink-0" />{consumer.address}
        </p>
        <div className="mt-1.5 flex items-center gap-2">
          <StatusBadge status={consumer.status} sm />
          <span className="text-[11px] text-muted-foreground">{consumer.lastVisit}</span>
        </div>
      </div>
      <ChevronRight size={16} className="text-disabled shrink-0" />
    </button>
  );
}

// ── Consumer Detail ───────────────────────────────────

export function ConsumerDetailScreen({ goBack, navigate, params, offline }: NavProps) {
  const [showContact, setShowContact] = useState(false);
  const [contactNotice, setContactNotice] = useState("");
  const consumer = mockConsumers.find(c => c.id === params?.consumerId) ?? mockConsumers[0];

  return (
    <div className="relative flex flex-col h-full bg-background">
      <PageHeader
        title={consumer.name}
        subtitle={consumer.business || "Konsumen"}
        onBack={goBack}
        action={isFeatureEnabled(featureFlags.mobile.editSubmittedCustomer) ? (
          <button
            onClick={() => navigate("edit-consumer", { consumerId: consumer.id })}
            className="flex items-center justify-center w-10 h-10 rounded-xl border border-border bg-card"
          >
            <Pencil size={16} className="text-foreground" />
          </button>
        ) : undefined}
      />

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        {/* Profile Card */}
        <div className="rounded-2xl border border-border bg-card overflow-hidden">
          <div className="bg-card-elevated px-4 py-4 flex items-center gap-3">
            <div className="flex items-center justify-center w-14 h-14 rounded-full bg-gold text-[18px] font-extrabold text-primary-foreground shrink-0">
              {consumer.initials}
            </div>
            <div>
              <h2 className="text-[17px] font-extrabold text-foreground">{consumer.name}</h2>
              <p className="text-[12px] text-muted-foreground mt-0.5">{consumer.business}</p>
              <div className="mt-1.5">
                <StatusBadge status={consumer.status} />
              </div>
            </div>
          </div>
          <div className="px-4 pb-3">
            <InfoRow label="Nomor Telepon" value={consumer.phone} />
            <InfoRow label="Alamat" value={consumer.address} />
            <InfoRow label="Kunjungan Terakhir" value={consumer.lastVisit} />
          </div>
        </div>

        {/* Notes */}
        <div className="rounded-2xl border border-border bg-card p-4">
          <h3 className="text-[14px] font-extrabold text-foreground mb-2">Catatan</h3>
          <p className="text-[13px] text-muted-foreground leading-relaxed">{consumer.notes}</p>
        </div>

        {/* Visit History */}
        <div className="rounded-2xl border border-border bg-card overflow-hidden">
          <div className="px-4 pt-4 pb-2 flex items-center justify-between">
            <h3 className="text-[14px] font-extrabold text-foreground">Riwayat Kunjungan</h3>
          </div>
          {[
            { date: "21 Jul 2026, 09:18", result: "Tertarik", note: "Tertarik produk pinjaman modal" },
            { date: "14 Jul 2026, 10:30", result: "Berhasil Bertemu", note: "Perkenalan awal produk" },
          ].map((v, i) => (
            <div key={i} className="flex items-start gap-3 px-4 py-3 border-t border-border">
              <div className="w-1.5 h-1.5 rounded-full bg-gold mt-2 shrink-0" />
              <div className="flex-1 min-w-0">
                <p className="text-[12px] text-muted-foreground">{v.date}</p>
                <div className="mt-0.5 flex items-center gap-2">
                  <StatusBadge status={v.result} sm />
                </div>
                <p className="mt-0.5 text-[13px] text-foreground">{v.note}</p>
              </div>
            </div>
          ))}
        </div>

        {/* Actions */}
        <div className="space-y-2.5 pb-[calc(88px+env(safe-area-inset-bottom))]">
          <PrimaryButton onClick={() => navigate("new-report", { consumerId: consumer.id })}>
            <span className="flex items-center justify-center gap-2">
              <FileText size={16} /> Buat Laporan
            </span>
          </PrimaryButton>
          {isFeatureEnabled(featureFlags.mobile.contactCustomer) && <SecondaryButton onClick={() => setShowContact(true)}><span className="flex items-center justify-center gap-2"><Phone size={16} /> Hubungi Konsumen</span></SecondaryButton>}
          {isFeatureEnabled(featureFlags.mobile.editSubmittedCustomer) && <SecondaryButton onClick={() => navigate("edit-consumer", { consumerId: consumer.id })}><span className="flex items-center justify-center gap-2"><Pencil size={16} /> Edit Data</span></SecondaryButton>}
        </div>
      </div>
      {contactNotice && <div className="absolute bottom-5 left-4 right-4 rounded-xl bg-card-elevated border border-border-gold px-4 py-3 text-center text-[13px] font-semibold text-foreground">{contactNotice}</div>}
      {showContact && <div className="absolute inset-0 z-20 flex items-end bg-black/60 p-4"><div className="w-full rounded-t-[24px] border border-border bg-card-elevated p-4 shadow-xl"><h3 className="text-[17px] font-extrabold text-foreground">Hubungi Konsumen</h3><p className="mt-1 text-[13px] text-muted-foreground">{consumer.phone}</p><button onClick={() => { setShowContact(false); setContactNotice("Membuka panggilan untuk " + consumer.phone); }} className="mt-4 w-full min-h-11 rounded-xl bg-gold py-3 text-[14px] font-bold text-primary-foreground">Telepon Konsumen</button><button onClick={() => { setShowContact(false); setContactNotice("Nomor konsumen disalin."); }} className="mt-2 w-full min-h-11 rounded-xl border border-border-gold py-3 text-[14px] font-bold text-foreground">Salin Nomor</button><button onClick={() => setShowContact(false)} className="mt-2 w-full min-h-11 py-2 text-[14px] font-semibold text-muted-foreground">Batal</button></div></div>}
    </div>
  );
}

// ── Add Consumer ──────────────────────────────────────

export function AddConsumerScreen({ goBack, navigate, offline }: NavProps) {
  const [form, setForm] = useState({
    name: "", phone: "", address: "", business: "", status: "", result: "", notes: "",
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saved, setSaved] = useState(false);

  const set = (key: string) => (v: string) => setForm(f => ({ ...f, [key]: v }));

  const validate = () => {
    const e: Record<string, string> = {};
    if (!form.name.trim()) e.name = "Nama konsumen wajib diisi";
    if (!form.phone.trim()) e.phone = "Nomor telepon wajib diisi";
    if (!form.address.trim()) e.address = "Alamat wajib diisi";
    if (!form.status) e.status = "Status konsumen wajib dipilih";
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSave = () => {
    if (!validate()) return;
    setSaved(true);
    setTimeout(() => goBack(), 1500);
  };

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Tambah Konsumen" onBack={goBack} />

      {saved ? (
        <div className="flex-1 flex flex-col items-center justify-center gap-4 px-8 text-center">
          <div className="flex items-center justify-center w-16 h-16 rounded-full bg-[#0f2a1a]">
            <div className="text-success">✓</div>
          </div>
          <p className="text-[17px] font-extrabold text-foreground">Konsumen Disimpan</p>
          <p className="text-[13px] text-muted-foreground">
            {offline ? "Data disimpan di perangkat dan akan tersinkron saat internet aktif." : "Data konsumen berhasil disimpan."}
          </p>
        </div>
      ) : (
        <>
          <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
            {offline && (
              <div className="rounded-xl bg-[#3a2b0d] border border-warning/30 p-3.5 text-[13px] text-[#fcd9a4]">
                Mode offline: data akan disimpan di perangkat dan dikirim saat internet aktif.
              </div>
            )}

            <div className="rounded-2xl border border-border bg-card p-4 space-y-4">
              <h3 className="text-[14px] font-extrabold text-foreground">Informasi Dasar</h3>
              <InputField label="Nama Konsumen" value={form.name} onChange={set("name")} placeholder="Masukkan nama lengkap" required error={errors.name} />
              <InputField label="Nomor Telepon" type="tel" value={form.phone} onChange={set("phone")} placeholder="08xx-xxxx-xxxx" required error={errors.phone} />
              <InputField label="Alamat" value={form.address} onChange={set("address")} placeholder="Alamat lengkap konsumen" required error={errors.address} />
              <InputField label="Nama Usaha / Pekerjaan" value={form.business} onChange={set("business")} placeholder="Opsional" />
            </div>

            <div className="rounded-2xl border border-border bg-card p-4 space-y-4">
              <h3 className="text-[14px] font-extrabold text-foreground">Status & Hasil Kunjungan</h3>
              <SelectField label="Status Konsumen" value={form.status} onChange={set("status")} options={STATUS_OPTIONS} required error={errors.status} />
              <TextareaField label="Hasil Awal Kunjungan" value={form.result} onChange={set("result")} placeholder="Ceritakan hasil pertemuan pertama..." />
              <TextareaField label="Catatan Tambahan" value={form.notes} onChange={set("notes")} placeholder="Catatan penting lainnya..." />
            </div>

            <div className="rounded-2xl border border-border bg-card p-4 space-y-1">
              <h3 className="text-[14px] font-extrabold text-foreground mb-3">Info Otomatis</h3>
              <InfoRow label="Tanggal & Waktu" value="21 Jul 2026, 09:30" />
              <InfoRow label="Lokasi Saat Ini" value="Gedebage, Kota Bandung" />
              <InfoRow label="Marketing" value="Budi Santoso" />
              <InfoRow label="Resort" value="Resort 3" />
            </div>
          </div>

          {/* Sticky form action bar — pinned above the persistent bottom nav */}
          <div className="shrink-0 border-t border-border bg-background px-4 py-3 space-y-2.5">
            <PrimaryButton onClick={handleSave}>Simpan Konsumen</PrimaryButton>
            <SecondaryButton onClick={goBack}>Batal</SecondaryButton>
          </div>
        </>
      )}
    </div>
  );
}

// ── Edit Consumer ─────────────────────────────────────

export function EditConsumerScreen({ goBack, params }: NavProps) {
  const consumer = mockConsumers.find(c => c.id === params?.consumerId) ?? mockConsumers[0];
  const [form, setForm] = useState({
    name: consumer.name,
    phone: consumer.phone,
    address: consumer.address,
    business: consumer.business,
    status: consumer.status,
    notes: consumer.notes,
  });
  const [saved, setSaved] = useState(false);

  const set = (key: string) => (v: string) => setForm(f => ({ ...f, [key]: v }));

  const handleSave = () => {
    setSaved(true);
    setTimeout(() => goBack(), 1200);
  };

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Edit Konsumen" onBack={goBack} />

      {saved ? (
        <div className="flex-1 flex flex-col items-center justify-center gap-4 px-8 text-center">
          <p className="text-[17px] font-extrabold text-success">Perubahan Disimpan</p>
          <p className="text-[13px] text-muted-foreground">Data konsumen berhasil diperbarui.</p>
        </div>
      ) : (
        <>
          <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
            <div className="rounded-2xl border border-border bg-card p-4 space-y-4">
              <h3 className="text-[14px] font-extrabold text-foreground">Informasi Dasar</h3>
              <InputField label="Nama Konsumen" value={form.name} onChange={set("name")} placeholder="Nama lengkap" required />
              <InputField label="Nomor Telepon" type="tel" value={form.phone} onChange={set("phone")} placeholder="08xx-xxxx-xxxx" required />
              <InputField label="Alamat" value={form.address} onChange={set("address")} placeholder="Alamat lengkap" required />
              <InputField label="Nama Usaha / Pekerjaan" value={form.business} onChange={set("business")} placeholder="Opsional" />
            </div>

            <div className="rounded-2xl border border-border bg-card p-4 space-y-4">
              <h3 className="text-[14px] font-extrabold text-foreground">Status & Catatan</h3>
              <SelectField label="Status Konsumen" value={form.status} onChange={set("status")} options={STATUS_OPTIONS} required />
              <TextareaField label="Catatan" value={form.notes} onChange={set("notes")} placeholder="Catatan tentang konsumen..." />
            </div>
          </div>

          {/* Sticky form action bar — pinned above the persistent bottom nav */}
          <div className="shrink-0 border-t border-border bg-background px-4 py-3 space-y-2.5">
            <PrimaryButton onClick={handleSave}>Simpan Perubahan</PrimaryButton>
            <SecondaryButton onClick={goBack}>Batal</SecondaryButton>
          </div>
        </>
      )}
    </div>
  );
}
