import { ClipboardList, Home, MapPin, UserRound, Users } from "lucide-react";

const tabs = [
  ["Dashboard", Home, "Dashboard"],
  ["MarketingList", Users, "Marketing"],
  ["DailyHub", ClipboardList, "Rencana Kerja"],
  ["TrackingMap", MapPin, "Tracking"],
  ["Profil", UserRound, "Profil"],
] as const;

export function BottomNavigation({ current, navigate }: {
  current: string;
  navigate: (route: string) => void;
}) {
  return (
    <nav
      aria-label="Navigasi utama"
      className="fixed inset-x-0 bottom-0 z-40 grid min-h-[78px] w-full min-w-0 max-w-full grid-cols-5 items-start gap-0.5 overflow-hidden border-t border-[var(--border-subtle)] bg-[var(--bg-secondary)] px-2 pb-[max(8px,env(safe-area-inset-bottom))] pt-1.5 shadow-[0_-8px_24px_rgba(0,0,0,.18)] lg:hidden"
    >
      {tabs.map(([route, Icon, label]) => {
        const active = current === route;

        return (
          <button
            key={route}
            type="button"
            aria-label={`Buka ${label}`}
            aria-current={active ? "page" : undefined}
            onClick={() => navigate(route)}
            className="relative flex h-[60px] min-w-0 flex-col items-center justify-center gap-1 overflow-hidden rounded-xl border border-transparent px-0.5 py-0.5 text-[10.5px] font-semibold transition-[background-color,color,transform] duration-200 ease-out hover:bg-white/[0.04] active:scale-[.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]/60 focus-visible:ring-offset-1 focus-visible:ring-offset-[#0A0D12]"
          >
            <span
              aria-hidden="true"
              className={`grid size-8 shrink-0 place-items-center rounded-xl transition-[background-color,box-shadow,transform] duration-200 ease-out ${
                active
                  ? "bg-[#D4AF37]"
                  : "scale-100 bg-transparent"
              }`}
            >
              <Icon
                size={active ? 21 : 20}
                strokeWidth={2}
                className={`shrink-0 transition-colors duration-200 ${
                  active ? "text-[#0B0E13]" : "text-[#8F98A5]"
                }`}
              />
            </span>
            <span
              className={`block min-h-3 max-w-full overflow-hidden text-ellipsis whitespace-nowrap text-center text-[10px] font-bold leading-3 transition-colors duration-200 ${
                active ? "text-[#E8C65A]" : "text-[#8F98A5]"
              }`}
            >
              {label}
            </span>
          </button>
        );
      })}
    </nav>
  );
}
