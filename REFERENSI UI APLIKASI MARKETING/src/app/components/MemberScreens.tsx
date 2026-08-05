import { useRef, useState } from "react";
import { Search, SlidersHorizontal, Plus, Phone, ChevronRight, UsersRound, Save, CheckCircle } from "lucide-react";
import {
  PageHeader, StatusBadge, PrimaryButton, SecondaryButton, ModalConfirm,
  InputField, SelectField, TextareaField, CurrencyField, PhotoPickerField, LocationCard, SectionCard,
  FilterChip, EmptyState, InfoRow, formatRupiah,
  mockMembers,
} from "./shared";
import type { NavProps, Member } from "./shared";

const ACC_FILTERS = ["Semua", "Menunggu", "Disetujui", "Ditolak"];
const RESORT_OPTIONS = ["Resort 1", "Resort 2", "Resort 3", "Resort 4", "Resort 5"];
const CURRENT_MARKETING_RESORT = "Resort 3";

function todayDisplay() {
  return "21 Juli 2026";
}

// ── Members List ──────────────────────────────────────

export function MembersScreen({ navigate }: NavProps) {
  const [search, setSearch] = useState("");
  const [filter, setFilter] = useState("Semua");

  const filtered = mockMembers.filter(m => {
    const matchSearch = m.nama.toLowerCase().includes(search.toLowerCase()) || m.nomorAnggota.toLowerCase().includes(search.toLowerCase());
    const matchFilter = filter === "Semua" ? true : m.accStatus === filter;
    return matchSearch && matchFilter;
  });

  return (
    <div className="flex flex-col h-full">
      <div className="bg-background px-4 pt-5 pb-3 shrink-0 border-b border-border">
        <div className="flex items-center justify-between mb-4">
          <div>
            <p className="text-[11px] font-bold uppercase tracking-[0.16em] text-gold-light">Data Lapangan</p>
            <h1 className="mt-0.5 text-[23px] font-extrabold tracking-tight text-foreground">Anggota</h1>
          </div>
          <button
            onClick={() => navigate("add-member")}
            className="flex items-center justify-center w-11 h-11 rounded-xl bg-gold text-primary-foreground active:scale-95 transition"
            aria-label="Tambah anggota"
          >
            <Plus size={21} />
          </button>
        </div>

        <div className="flex gap-2">
          <div className="flex flex-1 items-center gap-2 rounded-xl border border-border bg-input-background px-3 h-11">
            <Search size={16} className="text-muted-foreground shrink-0" />
            <input
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="Cari nama atau nomor anggota"
              className="flex-1 bg-transparent text-[14px] text-foreground outline-none placeholder:text-disabled"
            />
          </div>
          <button className="flex items-center justify-center w-11 h-11 rounded-xl border border-border bg-input-background" aria-label="Filter">
            <SlidersHorizontal size={17} className="text-foreground" />
          </button>
        </div>

        <div className="flex gap-2 mt-3 overflow-x-auto pb-0.5 hide-scrollbar">
          {ACC_FILTERS.map(f => (
            <FilterChip key={f} label={f} active={filter === f} onClick={() => setFilter(f)} />
          ))}
        </div>
      </div>

      <div className="flex-1 overflow-y-auto px-4 pt-3 pb-4 space-y-2.5">
        {filtered.length === 0 ? (
          <EmptyState icon={UsersRound} title="Tidak ada anggota" desc="Coba ubah filter atau kata kunci pencarian Anda" />
        ) : (
          filtered.map(m => (
            <MemberCard key={m.id} member={m} onClick={() => navigate("member-detail", { memberId: m.id })} />
          ))
        )}
      </div>
    </div>
  );
}

function MemberCard({ member, onClick }: { member: Member; onClick: () => void }) {
  return (
    <button
      onClick={onClick}
      className="flex w-full items-center gap-3 rounded-2xl border border-border bg-card p-3.5 text-left shadow-[0_2px_10px_rgba(0,0,0,0.2)] active:scale-[0.99] transition"
    >
      <div className="flex items-center justify-center w-11 h-11 rounded-full bg-card-elevated text-[13px] font-extrabold text-gold-light shrink-0">
        {member.initials}
      </div>
      <div className="min-w-0 flex-1">
        <p className="text-[14px] font-extrabold text-foreground truncate">{member.nama}</p>
        <p className="mt-0.5 text-[12px] text-muted-foreground truncate">No. Anggota {member.nomorAnggota} · {member.resort}</p>
        <div className="mt-1.5 flex items-center gap-2">
          <StatusBadge status={member.accStatus} sm />
          <span className="text-[11px] font-bold text-gold-light">{formatRupiah(member.pinjaman)}</span>
        </div>
      </div>
      <ChevronRight size={16} className="text-disabled shrink-0" />
    </button>
  );
}

