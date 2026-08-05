import { ArrowLeft, CalendarDays, Search } from "lucide-react";
import { useState } from "react";
import { marketers } from "../data/marketers";
import { schedules } from "../data/schedules";
import { getMarketingName } from "../utils/dataRelations";

const dateText=(value:string)=>new Intl.DateTimeFormat("id-ID",{day:"numeric",month:"long",year:"numeric"}).format(new Date(`${value}T00:00:00`));
const tone=(value:string)=>value==="Selesai"?"bg-emerald-400/10 text-emerald-300":value==="Berlangsung"?"bg-blue-400/10 text-blue-300":value==="Dibatalkan"?"bg-red-400/10 text-red-300":"bg-amber-400/10 text-amber-300";

export function ScheduleListPage({day,back}:{day:string;back:()=>void}) {
  const [query,setQuery]=useState("");const [marketing,setMarketing]=useState("Semua");
  const rows=schedules.filter(item=>item.day===day&&(marketing==="Semua"||item.marketingId===marketing)&&`${item.consumerName} ${item.agenda} ${item.area}`.toLowerCase().includes(query.toLowerCase()));
  return <div><div className="mb-5 flex items-center gap-3"><button onClick={back} className="grid size-11 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE]"><ArrowLeft size={19}/></button><div><h1 className="text-xl font-semibold text-white sm:text-2xl">Jadwal Marketing</h1><p className="text-xs text-[#7E8896]">Jadwal kunjungan marketing pada hari {day}.</p></div></div>
    <div className="mb-4 grid gap-3 sm:grid-cols-[1fr_240px]"><label className="flex min-h-11 items-center gap-3 rounded-xl border border-white/[.08] bg-[#11171F] px-3"><Search size={16}/><input value={query} onChange={event=>setQuery(event.target.value)} placeholder="Cari konsumen, agenda, atau lokasi" className="min-w-0 flex-1 bg-transparent text-sm text-white outline-none"/></label><select value={marketing} onChange={event=>setMarketing(event.target.value)} className="min-h-11 rounded-xl border border-white/[.08] bg-[#11171F] px-3 text-xs text-white"><option>Semua</option>{marketers.map(item=><option key={item.id} value={item.id}>{item.code} · {item.name}</option>)}</select></div>
    <div className="grid gap-3 xl:grid-cols-2">{rows.map(item=><section key={item.id} className="rounded-2xl border border-white/[.07] bg-[#0D131A] p-4"><div className="flex items-start gap-3"><span className="grid size-11 shrink-0 place-items-center rounded-xl bg-[#D4AF37]/10 text-[#E8C65A]"><CalendarDays size={19}/></span><div className="min-w-0 flex-1"><div className="flex flex-wrap items-center gap-2"><strong className="text-sm text-white">{item.startTime} · {item.consumerName}</strong><span className={`rounded-full px-2 py-1 text-[10px] font-semibold ${tone(item.status)}`}>{item.status}</span></div><p className="mt-1 text-xs text-[#A0A9B4]">{item.agenda}</p><p className="mt-2 text-xs text-[#727D8A]">{item.area} · {item.resort} · {dateText(item.date)}</p><p className="mt-1 text-[10px] text-[#E8C65A]">{item.marketingId} · {getMarketingName(item.marketingId)}</p></div></div></section>)}</div>
    {!rows.length&&<div className="rounded-2xl border border-dashed border-white/[.1] py-14 text-center text-sm text-[#7E8896]">Belum ada jadwal marketing pada hari {day}.</div>}</div>;
}
