import { useState } from "react";
import reportVisitPhoto from "@/assets/report-visit.jpg";
import {
  Plus, ClipboardList, ChevronRight, Search, MapPin, Camera, Image,
  CheckCircle, CloudOff, FileText, X, UsersRound,
} from "lucide-react";
import {
  PageHeader, StatusBadge, FilterChip, PrimaryButton, SecondaryButton,
  InputField, SelectField, TextareaField, EmptyState, InfoRow,
  mockReports, mockConsumers,
} from "./shared";
import type { NavProps, Report } from "./shared";
import { featureFlags, isFeatureEnabled, consumerStatusOptions } from "@/config/featureFlags";

const RESULT_OPTIONS = isFeatureEnabled(featureFlags.mobile.advancedVisitResult) ? ["Berhasil Bertemu", "Tidak Bertemu", "Tertarik", "Perlu Follow Up", "Tidak Tertarik", "Transaksi Selesai"] : ["Berhasil Bertemu", "Tidak Bertemu", "Tertarik", "Tidak Tertarik"];
const FILTER_TABS = isFeatureEnabled(featureFlags.mobile.reportDraft) ? ["Semua", "Terkirim", "Menunggu", "Draft"] : ["Semua", "Terkirim", "Menunggu"];
const REPORT_PHOTO = reportVisitPhoto;

// ── Reports List ──────────────────────────────────────

export function ReportsScreen({ navigate, offline }: NavProps) {
  const [activeTab, setActiveTab] = useState("Semua");

  const filtered = mockReports.filter(r => {
    if (!isFeatureEnabled(featureFlags.mobile.reportDraft) && r.status === "Draft") return false;
    if (activeTab === "Semua") return true;
    if (activeTab === "Menunggu") return r.status === "Menunggu Sinkronisasi";
    return r.status === activeTab;
  });

  return (
    <div className="flex flex-col h-full">
      {/* Header */}
      <div className="bg-background px-4 pt-5 pb-3 shrink-0 border-b border-border">
        <div className="flex items-center justify-between mb-4">
          <div>
            <p className="text-[11px] font-bold uppercase tracking-[0.16em] text-gold-light">Aktivitas Lapangan</p>
            <h1 className="mt-0.5 text-[23px] font-extrabold tracking-tight text-foreground">Laporan Kunjungan</h1>
          </div>
          <button
            onClick={() => navigate("new-report")}
            className="flex items-center gap-1.5 rounded-xl bg-gold px-3 py-2.5 text-[13px] font-bold text-primary-foreground active:scale-95 transition"
          >
            <Plus size={16} /> Buat
          </button>
        </div>

        {/* Summary card */}
        <div className="rounded-2xl border border-border-gold bg-card p-3.5 flex items-center justify-between">
          <div>
            <p className="text-[13px] font-extrabold text-foreground">2 laporan terkirim</p>
            <p className="text-[12px] text-muted-foreground mt-0.5">
              1 laporan menunggu sinkronisasi
            </p>
          </div>
          <div className="flex items-center justify-center w-10 h-10 rounded-xl bg-card-elevated text-gold">
            <FileText size={18} />
          </div>
        </div>

        {/* Filter tabs */}
        <div className="hide-scrollbar flex gap-2 mt-3 overflow-x-auto overscroll-x-contain pb-0.5">
          {FILTER_TABS.map(t => (
            <FilterChip key={t} label={t} active={activeTab === t} onClick={() => setActiveTab(t)} />
          ))}
        </div>
      </div>

      {/* List */}
      <div className="hide-scrollbar flex-1 overflow-y-auto overscroll-contain px-4 pt-3 pb-4 space-y-2.5">
        {filtered.length === 0 ? (
          <EmptyState icon={ClipboardList} title="Tidak ada laporan" desc="Belum ada laporan pada kategori ini" />
        ) : (
          filtered.map(r => (
            <ReportCard key={r.id} report={r} onClick={() => navigate("report-detail", { reportId: r.id })} />
          ))
        )}
      </div>
    </div>
  );
}