// ── Member Detail ─────────────────────────────────────

export function MemberDetailScreen({ goBack, params }: NavProps) {
  const [showContact, setShowContact] = useState(false);
  const [contactNotice, setContactNotice] = useState("");
  const member = mockMembers.find(m => m.id === params?.memberId) ?? mockMembers[0];

  return (
    <div className="relative flex flex-col h-full bg-background">
      <PageHeader title={member.nama} subtitle={`Anggota · ${member.resort}`} onBack={goBack} />

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        <div className="rounded-2xl border border-border bg-card overflow-hidden">
          <div className="bg-card-elevated px-4 py-4 flex items-center gap-3">
            <div className="flex items-center justify-center w-14 h-14 rounded-full bg-gold text-[18px] font-extrabold text-primary-foreground shrink-0">
              {member.initials}
            </div>
            <div>
              <h2 className="text-[17px] font-extrabold text-foreground">{member.nama}</h2>
              <p className="text-[12px] text-muted-foreground mt-0.5">No. Anggota {member.nomorAnggota}</p>
              <div className="mt-1.5">
                <StatusBadge status={member.accStatus} />
              </div>
            </div>
          </div>
          <div className="px-4 pb-3">
            <InfoRow label="Nomor Pinjaman" value={member.nomorPinjaman || "-"} />
            <InfoRow label="Nomor HP" value={member.noHp} />
            <InfoRow label="Alamat" value={member.alamat} />
            <InfoRow label="Usaha" value={member.usaha} />
            <InfoRow label="Tanggal Input" value={member.tanggal} />
          </div>
        </div>

        <SectionCard title="Informasi Pinjaman">
          <InfoRow label="Nominal Pinjaman" value={formatRupiah(member.pinjaman)} />
          <InfoRow label="Nominal Angsuran" value={formatRupiah(member.angsuran)} />
          <InfoRow label="Nominal Asuransi" value={formatRupiah(member.asuransi)} />
          <InfoRow label="Jaminan" value={member.jaminan || "-"} />
          <InfoRow label="Status ACC" value={member.accStatus} />
        </SectionCard>

        <SectionCard title="Foto Anggota">
          {member.hasPhoto ? (
            <div className="flex aspect-[3/4] w-full max-w-[180px] items-center justify-center rounded-xl bg-card-elevated text-muted-foreground">
              <span className="text-[11px]">Pratinjau Foto</span>
            </div>
          ) : (
            <p className="text-[13px] text-muted-foreground">Foto belum tersedia.</p>
          )}
        </SectionCard>

        <LocationCard
          status="ok"
          address={member.locationAddress}
          coords={member.locationCoords}
          updatedAt={member.locationUpdatedAt}
          onRefresh={() => {}}
        />

        <div className="space-y-2.5 pb-[calc(88px+env(safe-area-inset-bottom))]">
          <PrimaryButton onClick={() => setShowContact(true)}>
            <span className="flex items-center justify-center gap-2"><Phone size={16} /> Hubungi Anggota</span>
          </PrimaryButton>
        </div>
      </div>
      {contactNotice && <div className="absolute bottom-5 left-4 right-4 rounded-xl bg-card-elevated border border-border-gold px-4 py-3 text-center text-[13px] font-semibold text-foreground">{contactNotice}</div>}
      {showContact && (
        <div className="absolute inset-0 z-20 flex items-end bg-black/60 p-4">
          <div className="w-full rounded-t-[24px] border border-border bg-card-elevated p-4">
            <h3 className="text-[17px] font-extrabold text-foreground">Hubungi Anggota</h3>
            <p className="mt-1 text-[13px] text-muted-foreground">{member.noHp}</p>
            <button onClick={() => { setShowContact(false); setContactNotice("Membuka panggilan untuk " + member.noHp); }} className="mt-4 w-full min-h-11 rounded-xl bg-gold py-3 text-[14px] font-bold text-primary-foreground">Telepon Anggota</button>
            <button onClick={() => setShowContact(false)} className="mt-2 w-full min-h-11 py-2 text-[14px] font-semibold text-muted-foreground">Batal</button>
          </div>
        </div>
      )}
    </div>
  );
}

// ── Add Member ─────────────────────────────────────────

interface MemberForm {
  resort: string; tanggal: string; nama: string; nomorAnggota: string; nomorPinjaman: string;
  alamat: string; noHp: string; usaha: string; pinjaman: string; angsuran: string; asuransi: string; jaminan: string;
}

const EMPTY_FORM: MemberForm = {
  resort: CURRENT_MARKETING_RESORT, tanggal: todayDisplay(), nama: "", nomorAnggota: "", nomorPinjaman: "",
  alamat: "", noHp: "", usaha: "", pinjaman: "", angsuran: "", asuransi: "", jaminan: "",
};

