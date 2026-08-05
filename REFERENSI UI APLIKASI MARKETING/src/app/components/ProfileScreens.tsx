import { useState } from "react";
import type { ReactNode } from "react";
import {
  User, Shield, BellRing, BookOpen, Info, LogOut,
  ChevronRight, Eye, EyeOff, Check,
} from "lucide-react";
import { PageHeader, PrimaryButton, SecondaryButton, ModalConfirm, KspLogo, StatusBadge, InfoRow } from "./shared";
import type { NavProps } from "./shared";
import { featureFlags, isFeatureEnabled } from "@/config/featureFlags";

// ── Profile Screen ────────────────────────────────────

export function ProfileScreen({ navigate, resetTo, offline }: NavProps) {
  const [showLogout, setShowLogout] = useState(false);

  const menu = [
    isFeatureEnabled(featureFlags.mobile.accountInformation) && { icon: User, label: "Informasi Akun", action: () => navigate("account-info") },
    isFeatureEnabled(featureFlags.mobile.changePassword) && { icon: Shield, label: "Ubah Password", action: () => navigate("change-password") },
    isFeatureEnabled(featureFlags.mobile.separatePermissionOnboarding) && { icon: BellRing, label: "Status Izin Aplikasi", action: () => navigate("permission-status") },
    isFeatureEnabled(featureFlags.mobile.userGuide) && { icon: BookOpen, label: "Panduan Penggunaan", action: () => navigate("usage-guide") },
    isFeatureEnabled(featureFlags.mobile.aboutApplication) && { icon: Info, label: "Tentang Aplikasi", action: () => navigate("about") },
    { icon: LogOut, label: "Keluar", action: () => setShowLogout(true), danger: true },
  ].filter(Boolean) as { icon: typeof User; label: string; action: () => void; danger?: boolean }[];

  return (
    <div className="flex flex-col h-full bg-background relative">
      {/* Header */}
      <div className="bg-background px-4 pt-5 pb-3 shrink-0 border-b border-border">
        <h1 className="text-[23px] font-extrabold tracking-tight text-foreground">Profil</h1>
      </div>

      <div className="hide-scrollbar flex-1 overflow-y-auto overscroll-contain px-4 py-4 space-y-4">
        {/* Profile Card */}
        <div className="overflow-hidden rounded-2xl border border-border bg-card">
          <div className="bg-card-elevated px-4 py-5 flex items-center gap-4">
            <div className="flex items-center justify-center w-14 h-14 rounded-full bg-gold text-[18px] font-extrabold text-primary-foreground shrink-0">
              BS
            </div>
            <div>
              <p className="text-[17px] font-extrabold text-foreground leading-snug">Budi Santoso</p>
              <p className="text-[12px] text-muted-foreground mt-0.5">Marketing · Resort 3</p>
              <div className="mt-2 flex items-center gap-2">
                <span className="rounded-full bg-gold/15 px-2.5 py-1 text-[11px] font-bold text-gold-light">Kode KSP-0318</span>
                <StatusBadge status={offline ? "Menunggu Sinkronisasi" : "Aktif"} sm />
              </div>
            </div>
          </div>
          <div className="px-4 py-3 grid grid-cols-3 gap-2 border-b border-border">
            {[["3", "Kunjungan Hari Ini"], ["2", "Laporan Terkirim"], ["8,4 km", "Perjalanan"]].map(([val, lbl]) => (
              <div key={lbl} className="text-center py-1">
                <p className="font-['DM_Mono'] text-[16px] font-bold text-foreground">{val}</p>
                <p className="text-[10px] text-muted-foreground mt-0.5 leading-tight">{lbl}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Logo info */}
        <div className="flex items-center gap-3 rounded-2xl border border-border bg-card p-3.5">
          <KspLogo size="sm" />
          <div>
            <p className="text-[13px] font-extrabold text-foreground">KSP Manunggal Makmur Sejahtera</p>
            <p className="text-[12px] text-muted-foreground mt-0.5">Marketing · Resort 3, Bandung</p>
          </div>
        </div>

        {/* Menu */}
        <div className="overflow-hidden rounded-2xl border border-border bg-card divide-y divide-border">
          {menu.map(({ icon: Icon, label, action, danger }) => (
            <button
              key={label}
              onClick={action}
              className={`flex w-full items-center gap-3 px-4 py-4 text-left active:bg-card-elevated transition ${danger ? "text-destructive" : "text-foreground"}`}
            >
              <div className={`flex items-center justify-center w-9 h-9 rounded-xl shrink-0 ${danger ? "bg-[#2a1414]" : "bg-card-elevated"}`}>
                <Icon size={17} className={danger ? "text-destructive" : "text-foreground"} />
              </div>
              <span className="flex-1 text-[14px] font-bold">{label}</span>
              <ChevronRight size={16} className="text-disabled shrink-0" />
            </button>
          ))}
        </div>

        <p className="pb-4 text-center text-[11px] text-muted-foreground/70">Marketing KSP MMS v1.0.0</p>
      </div>

      {/* Logout Modal */}
      {showLogout && (
        <ModalConfirm
          title="Keluar dari aplikasi?"
          desc="Tracking perjalanan akan berhenti setelah Anda keluar. Pastikan semua data telah tersinkron."
          confirmLabel="Ya, Keluar"
          onConfirm={() => resetTo("login")}
          onCancel={() => setShowLogout(false)}
          danger
        />
      )}
    </div>
  );
}

// ── Change Password Screen ────────────────────────────

export function ChangePasswordScreen({ goBack }: NavProps) {
  const [form, setForm] = useState({ current: "", newPass: "", confirm: "" });
  const [show, setShow] = useState({ current: false, newPass: false, confirm: false });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saved, setSaved] = useState(false);

  const set = (key: string) => (v: string) => setForm(f => ({ ...f, [key]: v }));
  const toggleShow = (key: string) => setShow(s => ({ ...s, [key]: !s[key as keyof typeof s] }));

  const validate = () => {
    const e: Record<string, string> = {};
    if (!form.current) e.current = "Password saat ini wajib diisi";
    if (!form.newPass || form.newPass.length < 6) e.newPass = "Password baru minimal 6 karakter";
    if (form.newPass !== form.confirm) e.confirm = "Konfirmasi password tidak sesuai";
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSave = () => {
    if (!validate()) return;
    setSaved(true);
    setTimeout(() => goBack(), 1500);
  };

  const PasswordInput = ({ id, label, value, error }: { id: "current" | "newPass" | "confirm"; label: string; value: string; error?: string }) => (
    <div>
      <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">{label}<span className="text-destructive ml-0.5">*</span></label>
      <div className="relative">
        <input
          type={show[id] ? "text" : "password"}
          value={value}
          onChange={e => set(id)(e.target.value)}
          placeholder="Masukkan password"
          className={`h-12 min-h-11 w-full rounded-xl border px-4 pr-12 text-[14px] text-foreground outline-none placeholder:text-disabled focus:border-gold transition ${error ? "border-destructive bg-[#1f1414]" : "border-border bg-input-background"}`}
        />
        <button type="button" onClick={() => toggleShow(id)} className="absolute right-0 top-0 flex items-center justify-center w-12 h-12 text-muted-foreground">
          {show[id] ? <EyeOff size={18} /> : <Eye size={18} />}
        </button>
      </div>
      {error && <p className="mt-1 text-[12px] text-destructive">{error}</p>}
    </div>
  );

  return (
    <div className="flex flex-col h-full bg-background">
      <PageHeader title="Ubah Password" onBack={goBack} />

      {saved ? (
        <div className="flex-1 flex flex-col items-center justify-center gap-4 px-8 text-center">
          <div className="flex items-center justify-center w-16 h-16 rounded-full bg-[#0f2a1a]">
            <Check size={28} className="text-success" strokeWidth={2.5} />
          </div>
          <p className="text-[17px] font-extrabold text-success">Password Berhasil Diubah</p>
          <p className="text-[13px] text-muted-foreground">Gunakan password baru Anda untuk masuk berikutnya.</p>
        </div>
      ) : (
        <>
          <div className="flex-1 overflow-y-auto px-4 py-4 space-y-4">
            <div className="rounded-2xl border border-border bg-card p-4 space-y-4">
              <PasswordInput id="current" label="Password Saat Ini" value={form.current} error={errors.current} />
              <PasswordInput id="newPass" label="Password Baru" value={form.newPass} error={errors.newPass} />
              <PasswordInput id="confirm" label="Konfirmasi Password Baru" value={form.confirm} error={errors.confirm} />
            </div>

            <div className="rounded-2xl border border-border bg-card p-3.5">
              <p className="text-[12px] text-muted-foreground leading-relaxed">
                Password harus minimal 6 karakter. Hubungi admin jika Anda lupa password saat ini.
              </p>
            </div>
          </div>

          {/* Sticky form action bar — pinned above the persistent bottom nav */}
          <div className="shrink-0 border-t border-border bg-background px-4 py-3 space-y-2.5">
            <PrimaryButton onClick={handleSave}>Simpan Password Baru</PrimaryButton>
            <SecondaryButton onClick={goBack}>Batal</SecondaryButton>
          </div>
        </>
      )}
    </div>
  );
}


function SimpleInfoScreen({ title, children, goBack }: { title: string; children: ReactNode; goBack: () => void }) {
  return <div className="flex h-full flex-col bg-background"><PageHeader title={title} onBack={goBack} /><div className="hide-scrollbar flex-1 overflow-y-auto p-4">{children}</div></div>;
}
export function AccountInfoScreen({ goBack }: NavProps) {
  return <SimpleInfoScreen title="Informasi Akun" goBack={goBack}><div className="rounded-2xl border border-border bg-card p-4"><InfoRow label="Nama Marketing" value="Budi Santoso" /><InfoRow label="Username" value="budi.santoso" /><InfoRow label="Kode Marketing" value="KSP-0318" /><InfoRow label="Resort" value="Resort 3 · Bandung" /><InfoRow label="Status Akun" value="Aktif" /><InfoRow label="Nomor Telepon" value="0812-3456-7890" /></div></SimpleInfoScreen>;
}
export function PermissionStatusScreen({ goBack }: NavProps) {
  const permissions = [["Akses Lokasi", "Diizinkan"], ["Lokasi Latar Belakang", "Diizinkan"], ["Notifikasi Tracking", "Diizinkan"], ["Kamera", "Diizinkan"], ["Penyimpanan Foto", "Diizinkan"]];
  return <SimpleInfoScreen title="Status Izin Aplikasi" goBack={goBack}><div className="overflow-hidden rounded-2xl border border-border bg-card">{permissions.map(([label, value]) => <div key={label} className="flex items-center justify-between border-b border-border px-4 py-4 last:border-0"><div><p className="text-[14px] font-bold text-foreground">{label}</p><p className="mt-0.5 text-[12px] text-muted-foreground">Digunakan untuk aktivitas lapangan.</p></div><StatusBadge status={value} sm /></div>)}</div></SimpleInfoScreen>;
}
export function UsageGuideScreen({ goBack }: NavProps) {
  return <SimpleInfoScreen title="Panduan Penggunaan" goBack={goBack}><div className="space-y-3">{["Masuk menggunakan akun marketing Anda.", "Pastikan tracking aktif sebelum mulai kunjungan.", "Tambahkan konsumen dari menu Konsumen.", "Buat laporan setelah kunjungan selesai.", "Cek Status Sinkronisasi saat internet tersedia.", "Saat offline, data tetap tersimpan di perangkat."].map((text, i) => <div key={text} className="rounded-2xl border border-border bg-card p-4"><p className="text-[12px] font-bold text-gold-light">0{i + 1}</p><p className="mt-1 text-[14px] text-foreground">{text}</p></div>)}</div></SimpleInfoScreen>;
}
export function AboutScreen({ goBack }: NavProps) {
  return <SimpleInfoScreen title="Tentang Aplikasi" goBack={goBack}><div className="rounded-2xl border border-border bg-card p-5 text-center"><div className="flex justify-center"><KspLogo size="md" /></div><h2 className="mt-4 text-[18px] font-extrabold text-foreground">KSP Manunggal Makmur Sejahtera</h2><p className="mt-1 text-[13px] text-muted-foreground">Marketing · Versi 1.0.0</p><p className="mt-5 text-[14px] leading-relaxed text-foreground">Aplikasi monitoring aktivitas lapangan untuk KSP Manunggal Makmur Sejahtera.</p></div></SimpleInfoScreen>;
}