function ReportCard({ report, onClick }: { report: Report; onClick: () => void }) {
  return (
    <button
      onClick={onClick}
      className="flex w-full items-start gap-3 rounded-2xl border border-border bg-card p-3.5 text-left shadow-[0_2px_10px_rgba(0,0,0,0.2)] active:scale-[0.99] transition"
    >
      <div className="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-card-elevated">
        <img src={REPORT_PHOTO} alt="Dokumentasi kunjungan usaha" className="h-full w-full object-cover" />
      </div>
      <div className="flex-1 min-w-0">
        <div className="flex items-start justify-between gap-2">
          <p className="text-[14px] font-extrabold text-foreground truncate">{report.consumer}</p>
          <StatusBadge status={report.status} sm />
        </div>
        <p className="mt-0.5 text-[13px] text-muted-foreground">{report.result}</p>
        {report.status === "Draft" && <p className="mt-1 text-[12px] text-muted-foreground">Belum dikirim, masih dapat dilanjutkan.</p>}
        {report.status === "Menunggu Sinkronisasi" && <p className="mt-1 text-[12px] text-warning">Laporan selesai, menunggu koneksi untuk dikirim.</p>}
        <div className="mt-1.5 flex items-center gap-2 text-[12px] text-muted-foreground">
          <MapPin size={11} className="shrink-0" />
          <span className="truncate">{report.location}</span>
          <span>·</span>
          <span className="shrink-0 font-['DM_Mono']">{report.time}</span>
        </div>
      </div>
      <ChevronRight size={16} className="text-disabled shrink-0 mt-1" />
    </button>
  );
}

// ── Report Detail ─────────────────────────────────────

export function ReportDetailScreen({ goBack, params }: NavProps) {
  const report = mockReports.find(r => r.id === params?.reportId) ?? mockReports[0];

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Detail Laporan" subtitle={`${report.date} · ${report.time}`} onBack={goBack} />

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        {/* Status */}
        <div className="flex items-center justify-between rounded-2xl border border-border bg-card px-4 py-4">
          <div>
            <p className="text-[12px] text-muted-foreground">Status Pengiriman</p>
            <div className="mt-1">
              <StatusBadge status={report.status} />
            </div>
          </div>
          <div className="flex items-center justify-center w-10 h-10 rounded-xl bg-card-elevated text-gold">
            <ClipboardList size={18} />
          </div>
        </div>

        {/* Consumer Info */}
        <div className="rounded-2xl border border-border bg-card p-4">
          <h3 className="text-[14px] font-extrabold text-foreground mb-3">Informasi Konsumen</h3>
          <InfoRow label="Nama Konsumen" value={report.consumer} />
          <InfoRow label="Lokasi" value={report.location} />
        </div>

        {/* Visit Result */}
        <div className="rounded-2xl border border-border bg-card p-4">
          <h3 className="text-[14px] font-extrabold text-foreground mb-3">Hasil Kunjungan</h3>
          <InfoRow label="Hasil" value={report.result} />
          <div className="py-2.5">
            <p className="text-[13px] text-muted-foreground mb-1">Catatan</p>
            <p className="text-[13px] text-foreground leading-relaxed">{report.notes}</p>
          </div>
        </div>

        {/* Photo placeholder */}
        <div className="rounded-2xl border border-border bg-card p-4">
          <h3 className="text-[14px] font-extrabold text-foreground mb-3">Foto Kunjungan</h3>
          <div className="h-52 overflow-hidden rounded-xl bg-card-elevated"><img src={REPORT_PHOTO} alt="Dokumentasi kunjungan konsumen" className="h-full w-full object-cover" /></div>
          <p className="mt-3 text-[13px] text-muted-foreground">Dokumentasi kunjungan di lokasi konsumen.</p>
        </div>

        {/* Meta */}
        <div className="rounded-2xl border border-border bg-card p-4 pb-2">
          <h3 className="text-[14px] font-extrabold text-foreground mb-3">Info Pengiriman</h3>
          <InfoRow label="Tanggal" value={report.date} />
          <InfoRow label="Waktu" value={report.time} />
          <InfoRow label="Marketing" value="Budi Santoso" />
          <InfoRow label="Resort" value="Resort 3" />
        </div>
      </div>
    </div>
  );
}

// ── New Report (Multi-step) ───────────────────────────

type ReportStep = "select-consumer" | "visit-result" | "add-photo" | "confirm" | "success" | "offline-saved" | "draft-saved";

