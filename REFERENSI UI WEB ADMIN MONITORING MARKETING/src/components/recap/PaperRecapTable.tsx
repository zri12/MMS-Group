import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import { Maximize2, X } from "lucide-react";
import type { Marketing, OperationalRecap } from "../../types";

interface PaperRecapTableProps {
  recap: OperationalRecap;
  marketers: Marketing[];
}

const number = (value: number) => new Intl.NumberFormat("id-ID").format(value);
const dateText = (value: string) => new Intl.DateTimeFormat("id-ID", { day:"numeric", month:"long", year:"numeric" }).format(new Date(`${value}T00:00:00`));

export function PaperRecapTable({ recap, marketers }: PaperRecapTableProps) {
  const [fullscreen, setFullscreen] = useState(false);
  useEffect(() => {
    if (!fullscreen) return;
    const previous = document.body.style.overflow;
    document.body.style.overflow = "hidden";
    const close = (event: KeyboardEvent) => event.key === "Escape" && setFullscreen(false);
    window.addEventListener("keydown", close);
    return () => { document.body.style.overflow = previous; window.removeEventListener("keydown", close); };
  }, [fullscreen]);

  const table = <div className="paper-recap-scroll max-w-full overflow-x-auto overscroll-x-contain [scrollbar-width:thin] [-webkit-overflow-scrolling:touch]">
    <table className="w-full min-w-[1680px] border-collapse text-right text-[12px] tabular-nums text-[#E1E5E9]">
      <caption className="sr-only">Rekap Operasional {recap.day}, {dateText(recap.date)}</caption>
      <thead className="sticky top-0 z-20 text-center">
        <tr className="bg-[#2A2515] text-[#E8C65A]">
          <th scope="col" rowSpan={2} className="sticky left-0 z-30 border border-white/[.12] bg-[#2A2515] px-3 py-2.5">MG</th>
          <th scope="colgroup" colSpan={4} className="border border-white/[.12] px-3 py-2.5">ANGGOTA</th>
          <th scope="colgroup" colSpan={4} className="border border-white/[.12] px-3 py-2.5">TARGET</th>
          <th scope="colgroup" colSpan={3} className="border border-white/[.12] px-3 py-2.5">DROP</th>
          <th scope="colgroup" colSpan={3} className="border border-white/[.12] px-3 py-2.5">STORTING</th>
          <th scope="col" rowSpan={2} className="border border-white/[.12] px-3 py-2.5">%</th>
          <th scope="col" rowSpan={2} className="border border-white/[.12] px-3 py-2.5">SIRKULASI<br/>LALU</th>
          <th scope="col" rowSpan={2} className="border border-white/[.12] px-3 py-2.5">SIRKULASI<br/>SEKARANG</th>
          <th scope="col" rowSpan={2} className="border border-white/[.12] px-3 py-2.5 normal-case">Diikuti Oleh</th>
          <th scope="col" rowSpan={2} className="border border-white/[.12] px-3 py-2.5 normal-case">Kas Pagi</th>
        </tr>
        <tr className="bg-[#151C25] text-[#C7CDD4]">
          {["L","M","K","S","Lalu","MSK","KLR","S","Lalu","Kini","Total","Lalu","Kini","Total"].map((label,index) => <th scope="col" key={`${label}-${index}`} className="border border-white/[.12] px-3 py-2">{label}</th>)}
        </tr>
      </thead>
      <tbody>
        {recap.rows.map((row,index) => {
          const marketer = marketers.find(item => item.id === row.marketingId);
          return <tr key={row.id} className={`${index % 2 ? "bg-white/[.012]" : "bg-[#0E141B]"} hover:bg-white/[.035]`}>
            <th scope="row" title={marketer?.name} className="sticky left-0 z-10 border border-white/[.12] bg-[#111820] px-3 py-2.5 text-center font-bold text-[#E8C65A]">{row.mg}<span className="mt-0.5 block text-[9px] font-medium text-[#788391]">{marketer?.name}</span></th>
            {[row.members.l,row.members.m,row.members.k,row.members.s].map((value,i)=><td key={`member-${i}`} className="border border-white/[.12] px-3 py-2.5">{value}</td>)}
            {[row.target.previous,row.target.incoming,row.target.outgoing,row.target.s,row.drop.previous,row.drop.current,row.drop.total,row.storting.previous,row.storting.current,row.storting.total].map((value,i)=><td key={`value-${i}`} className="whitespace-nowrap border border-white/[.12] px-3 py-2.5">{number(value)}</td>)}
            <td className="border border-white/[.12] px-3 py-2.5">{row.percentage == null ? "—" : `${row.percentage}%`}</td>
            <td className="whitespace-nowrap border border-white/[.12] px-3 py-2.5">{number(row.previousCirculation)}</td>
            <td className="whitespace-nowrap border border-white/[.12] px-3 py-2.5">{number(row.currentCirculation)}</td>
            <td className="max-w-28 whitespace-normal border border-white/[.12] px-3 py-2.5 text-center leading-4">{row.followedBy}</td>
            <td className="whitespace-nowrap border border-white/[.12] px-3 py-2.5">{number(row.morningCash)}</td>
          </tr>;
        })}
      </tbody>
    </table>
  </div>;

  return <>
    <div className="mb-3 flex flex-wrap items-center justify-between gap-3">
      <p className="text-xs text-[#7E8896]">Geser tabel ke samping untuk melihat seluruh kolom.</p>
      <button onClick={() => setFullscreen(true)} className="inline-flex min-h-10 items-center gap-2 rounded-xl bg-[#D4AF37] px-4 text-xs font-semibold text-[#080B10] hover:bg-[#E6C45A]"><Maximize2 size={15}/>Perbesar Tabel</button>
    </div>
    <div className="isolate max-w-full overflow-hidden rounded-2xl border border-white/[.1] bg-[#0E141B]">{table}</div>
    {fullscreen && createPortal(
      <div role="dialog" aria-modal="true" aria-label="Rekap Operasional fullscreen" className="fixed inset-0 z-[3000] flex flex-col bg-[#080B10]">
        <div className="flex min-h-16 items-center justify-between gap-4 border-b border-white/[.1] bg-[#0D131A] px-4 sm:px-6">
          <div><h2 className="text-base font-semibold text-white">Rekap Operasional</h2><p className="text-xs text-[#7E8896]">{recap.day} · {dateText(recap.date)}</p></div>
          <button onClick={() => setFullscreen(false)} className="inline-flex min-h-10 items-center gap-2 rounded-xl border border-white/[.1] bg-white/[.04] px-3 text-xs font-semibold text-white"><X size={16}/>Tutup</button>
        </div>
        <div className="min-h-0 flex-1 overflow-auto p-3 sm:p-5">{table}</div>
      </div>, document.body,
    )}
  </>;
}
