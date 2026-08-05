import { ArrowLeft, ChevronRight, FileText, Search, Printer, Share2 } from "lucide-react";
import { useState } from "react";
import { dailyReports } from "../data/dailyReports";
import { operationalRecaps } from "../data/operationalRecaps";
import { getMarketingName } from "../utils/dataRelations";
import { getMarketingById } from "../utils/dataRelations";

type Navigate = (name: string, params?: Record<string, unknown>) => void;
const money = (value: number) => new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(value);
const dateText = (value: string) => new Intl.DateTimeFormat("id-ID", { day: "numeric", month: "long", year: "numeric" }).format(new Date(`${value}T00:00:00`));
const circulationFor = (day: string, marketingId: string) => operationalRecaps
  .find(recap => recap.day === day)?.rows
  .find(row => row.marketingId === marketingId)?.currentCirculation ?? 0;

function Header({ title, subtitle, back }: { title: string; subtitle: string; back: () => void }) {
  return <div className="mb-5 flex items-center gap-3">
    <button onClick={back} aria-label="Kembali" className="grid size-11 shrink-0 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE] hover:text-white print:hidden"><ArrowLeft size={19}/></button>
    <div className="min-w-0 flex-1"><h1 className="text-xl font-semibold text-white sm:text-2xl">{title}</h1><p className="mt-0.5 text-xs text-[#7E8896]">{subtitle}</p></div>
    <div className="flex items-center gap-1.5 shrink-0 print:hidden">
      <button onClick={() => window.print()} className="grid size-9 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE] hover:bg-white/10 hover:text-white transition" title="Cetak"><Printer size={16}/></button>
      <button onClick={() => window.open(`https://wa.me/?text=${encodeURIComponent(`Laporan Harian\n${title}`)}`, '_blank')} className="grid size-9 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE] hover:bg-[#25D366]/20 hover:text-[#25D366] transition" title="Share WA"><Share2 size={16}/></button>
    </div>
  </div>;
}
function Field({ label, value, wide = false }: { label: string; value: React.ReactNode; wide?: boolean }) {
  return <div className={wide ? "sm:col-span-2" : ""}><p className="text-[10px] font-semibold uppercase tracking-[.08em] text-[#687483]">{label}</p><div className="mt-1.5 text-sm text-[#E5E9ED]">{value}</div></div>;
}
function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return <section className="rounded-2xl border border-white/[.07] bg-[#0D131A] p-4 sm:p-5"><h2 className="mb-4 text-sm font-semibold text-[#E8C65A]">{title}</h2><div className="grid gap-4 sm:grid-cols-2">{children}</div></section>;
}

export function DailyReportListPage({ day, navigate, back }: { day: string; navigate: Navigate; back: () => void }) {
  const [query, setQuery] = useState("");
  const rows = dailyReports.filter(item => item.day === day && `${getMarketingName(item.marketingId)} ${item.resort}`.toLowerCase().includes(query.toLowerCase()));
  return <div className="px-4 pb-24 pt-5 sm:px-6 lg:px-8 lg:pb-8">
    <Header title="Laporan Operasional Harian" subtitle={`${rows.length} laporan Input Setoran untuk ${day}`} back={back}/>
    <label className="mb-4 flex min-h-12 items-center gap-3 rounded-xl border border-white/[.08] bg-[#11171F] px-4 focus-within:border-[#D4AF37]/40"><Search size={17} className="text-[#687483]"/><input value={query} onChange={event => setQuery(event.target.value)} placeholder="Cari marketing atau resort" className="min-w-0 flex-1 bg-transparent text-sm text-white outline-none placeholder:text-[#647080]"/></label>
    <div className="grid gap-3 lg:hidden">{rows.map(item => <button key={item.id} onClick={() => navigate("LaporanHarianDetail", { id: item.id })} className="group rounded-2xl border border-white/[.07] bg-[#0D131A] p-4 text-left hover:border-[#D4AF37]/30">
      <div className="flex items-center gap-3"><span className="grid size-11 shrink-0 place-items-center rounded-xl bg-[#D4AF37]/10 text-[#E8C65A]"><FileText size={20}/></span><span className="min-w-0 flex-1"><strong className="block truncate text-sm text-white">{getMarketingName(item.marketingId)}</strong><span className="mt-1 block text-xs text-[#7E8896]">{item.id} · {item.resort}</span></span><ChevronRight size={17} className="text-[#5E6977] group-hover:text-[#D4AF37]"/></div>
      <div className="mt-4 grid grid-cols-2 gap-3 border-t border-white/[.06] pt-3"><Mini label="Storting" value={money(item.storting)}/><Mini label="Asuransi" value={money(item.insuranceAmount)}/><Mini label="Drop" value={money(item.drop)}/><Mini label="Tabungan keluar" value={money(item.withdrawalSaving)}/><Mini label="Target masuk" value={`${money(item.incomingTargetAmount)} · ${item.incomingTargetPeople} orang`}/><Mini label="Target keluar" value={`${money(item.outgoingTargetAmount)} · ${item.outgoingTargetPeople} orang`}/><Mini label="Sirkulasi" value={money(circulationFor(item.day,item.marketingId))}/><Mini label="Jumlah target" value={`${money(item.totalTargetAmount)} · ${item.totalTargetPeople} orang`}/><Mini label="Sinkronisasi" value={item.syncStatus}/></div>
    </button>)}</div>
    <div className="hidden overflow-x-auto rounded-2xl border border-white/[.08] lg:block"><table className="w-full min-w-[1750px] text-left text-xs"><thead className="bg-[#151C25] text-[#E8C65A]"><tr>{["No","Marketing","Kode","Resort","Tanggal","Storting","Asuransi","Drop","Tabungan Keluar","Target Lama","Target Masuk","Target Keluar","Sirkulasi","Jumlah Target","Drop Baru","Drop Lanjut","Status","Aksi"].map(label=><th key={label} className="whitespace-nowrap px-4 py-3">{label}</th>)}</tr></thead><tbody>{rows.map((item,index)=>{const marketing=getMarketingById(item.marketingId);return <tr key={item.id} className="border-t border-white/[.06] text-[#DCE1E6] hover:bg-white/[.025]"><td className="px-4 py-3">{index+1}</td><td className="px-4 py-3 font-semibold text-white">{marketing?.name}</td><td className="px-4 py-3 text-[#E8C65A]">{marketing?.code}</td><td className="px-4 py-3">{item.resort}</td><td className="whitespace-nowrap px-4 py-3">{dateText(item.date)} · {item.time}</td>{[item.storting,item.insuranceAmount,item.drop,item.withdrawalSaving].map((value,i)=><td key={i} className="whitespace-nowrap px-4 py-3">{money(value)}</td>)}<td className="whitespace-nowrap px-4 py-3">{money(item.previousTargetAmount)} · {item.previousTargetPeople} orang</td><td className="whitespace-nowrap px-4 py-3">{money(item.incomingTargetAmount)} · {item.incomingTargetPeople} orang</td><td className="whitespace-nowrap px-4 py-3">{money(item.outgoingTargetAmount)} · {item.outgoingTargetPeople} orang</td><td className="whitespace-nowrap px-4 py-3">{money(circulationFor(item.day,item.marketingId))}</td><td className="whitespace-nowrap px-4 py-3">{money(item.totalTargetAmount)} · {item.totalTargetPeople} orang</td><td className="whitespace-nowrap px-4 py-3">{money(item.newDrop)}</td><td className="whitespace-nowrap px-4 py-3">{money(item.continuedDrop)}</td><td className="px-4 py-3">{item.syncStatus}</td><td className="px-4 py-3"><button onClick={()=>navigate("LaporanHarianDetail",{id:item.id})} className="font-semibold text-[#E8C65A]">Detail</button></td></tr>})}</tbody></table></div>
    {!rows.length&&<div className="rounded-2xl border border-dashed border-white/[.1] py-14 text-center text-sm text-[#7E8896]">Belum ada laporan operasional pada hari {day}.</div>}
  </div>;
}

