import { useEffect } from "react";
import { KspLogo } from "./shared";
import type { NavProps } from "./shared";

export function SplashScreen({ navigate }: NavProps) {
  useEffect(() => {
    const t = setTimeout(() => navigate("login"), 2000);
    return () => clearTimeout(t);
  }, [navigate]);

  return (
    <div className="flex flex-col items-center justify-between bg-background h-full py-16 px-8">
      <div className="h-px w-16 bg-gold rounded-full" />

      <div className="flex flex-col items-center gap-6 text-center">
        <KspLogo size="lg" />

        <div>
          <h1 className="text-[24px] font-extrabold tracking-tight text-foreground leading-snug max-w-[260px]">KSP Manunggal Makmur Sejahtera</h1>
          <p className="mt-3 text-[15px] font-semibold tracking-[0.04em] text-gold-light">Marketing Monitoring</p>
        </div>

        <div className="flex items-center gap-2 mt-1">
          <div className="h-px w-10 bg-gold/35" />
          <div className="h-1 w-1 rounded-full bg-gold/50" />
          <div className="h-px w-10 bg-gold/35" />
        </div>

        <div className="flex gap-2 mt-1">
          {[0, 0.25, 0.5].map((delay, i) => (
            <div
              key={i}
              className="h-1.5 w-1.5 rounded-full bg-gold animate-bounce"
              style={{ animationDelay: `${delay}s`, animationDuration: "1s" }}
            />
          ))}
        </div>
      </div>

      <p className="text-[11px] text-muted-foreground/70">v1.0.0</p>
    </div>
  );
}
