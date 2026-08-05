import { useEffect, useState } from "react";
import { Search, X } from "lucide-react";
import type { DayName } from "../../types";

export interface TrackingFilterValue {
  day: DayName;
  status: string;
  area: string;
  search: string;
}

interface TrackingFilterSheetProps {
  open: boolean;
  value: TrackingFilterValue;
  defaultDay: DayName;
  days: DayName[];
  areas: string[];
  onClose: () => void;
  onApply: (value: TrackingFilterValue, resetMarketing: boolean) => void;
}

const statuses = ["Semua", "Aktif", "Offline", "Belum Mulai", "Tidak Dijadwalkan", "GPS Tidak Aktif"];

export function TrackingFilterSheet({
  open, value, defaultDay, days, areas, onClose, onApply,
}: TrackingFilterSheetProps) {
  const [draft, setDraft] = useState(value);
  const [resetMarketing, setResetMarketing] = useState(false);

  useEffect(() => {
    if (open) {
      setDraft(value);
      setResetMarketing(false);
    }
  }, [open, value]);

  useEffect(() => {
    if (!open) return;
    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    const handleKey = (event: KeyboardEvent) => {
      if (event.key === "Escape") onClose();
    };
    window.addEventListener("keydown", handleKey);
    return () => {
      document.body.style.overflow = previousOverflow;
      window.removeEventListener("keydown", handleKey);
    };
  }, [open, onClose]);

  if (!open) return null;

  const reset = () => {
    setDraft({ day:defaultDay, status:"Semua", area:"Semua", search:"" });
    setResetMarketing(true);
  };

  const chipClass = (active: boolean) =>
    `min-h-10 rounded-xl border px-3 text-[12px] font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37] ${
      active ? "border-[#D4AF37]/25 bg-[#D4AF37]/[0.12] text-[#E6C45A]" : "border-white/[0.07] bg-[#151C25] text-[#A7AFBA]"
    }`;

  return (
    <div className="fixed inset-0 z-[1100] w-full max-w-full overflow-x-hidden lg:hidden" role="presentation">
      <button type="button" aria-label="Tutup filter" onClick={onClose} className="absolute inset-0 z-0 bg-black/75 backdrop-blur-[2px]"/>
      <section role="dialog" aria-modal="true" aria-labelledby="tracking-filter-title"
        className="absolute inset-x-0 bottom-0 z-10 flex max-h-[82dvh] w-full min-w-0 max-w-full flex-col overflow-x-hidden rounded-t-[24px] border-t border-white/[0.08] bg-[#111720] shadow-[0_-16px_48px_rgba(0,0,0,.34)]">
        <div className="shrink-0 px-4 pb-3 pt-2">
          <div className="mx-auto mb-3 h-1 w-10 rounded-full bg-white/20"/>
          <div className="flex items-center justify-between">
            <div>
              <h2 id="tracking-filter-title" className="text-[17px] font-semibold text-[#F3F5F7]">Filter Tracking</h2>
              <p className="text-[11px] text-[#7E8794]">Atur data dan marker yang ingin ditampilkan.</p>
            </div>
            <button type="button" onClick={onClose} aria-label="Tutup"
              className="grid size-10 place-items-center rounded-xl border border-white/[0.09] bg-[#0D131B] text-[#A8B0BD] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]">
              <X size={18}/>
            </button>
          </div>
        </div>
        <div className="min-h-0 flex-1 space-y-5 overflow-y-auto overscroll-contain px-4 pb-4">
          <fieldset>
            <legend className="mb-2 text-[12px] font-bold text-[#A8B0BD]">Hari</legend>
            <div className="grid grid-cols-3 gap-2">
              {days.map(day=><button key={day} type="button" onClick={()=>setDraft(current=>({...current,day}))} className={chipClass(draft.day===day)}>{day}</button>)}
            </div>
          </fieldset>
          <fieldset>
            <legend className="mb-2 text-[12px] font-bold text-[#A8B0BD]">Status</legend>
            <div className="flex flex-wrap gap-2">
              {statuses.map(status=><button key={status} type="button" onClick={()=>setDraft(current=>({...current,status}))} className={chipClass(draft.status===status)}>{status==="Semua"?"Semua Status":status}</button>)}
            </div>
          </fieldset>
          <fieldset>
            <legend className="mb-2 text-[12px] font-bold text-[#A8B0BD]">Area</legend>
            <div className="flex flex-wrap gap-2">
              {["Semua",...areas].map(area=><button key={area} type="button" onClick={()=>setDraft(current=>({...current,area}))} className={chipClass(draft.area===area)}>{area==="Semua"?"Semua Area":area}</button>)}
            </div>
          </fieldset>
          <label className="block">
            <span className="mb-2 block text-[12px] font-bold text-[#A8B0BD]">Pencarian</span>
            <span className="relative block">
              <Search size={16} className="pointer-events-none absolute left-3.5 top-3.5 text-[#7E8794]"/>
              <input value={draft.search} onChange={event=>setDraft(current=>({...current,search:event.target.value}))}
                placeholder="Cari marketing atau kode..."
                className="min-h-11 w-full rounded-[13px] border border-white/[0.09] bg-[#0D131B] pl-10 pr-3 text-[13px] text-[#F8FAFC] placeholder:text-[#697482] outline-none focus-visible:border-[#D4AF37]/60 focus-visible:ring-2 focus-visible:ring-[#D4AF37]/15"/>
            </span>
          </label>
        </div>
        <footer className="grid shrink-0 grid-cols-[.8fr_1.2fr] gap-2 border-t border-white/[0.07] bg-[#10161E] px-4 pb-[max(14px,env(safe-area-inset-bottom))] pt-3">
          <button type="button" onClick={reset}
            className="min-h-11 rounded-[13px] border border-white/[0.07] bg-[#1A222D] text-[13px] font-semibold text-[#F3F5F7] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]">
            Reset
          </button>
          <button type="button" onClick={()=>onApply(draft, resetMarketing)}
            className="min-h-11 rounded-[13px] bg-[#D4AF37] text-[13px] font-semibold text-[#080B10] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5D86C]">
            Terapkan Filter
          </button>
        </footer>
      </section>
    </div>
  );
}
