import { useState } from "react";
import { Eye, EyeOff, AlertCircle, Check } from "lucide-react";
import { KspLogo, PrimaryButton } from "./shared";
import type { NavProps } from "./shared";

type LoginState = "idle" | "loading" | "error-password" | "error-inactive" | "error-offline";

export function LoginScreen({ navigate, offline }: NavProps) {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [remember, setRemember] = useState(false);
  const [state, setState] = useState<LoginState>("idle");

  const errorMsg: Record<string, string> = {
    "error-password": "Username atau password salah. Silakan coba lagi.",
    "error-inactive": "Akun Anda tidak aktif. Hubungi admin untuk bantuan.",
    "error-offline": "Tidak ada koneksi internet. Pastikan internet Anda aktif terlebih dahulu.",
  };

  const handleLogin = () => {
    if (!username.trim() || !password.trim()) return;
    if (offline) { setState("error-offline"); return; }
    setState("loading");
    setTimeout(() => {
      setState("idle");
      navigate("perm-location");
    }, 1600);
  };

  return (
    <div className="flex flex-col h-full bg-background">
      {/* Dark header */}
      <div className="relative overflow-hidden bg-background-secondary px-6 pt-14 pb-10 shrink-0">
        <div className="pointer-events-none absolute -right-8 -top-8 h-36 w-36 rounded-full border border-gold/10" />
        <div className="pointer-events-none absolute -right-2 -top-2 h-20 w-20 rounded-full border border-gold/10" />
        <div className="pointer-events-none absolute right-12 bottom-0 h-px w-32 bg-gradient-to-r from-transparent to-gold/25" />

        <div className="flex flex-col items-center gap-4">
          <KspLogo size="lg" />
          <div className="text-center">
            <h1 className="text-[24px] font-extrabold text-foreground">Selamat Datang</h1>
            <p className="mt-1.5 text-[13px] text-muted-foreground leading-relaxed max-w-[240px]">Masuk menggunakan akun marketing Anda</p>
          </div>
        </div>
      </div>

      {/* Form */}
      <div className="flex-1 overflow-y-auto px-5 pt-7 pb-8 space-y-5">
        {errorMsg[state] && (
          <div className="flex items-start gap-2.5 rounded-2xl bg-[#2a1414] border border-destructive/40 p-4">
            <AlertCircle size={16} className="text-destructive mt-0.5 shrink-0" />
            <p className="text-[13px] text-[#fca5a5] leading-snug">{errorMsg[state]}</p>
          </div>
        )}

        <div>
          <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">Username</label>
          <input
            type="text"
            value={username}
            onChange={e => setUsername(e.target.value)}
            placeholder="Masukkan username Anda"
            className="h-12 min-h-11 w-full rounded-xl border border-border bg-input-background px-4 text-[14px] text-foreground outline-none placeholder:text-disabled focus:border-gold transition"
          />
        </div>

        <div>
          <label className="mb-1.5 block text-[13px] font-bold text-gold-soft">Password</label>
          <div className="relative">
            <input
              type={showPassword ? "text" : "password"}
              value={password}
              onChange={e => setPassword(e.target.value)}
              placeholder="Masukkan password"
              className="h-12 min-h-11 w-full rounded-xl border border-border bg-input-background px-4 pr-12 text-[14px] text-foreground outline-none placeholder:text-disabled focus:border-gold transition"
            />
            <button
              type="button"
              onClick={() => setShowPassword(!showPassword)}
              aria-label={showPassword ? "Sembunyikan password" : "Tampilkan password"}
              className="absolute right-0 top-0 flex items-center justify-center w-12 h-12 text-muted-foreground"
            >
              {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
            </button>
          </div>
        </div>

        <button onClick={() => setRemember(!remember)} className="flex items-center gap-3 w-full min-h-11">
          <div className={`flex items-center justify-center w-5 h-5 rounded-md border-2 transition shrink-0 ${remember ? "bg-gold border-gold" : "border-border bg-input-background"}`}>
            {remember && <Check size={11} className="text-primary-foreground" strokeWidth={3} />}
          </div>
          <span className="text-[14px] text-foreground">Ingat saya</span>
        </button>

        <PrimaryButton onClick={handleLogin} disabled={!username.trim() || !password.trim() || state === "loading"} loading={state === "loading"}>
          {state === "loading" ? "Memproses..." : "Masuk"}
        </PrimaryButton>

        <p className="text-center text-[12px] text-muted-foreground leading-relaxed">
          Lupa akun atau password?{" "}
          <span className="font-bold text-gold-light">Hubungi admin jika perlu bantuan</span>
        </p>
      </div>
    </div>
  );
}