const FIELD_ORDER: (keyof MemberForm | "foto" | "lokasi")[] = [
  "nama", "nomorAnggota", "alamat", "noHp", "usaha", "pinjaman", "angsuran", "foto", "lokasi",
];

export function AddMemberScreen({ goBack, navigate, offline }: NavProps) {
  const [form, setForm] = useState<MemberForm>(EMPTY_FORM);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [hasPhoto, setHasPhoto] = useState(false);
  const [photoLoading, setPhotoLoading] = useState(false);
  const [locationStatus, setLocationStatus] = useState<"ok" | "loading" | "error">("ok");
  const [locationUpdatedAt, setLocationUpdatedAt] = useState("21 Jul 2026, 09:30");
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);
  const [showLeaveConfirm, setShowLeaveConfirm] = useState(false);

  const fieldRefs = useRef<Record<string, HTMLDivElement | null>>({});
  const setFieldRef = (key: string) => (el: HTMLDivElement | null) => { fieldRefs.current[key] = el; };

  const set = (key: keyof MemberForm) => (v: string) => setForm(f => ({ ...f, [key]: v }));

  const isDirty = () =>
    form.nama.trim() !== "" || form.nomorAnggota.trim() !== "" || form.alamat.trim() !== "" ||
    form.noHp.trim() !== "" || form.usaha.trim() !== "" || form.pinjaman !== "" || form.angsuran !== "" ||
    form.asuransi !== "" || form.jaminan.trim() !== "" || form.nomorPinjaman.trim() !== "" || hasPhoto;

  const handleBack = () => {
    if (!saved && isDirty()) { setShowLeaveConfirm(true); return; }
    goBack();
  };

  const validate = () => {
    const e: Record<string, string> = {};
    if (!form.nama.trim() || form.nama.trim().length < 3) e.nama = "Nama anggota wajib diisi minimal 3 karakter.";
    if (!form.nomorAnggota.trim()) e.nomorAnggota = "Nomor anggota wajib diisi.";
    if (!form.alamat.trim()) e.alamat = "Alamat wajib diisi.";
    const phoneDigits = form.noHp.replace(/[^0-9]/g, "");
    if (!form.noHp.trim()) e.noHp = "Nomor HP wajib diisi.";
    else if (phoneDigits.length < 10 || phoneDigits.length > 15) e.noHp = "Nomor HP minimal 10 digit dan maksimal 15 digit.";
    if (!form.usaha.trim()) e.usaha = "Jenis usaha wajib diisi.";
    if (!form.pinjaman || Number(form.pinjaman) <= 0) e.pinjaman = "Nominal pinjaman harus lebih dari Rp0.";
    if (!form.angsuran || Number(form.angsuran) <= 0) e.angsuran = "Nominal angsuran harus lebih dari Rp0.";
    if (!hasPhoto) e.foto = "Silakan ambil foto anggota.";
    if (locationStatus !== "ok") e.lokasi = "Lokasi belum berhasil didapatkan.";
    setErrors(e);
    return e;
  };

  const handleSave = () => {
    if (saving) return;
    const e = validate();
    if (Object.keys(e).length > 0) {
      const firstKey = FIELD_ORDER.find(k => e[k as string]);
      if (firstKey) fieldRefs.current[firstKey]?.scrollIntoView({ behavior: "smooth", block: "center" });
      return;
    }
    setSaving(true);
    setTimeout(() => {
      setSaving(false);
      setSaved(true);
      setTimeout(() => navigate("members"), 1400);
    }, 900);
  };

  const handleRefreshLocation = () => {
    setLocationStatus("loading");
    setTimeout(() => {
      setLocationStatus("ok");
      setLocationUpdatedAt("21 Jul 2026, 09:32");
    }, 800);
  };

  const handleTakePhoto = () => {
    setPhotoLoading(true);
    setTimeout(() => { setPhotoLoading(false); setHasPhoto(true); }, 700);
  };

  if (saved) {
    return (
      <div className="flex flex-col h-full bg-background">
        <PageHeader title="Tambah Anggota" onBack={() => navigate("members")} />
        <div className="flex-1 flex flex-col items-center justify-center gap-4 px-8 text-center">
          <div className="flex items-center justify-center w-16 h-16 rounded-full bg-[#0f2a1a]">
            <CheckCircle size={30} className="text-success" />
          </div>
          <p className="text-[17px] font-extrabold text-foreground">Data anggota berhasil disimpan</p>
          <p className="text-[13px] text-muted-foreground">
            {offline ? "Data disimpan di perangkat dan akan tersinkron saat internet aktif." : "Data anggota telah tersimpan."}
          </p>
        </div>
      </div>
    );
  }

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Tambah Anggota" subtitle="Lengkapi data anggota" onBack={handleBack} />

      <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4 pb-28">
        {offline && (
          <div className="rounded-xl bg-[#3a2b0d] border border-warning/30 p-3.5 text-[13px] text-[#fcd9a4]">
            Mode offline: data akan disimpan di perangkat dan dikirim saat internet aktif.
          </div>
        )}

        <SectionCard title="A. Informasi Utama">
          <SelectField label="Resort" value={form.resort} onChange={set("resort")} options={RESORT_OPTIONS} required disabled />
          <InputField label="Tanggal" value={form.tanggal} onChange={set("tanggal")} required />
          <InputField label="Nama Anggota/Nasabah" value={form.nama} onChange={set("nama")} placeholder="Masukkan nama lengkap" required error={errors.nama} fieldRef={setFieldRef("nama")} />
          <InputField label="Nomor Anggota" value={form.nomorAnggota} onChange={set("nomorAnggota")} placeholder="Masukkan nomor anggota" required error={errors.nomorAnggota} fieldRef={setFieldRef("nomorAnggota")} />
          <InputField label="Nomor Pinjaman" value={form.nomorPinjaman} onChange={set("nomorPinjaman")} placeholder="Masukkan nomor pinjaman" />
        </SectionCard>

        <SectionCard title="B. Kontak dan Usaha">
          <TextareaField label="Alamat Lengkap" value={form.alamat} onChange={set("alamat")} placeholder="Masukkan alamat lengkap anggota" rows={3} required error={errors.alamat} fieldRef={setFieldRef("alamat")} />
          <InputField label="Nomor HP" type="tel" inputMode="tel" value={form.noHp} onChange={set("noHp")} placeholder="Contoh: 081234567890" required error={errors.noHp} fieldRef={setFieldRef("noHp")} />
          <InputField label="Jenis Usaha" value={form.usaha} onChange={set("usaha")} placeholder="Contoh: Warung sembako" required error={errors.usaha} fieldRef={setFieldRef("usaha")} />
        </SectionCard>

        <SectionCard title="C. Informasi Pinjaman">
          <CurrencyField label="Nominal Pinjaman" value={form.pinjaman} onChange={set("pinjaman")} required error={errors.pinjaman} fieldRef={setFieldRef("pinjaman")} />
          <CurrencyField label="Nominal Angsuran" value={form.angsuran} onChange={set("angsuran")} required error={errors.angsuran} fieldRef={setFieldRef("angsuran")} />
          <CurrencyField label="Nominal Asuransi" value={form.asuransi} onChange={set("asuransi")} />
          <InputField label="Jaminan" value={form.jaminan} onChange={set("jaminan")} placeholder="Contoh: BPKB Motor" />
          <div>
            <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">Status ACC</label>
            <div className="flex items-center justify-between rounded-xl border border-border bg-card-elevated px-4 py-3">
              <span className="text-[13px] text-muted-foreground">Persetujuan dilakukan oleh admin</span>
              <StatusBadge status="Menunggu" />
            </div>
          </div>
        </SectionCard>

        <SectionCard title="D. Foto Anggota">
          <PhotoPickerField
            label="Foto Anggota/Nasabah"
            hasPhoto={hasPhoto}
            loading={photoLoading}
            error={errors.foto}
            onTake={handleTakePhoto}
            onPick={handleTakePhoto}
            onRemove={() => setHasPhoto(false)}
            fieldRef={setFieldRef("foto")}
          />
        </SectionCard>

        <div ref={setFieldRef("lokasi")}>
          <LocationCard
            status={locationStatus}
            address="Gedebage, Kota Bandung"
            coords="-6.9500, 107.6800"
            updatedAt={locationUpdatedAt}
            onRefresh={handleRefreshLocation}
            refreshing={locationStatus === "loading"}
          />
          {errors.lokasi && <p className="mt-2 flex items-center gap-1 text-[12px] text-destructive">{errors.lokasi}</p>}
        </div>

        <SectionCard title="Info Otomatis">
          <InfoRow label="Marketing" value="Budi Santoso" />
          <InfoRow label="Kode Marketing" value="KSP-0318" />
        </SectionCard>
      </div>

      {/* Sticky bottom actions */}
      <div className="shrink-0 border-t border-border bg-background px-4 py-3 space-y-2.5">
        <PrimaryButton onClick={handleSave} loading={saving}>
          <span className="flex items-center justify-center gap-2"><Save size={16} /> Simpan Anggota</span>
        </PrimaryButton>
        <SecondaryButton onClick={handleBack}>Batal</SecondaryButton>
      </div>

      {showLeaveConfirm && (
        <ModalConfirm
          title="Keluar tanpa menyimpan?"
          desc="Data yang belum disimpan akan hilang. Tetap keluar?"
          confirmLabel="Keluar"
          cancelLabel="Tetap di Halaman"
          onConfirm={goBack}
          onCancel={() => setShowLeaveConfirm(false)}
          danger
        />
      )}
    </div>
  );
}
