import { ArrowLeft, Printer, Share2 } from "lucide-react";
import { PaperRecapTable } from "../components/recap/PaperRecapTable";
import { operationalRecaps } from "../data/operationalRecaps";
import { marketers } from "../data/marketers";
import type { DayName } from "../types";

const days: DayName[] = ["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
const dateText = (value: string) => new Intl.DateTimeFormat("id-ID", { day:"numeric", month:"long", year:"numeric" }).format(new Date(`${value}T00:00:00`));

export function OperationalRecapPage({ day, setDay, back }: { day: string; setDay: (day: string) => void; back: () => void }) {
  const recap = operationalRecaps.find(item => item.day === day);
  return <div className="min-w-0 max-w-full px-4 pb-6 pt-5 sm:px-6 lg:px-8">
    <div className="mb-5 flex items-center gap-3">
      <button onClick={back} aria-label="Kembali" className="grid size-11 shrink-0 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE] hover:text-white print:hidden"><ArrowLeft size={19}/></button>
      <div className="min-w-0 flex-1"><h1 className="text-xl font-semibold text-white sm:text-2xl">Rekap Operasional</h1><p className="mt-0.5 text-xs text-[#7E8896]">Rekap seluruh marketing dalam format tabel operasional.</p></div>
      <div className="flex items-center gap-1.5 shrink-0 print:hidden">
        <button onClick={() => window.print()} className="grid size-9 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE] hover:bg-white/10 hover:text-white transition" title="Cetak"><Printer size={16}/></button>
        <button onClick={() => window.open(`https://wa.me/?text=${encodeURIComponent(`Rekap Operasional Harian\nHari: ${day}`)}`, '_blank')} className="grid size-9 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE] hover:bg-[#25D366]/20 hover:text-[#25D366] transition" title="Share WA"><Share2 size={16}/></button>
      </div>
    </div>
    <div className="mb-5 flex gap-2 overflow-x-auto pb-1">
      {days.map(item => <button key={item} onClick={() => setDay(item)} className={`min-h-10 shrink-0 rounded-xl border px-4 text-xs font-semibold ${item === day ? "border-[#D4AF37] bg-[#D4AF37] text-[#080B10]" : "border-white/[.08] bg-[#151C25] text-[#A7AFBA]"}`}>{item}</button>)}
    </div>
    {!recap ? <div className="rounded-2xl border border-dashed border-white/[.1] py-14 text-center text-sm text-[#7E8896]">Rekap operasional untuk hari {day} belum tersedia.</div> : <>
      <div className="mb-3 flex flex-wrap gap-x-10 gap-y-2 border-b border-white/[.08] pb-3 text-sm text-[#DCE1E6]"><span><b className="text-[#7E8896]">No :</b> {recap.reportNumber}</span><span><b className="text-[#7E8896]">Hari :</b> {recap.day}</span><span><b className="text-[#7E8896]">Tanggal :</b> {dateText(recap.date)}</span></div>
      <PaperRecapTable recap={recap} marketers={marketers}/>
    </>}
  </div>;
}