export function DailyReportDetailPage({ id, back }: { id: string; back: () => void }) {
  const item = dailyReports.find(row => row.id === id);
  if (!item) return <><Header title="Detail Laporan Harian" subtitle="Data tidak ditemukan" back={back}/><div className="rounded-2xl border border-dashed border-white/[.1] py-14 text-center text-sm text-[#7E8896]">Laporan tidak ditemukan.</div></>;
  return <div className="space-y-4 px-4 pb-8 pt-5 sm:px-6 lg:px-8">
    <Header title="Detail Laporan Harian" subtitle={`${item.id} · ${getMarketingName(item.marketingId)}`} back={back}/>
    <Section title="Informasi Pengirim"><Field label="Marketing" value={getMarketingName(item.marketingId)}/><Field label="Kode marketing" value={item.marketingId}/><Field label="Resort" value={item.resort}/><Field label="Hari" value={item.day}/><Field label="Tanggal" value={dateText(item.date)}/><Field label="Waktu" value={item.time}/></Section>
    <Section title="Setoran dan Keuangan"><Field label="Storting" value={money(item.storting)}/><Field label="Asuransi" value={money(item.insuranceAmount)}/><Field label="Drop" value={money(item.drop)}/><Field label="Tabungan keluar" value={money(item.withdrawalSaving)}/></Section>
    <Section title="Target Anggota"><Field label="Target lama nominal" value={money(item.previousTargetAmount)}/><Field label="Target lama orang" value={`${item.previousTargetPeople} orang`}/><Field label="Target masuk nominal" value={money(item.incomingTargetAmount)}/><Field label="Target masuk orang" value={`${item.incomingTargetPeople} orang`}/><Field label="Target keluar nominal" value={money(item.outgoingTargetAmount)}/><Field label="Target keluar orang" value={`${item.outgoingTargetPeople} orang`}/><Field label="Jumlah nominal" value={<strong className="text-[#E8C65A]">{money(item.totalTargetAmount)}</strong>}/><Field label="Jumlah orang" value={<strong className="text-[#E8C65A]">{item.totalTargetPeople} orang</strong>}/></Section>
    <Section title="Rincian Drop"><Field label="Drop baru" value={money(item.newDrop)}/><Field label="Drop lanjut" value={money(item.continuedDrop)}/></Section>
    <Section title="Catatan"><Field label="Catatan marketing" value={item.notes || "Tidak ada catatan"} wide/></Section>
    <Section title="Informasi Sistem"><Field label="ID laporan" value={item.id}/><Field label="Status sinkronisasi" value={item.syncStatus}/></Section>
  </div>;
}

function Mini({ label, value }: { label: string; value: string }) {
  return <span className="min-w-0"><span className="block text-[10px] text-[#697483]">{label}</span><strong className="mt-1 block truncate text-[11px] text-[#DDE2E7]">{value}</strong></span>;
}
