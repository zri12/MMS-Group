import { useEffect, useMemo, useRef, useState } from "react";
import { Check, Search, X } from "lucide-react";

export interface MarketingPickerItem {
  id: string;
  code: string;
  name: string;
  area: string;
  status: string;
  photo?: string;
}

interface MarketingPickerSheetProps {
  open: boolean;
  items: MarketingPickerItem[];
  value: string;
  onClose: () => void;
  onSelect: (marketingId: string) => void;
}

function statusColor(status: string) {
  if (status === "Aktif") return "bg-[#22C55E]";
  if (status === "Offline") return "bg-[#EF4444]";
  if (status === "GPS Tidak Aktif") return "bg-[#F59E0B]";
  if (status === "Belum Mulai") return "bg-[#9CA3AF]";
  return "bg-[#5B6471]";
}

export function MarketingPickerSheet({ open, items, value, onClose, onSelect }: MarketingPickerSheetProps) {
  const [query, setQuery] = useState("");
  const searchRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (!open) return;
    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    const handleKey = (event: KeyboardEvent) => {
      if (event.key === "Escape") onClose();
    };
    window.addEventListener("keydown", handleKey);
    const timer = window.setTimeout(() => searchRef.current?.focus(), 120);
    return () => {
      document.body.style.overflow = previousOverflow;
      window.clearTimeout(timer);
      window.removeEventListener("keydown", handleKey);
    };
  }, [open, onClose]);

  useEffect(() => {
    if (!open) setQuery("");
  }, [open]);

  const filtered = useMemo(() => {
    const normalized = query.trim().toLowerCase();
    if (!normalized) return items;
    return items.filter(item =>
      item.code.toLowerCase().includes(normalized) || item.name.toLowerCase().includes(normalized)
    );
  }, [items, query]);

  if (!open) return null;

  return (
    <div className="fixed inset-0 z-[1100] w-full max-w-full overflow-x-hidden lg:hidden" role="presentation">
      <button type="button" aria-label="Tutup pilihan marketing" onClick={onClose}
        className="absolute inset-0 z-0 bg-black/75 backdrop-blur-[2px]"/>
      <section role="dialog" aria-modal="true" aria-labelledby="marketing-picker-title"
        className="absolute inset-x-0 bottom-0 z-10 flex max-h-[82dvh] w-full min-w-0 max-w-full flex-col overflow-x-hidden rounded-t-[24px] border-t border-white/[0.08] bg-[#111720] shadow-[0_-16px_48px_rgba(0,0,0,.34)]">
        <div className="shrink-0 px-4 pb-3 pt-2">
          <div className="mx-auto mb-3 h-1 w-10 rounded-full bg-white/20"/>
          <div className="flex items-center justify-between gap-3">
            <div className="min-w-0">
              <h2 id="marketing-picker-title" className="text-[17px] font-semibold text-[#F3F5F7]">Pilih Marketing</h2>
              <p className="text-[11px] text-[#7E8794]">Pilih marketing yang ingin ditampilkan.</p>
            </div>
            <button type="button" onClick={onClose} aria-label="Tutup"
              className="grid size-10 shrink-0 place-items-center rounded-xl border border-white/[0.09] bg-[#0D131B] text-[#A8B0BD] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37]">
              <X size={18}/>
            </button>
          </div>
          <div className="relative mt-3">
            <Search size={16} className="pointer-events-none absolute left-3.5 top-3.5 text-[#7E8794]"/>
            <input ref={searchRef} value={query} onChange={event=>setQuery(event.target.value)}
              placeholder="Cari kode atau nama marketing..."
              className="min-h-11 w-full min-w-0 max-w-full rounded-[13px] border border-white/[0.09] bg-[#0D131B] pl-10 pr-3 text-[13px] text-[#F8FAFC] placeholder:text-[#697482] outline-none focus-visible:border-[#D4AF37]/60 focus-visible:ring-2 focus-visible:ring-[#D4AF37]/15"/>
          </div>
        </div>
        <div className="min-h-0 flex-1 overflow-y-auto overscroll-contain px-4 pb-[max(18px,env(safe-area-inset-bottom))]">
          <button type="button" onClick={()=>onSelect("Semua")}
            className={`mb-2 flex min-h-[68px] w-full min-w-0 max-w-full items-center gap-3 overflow-hidden rounded-[14px] border p-3 text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37] ${
              value==="Semua" ? "border-[#D4AF37]/25 bg-[#D4AF37]/[0.1]" : "border-white/[0.07] bg-[#151C25]"
            }`}>
            <span className="grid size-10 shrink-0 place-items-center rounded-full bg-[#D4AF37]/15 text-[12px] font-extrabold text-[#D4AF37]">ALL</span>
            <span className="min-w-0 flex-1 text-[13px] font-bold text-[#F8FAFC]">Semua Marketing</span>
            {value==="Semua" && <Check size={18} className="shrink-0 text-[#D4AF37]"/>}
          </button>
          {filtered.map(item => (
            <button key={item.id} type="button" onClick={()=>onSelect(item.id)}
              className={`mb-2 flex min-h-[74px] w-full min-w-0 max-w-full items-center gap-3 overflow-hidden rounded-[14px] border p-3 text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4AF37] ${
              value===item.id ? "border-[#D4AF37]/25 bg-[#D4AF37]/[0.1]" : "border-white/[0.07] bg-[#151C25]"
              }`}>
              <span className="relative grid size-11 shrink-0 place-items-center overflow-hidden rounded-full border border-white/10 bg-[#18212d] text-[12px] font-extrabold text-[#D4AF37]">
                {item.code}
                {item.photo && <img src={item.photo} alt="" onError={event=>event.currentTarget.remove()} className="absolute inset-0 size-full object-cover"/>}
              </span>
              <span className="min-w-0 flex-1">
                <span className="block truncate text-[13px] font-bold text-[#F8FAFC]"><b className="mr-1.5 text-[#D4AF37]">{item.code}</b>{item.name}</span>
                <span className="mt-1 flex items-center gap-1.5 text-[11px] text-[#8F98A5]">
                  <i className={`size-1.5 shrink-0 rounded-full ${statusColor(item.status)}`}/>{item.area} · {item.status}
                </span>
              </span>
              {value===item.id && <Check size={18} className="shrink-0 text-[#D4AF37]"/>}
            </button>
          ))}
          {!filtered.length && <p className="py-10 text-center text-[12px] text-[#7E8794]">Marketing tidak ditemukan.</p>}
        </div>
      </section>
    </div>
  );
}