export function NewReportScreen({ goBack, navigate, offline, params }: NavProps) {
  const [step, setStep] = useState<ReportStep>(params?.consumerId ? "visit-result" : "select-consumer");
  const [selectedConsumerId, setSelectedConsumerId] = useState(params?.consumerId ?? "");
  const [search, setSearch] = useState("");
  const [form, setForm] = useState({ purpose: "", result: "", status: "", notes: "", followup: "" });
  const [hasPhoto, setHasPhoto] = useState(false);
  const [photoNote, setPhotoNote] = useState("");

  const selectedConsumer = mockConsumers.find(c => c.id === selectedConsumerId);
  const set = (key: string) => (v: string) => setForm(f => ({ ...f, [key]: v }));

  const filteredConsumers = mockConsumers.filter(c =>
    c.name.toLowerCase().includes(search.toLowerCase())
  );

  const STEPS: ReportStep[] = ["select-consumer", "visit-result", "add-photo", "confirm"];
  const stepIndex = STEPS.indexOf(step);

  const handleSubmit = () => setStep(offline ? "offline-saved" : "success");
  const handleDraft = () => setStep("draft-saved");

  if (step === "success" || step === "offline-saved" || step === "draft-saved") {
    return (
      <div className="flex flex-col h-full bg-background">
        <PageHeader title="Laporan" onBack={goBack} />
        <div className="flex-1 flex flex-col items-center justify-center px-8 text-center gap-5">
          <div className={`flex items-center justify-center w-20 h-20 rounded-full ${step === "success" ? "bg-[#0f2a1a]" : step === "draft-saved" ? "bg-card-elevated" : "bg-[#3a2b0d]"}`}>
            {step === "success" ? <CheckCircle size={36} className="text-success" /> : step === "draft-saved" ? <FileText size={36} className="text-muted-foreground" /> : <CloudOff size={36} className="text-warning" />}
          </div>
          <div>
            <h2 className="text-[22px] font-extrabold text-foreground">
              {step === "success" ? "Laporan Berhasil Dikirim!" : step === "draft-saved" ? "Laporan Berhasil Disimpan sebagai Draft" : "Laporan Menunggu Sinkronisasi"}
            </h2>
            <p className="mt-2 text-[14px] text-muted-foreground leading-relaxed">
              {step === "success"
                ? "Laporan kunjungan telah berhasil dikirim ke server."
                : step === "draft-saved" ? "Laporan tersimpan sebagai draft dan dapat dilanjutkan melalui menu Laporan." : "Laporan tersimpan di perangkat dan akan dikirim saat internet kembali aktif."}
            </p>
          </div>
          <PrimaryButton onClick={() => navigate("reports")}>Lihat Daftar Laporan</PrimaryButton>
          <button onClick={() => navigate("dashboard")} className="text-[13px] font-semibold text-muted-foreground">Kembali ke Dashboard</button>
        </div>
      </div>
    );
  }

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Buat Laporan" onBack={goBack} />

      {/* Step indicator */}
      {step !== "select-consumer" && (
        <div className="flex items-center gap-1.5 px-4 py-3 bg-card border-b border-border shrink-0">
          {STEPS.map((s, i) => (
            <div key={s} className={`h-1 flex-1 rounded-full transition-all ${i <= stepIndex ? "bg-gold" : "bg-border"}`} />
          ))}
          <span className="text-[12px] text-muted-foreground ml-1 font-bold">{stepIndex + 1}/4</span>
        </div>
      )}

      {/* Step: Select Consumer */}
      {step === "select-consumer" && (
        <div className="flex flex-col h-full">
          <div className="px-4 pt-4 pb-2 border-b border-border bg-card shrink-0">
            <p className="text-[13px] text-muted-foreground mb-3">Pilih konsumen yang dikunjungi:</p>
            <div className="flex items-center gap-2 rounded-xl border border-border bg-background px-3 h-11">
              <Search size={16} className="text-muted-foreground shrink-0" />
              <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Cari nama konsumen..."
                className="flex-1 bg-transparent text-[14px] text-foreground outline-none placeholder:text-disabled" />
            </div>
          </div>

          <div className="flex-1 overflow-y-auto px-4 pt-3 pb-4 space-y-2.5">
            <button
              onClick={() => navigate("add-consumer")}
              className="flex w-full items-center gap-3 rounded-2xl border border-dashed border-border-gold bg-card p-3.5 active:scale-[0.99] transition"
            >
              <div className="flex items-center justify-center w-10 h-10 rounded-full bg-gold/15 text-gold">
                <Plus size={18} />
              </div>
              <p className="text-[14px] font-bold text-gold-light">Tambah Konsumen Baru</p>
            </button>

            {filteredConsumers.map(c => (
              <button
                key={c.id}
                onClick={() => { setSelectedConsumerId(c.id); setStep("visit-result"); }}
                className={`flex w-full items-center gap-3 rounded-2xl border p-3.5 text-left active:scale-[0.99] transition ${selectedConsumerId === c.id ? "border-gold bg-card" : "border-border bg-card"}`}
              >
                <div className="flex items-center justify-center w-10 h-10 rounded-full bg-card-elevated text-[12px] font-extrabold text-gold-light shrink-0">
                  {c.initials}
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-[14px] font-extrabold text-foreground truncate">{c.name}</p>
                  <p className="text-[12px] text-muted-foreground mt-0.5 truncate"><MapPin size={10} className="inline mr-0.5" />{c.address}</p>
                </div>
                <StatusBadge status={c.status} sm />
              </button>
            ))}
          </div>
        </div>
      )}

      {/* Step: Visit Result */}
      {step === "visit-result" && (
        <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
          {selectedConsumer && (
            <div className="flex items-center gap-3 rounded-2xl border border-border bg-card p-3.5">
              <div className="flex items-center justify-center w-10 h-10 rounded-full bg-card-elevated text-[12px] font-extrabold text-gold-light shrink-0">
                {selectedConsumer.initials}
              </div>
              <div className="flex-1 min-w-0">
                <p className="text-[14px] font-extrabold text-foreground">{selectedConsumer.name}</p>
                <p className="text-[12px] text-muted-foreground">{selectedConsumer.address}</p>
              </div>
              <button onClick={() => setStep("select-consumer")} className="text-muted-foreground"><X size={16} /></button>
            </div>
          )}

          <div className="rounded-2xl border border-border bg-card p-4 space-y-4">
            <InputField label="Tujuan Kunjungan" value={form.purpose} onChange={set("purpose")} placeholder="Misal: Presentasi produk tabungan" />
            <SelectField label="Hasil Kunjungan" value={form.result} onChange={set("result")} options={RESULT_OPTIONS} required />
            <SelectField label="Status Konsumen" value={form.status} onChange={set("status")} options={consumerStatusOptions()} required />
            <TextareaField label="Catatan" value={form.notes} onChange={set("notes")} placeholder="Catatan hasil kunjungan..." rows={3} />
            {isFeatureEnabled(featureFlags.mobile.followUpSchedule) && <InputField label="Tanggal Tindak Lanjut" type="date" value={form.followup} onChange={set("followup")} />}
          </div>

        </div>
      )}
      {step === "visit-result" && (
        <div className="shrink-0 border-t border-border bg-background px-4 py-3 space-y-2.5">
          <PrimaryButton onClick={() => setStep("add-photo")} disabled={!form.result}>Lanjutkan → Foto</PrimaryButton>
          <SecondaryButton onClick={goBack}>Batal</SecondaryButton>
        </div>
      )}

      {/* Step: Add Photo */}
      {step === "add-photo" && (
        <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
          <div className="rounded-2xl border border-border bg-card p-4 space-y-3">
            <h3 className="text-[14px] font-extrabold text-foreground">Foto Kunjungan</h3>

            {hasPhoto ? (
              <div className="relative">
                <div className="h-44 overflow-hidden rounded-xl bg-card-elevated"><img src={REPORT_PHOTO} alt="Preview foto kunjungan" className="h-full w-full object-cover" /></div>
                <button onClick={() => setHasPhoto(false)} className="absolute top-2 right-2 flex items-center justify-center w-8 h-8 rounded-full bg-destructive text-white">
                  <X size={14} />
                </button>
                <div className="mt-2 flex gap-2">
                  <button onClick={() => setHasPhoto(false)} className="flex-1 h-10 min-h-11 rounded-xl border border-border text-[13px] font-bold text-foreground">Ganti Foto</button>
                  {isFeatureEnabled(featureFlags.mobile.multipleReportPhotos) && <button onClick={() => setHasPhoto(true)} className="flex-1 h-10 min-h-11 rounded-xl border border-border text-[13px] font-bold text-foreground">Tambah Foto Lain</button>}
                </div>
              </div>
            ) : (
              <div className="space-y-2">
                <button onClick={() => setHasPhoto(true)} className="flex w-full items-center gap-3 rounded-xl border border-border bg-background p-4 active:bg-card-elevated transition">
                  <Camera size={20} className="text-foreground" />
                  <span className="text-[14px] font-bold text-foreground">Ambil Foto</span>
                </button>
                <button onClick={() => setHasPhoto(true)} className="flex w-full items-center gap-3 rounded-xl border border-border bg-background p-4 active:bg-card-elevated transition">
                  <Image size={20} className="text-foreground" />
                  <span className="text-[14px] font-bold text-foreground">Pilih dari Galeri</span>
                </button>
              </div>
            )}
          </div>
          <TextareaField label="Keterangan Foto" value={photoNote} onChange={setPhotoNote} placeholder="Contoh: Tampak depan toko saat kunjungan" rows={2} />
          <p className="text-[12px] text-muted-foreground">Foto akan dikompres sebelum disimpan.</p>

        </div>
      )}
      {step === "add-photo" && (
        <div className="shrink-0 border-t border-border bg-background px-4 py-3 space-y-2.5">
          <PrimaryButton onClick={() => setStep("confirm")}>
            {hasPhoto ? "Lanjutkan → Konfirmasi" : "Lewati, Lanjutkan"}
          </PrimaryButton>
          <SecondaryButton onClick={() => setStep("visit-result")}>Kembali</SecondaryButton>
        </div>
      )}

      {/* Step: Confirm */}
      {step === "confirm" && (
        <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
          <div className="rounded-2xl border border-border bg-card p-4">
            <h3 className="text-[14px] font-extrabold text-foreground mb-3">Ringkasan Laporan</h3>
            <InfoRow label="Konsumen" value={selectedConsumer?.name ?? "-"} />
            <InfoRow label="Lokasi" value={selectedConsumer?.address ?? "-"} />
            <InfoRow label="Hasil" value={form.result || "-"} />
            <InfoRow label="Status Konsumen" value={form.status || "-"} />
            {form.notes && (
              <div className="py-2.5 border-b border-border">
                <p className="text-[13px] text-muted-foreground mb-1">Catatan</p>
                <p className="text-[13px] text-foreground leading-snug">{form.notes}</p>
              </div>
            )}
            {isFeatureEnabled(featureFlags.mobile.followUpSchedule) && <InfoRow label="Tanggal Follow-up" value={form.followup || "-"} />}
            <InfoRow label="Foto" value={hasPhoto ? "1 foto" : "Tidak ada"} />
            <InfoRow label="Keterangan Foto" value={photoNote || "-"} />
            <InfoRow label="Status Koneksi" value={offline ? "Offline" : "Online"} />
            <InfoRow label="Tanggal & Waktu" value="21 Jul 2026, 09:30" />
            <InfoRow label="Lokasi GPS" value="Gedebage, Bandung" />
            <InfoRow label="Marketing" value="Budi Santoso · Resort 3" />
          </div>

          {offline && (
            <div className="flex items-start gap-2.5 rounded-2xl bg-[#3a2b0d] border border-warning/30 p-3.5">
              <CloudOff size={15} className="text-warning mt-0.5 shrink-0" />
              <p className="text-[13px] text-[#fcd9a4]">Anda sedang offline. Laporan akan disimpan di perangkat dan dikirim saat internet aktif.</p>
            </div>
          )}

        </div>
      )}
      {step === "confirm" && (
        <div className="shrink-0 border-t border-border bg-background px-4 py-3 space-y-2.5">
          <PrimaryButton onClick={handleSubmit}>
            {offline ? "Simpan dan Kirim Saat Online" : "Kirim Laporan"}
          </PrimaryButton>
          {isFeatureEnabled(featureFlags.mobile.reportDraft) && <SecondaryButton onClick={handleDraft}>Simpan sebagai Draft</SecondaryButton>}
          <SecondaryButton onClick={() => setStep("add-photo")}>Kembali</SecondaryButton>
        </div>
      )}
    </div>
  );
}
