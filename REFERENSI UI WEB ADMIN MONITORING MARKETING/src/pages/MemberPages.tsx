import { ArrowLeft, ChevronRight, MapPin, Search, UserRound, Printer, Share2 } from "lucide-react";
import { useState } from "react";
import { members } from "../data/members";
import { getMarketingById, getMarketingName } from "../utils/dataRelations";

type Navigate = (name: string, params?: Record<string, unknown>) => void;
const money = (value: number) => new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",maximumFractionDigits:0}).format(value);
const dateText = (value: string) => new Intl.DateTimeFormat("id-ID",{day:"numeric",month:"long",year:"numeric"}).format(new Date(`${value}T00:00:00`));
const statusClass = (value: string) => value === "Disetujui" ? "bg-emerald-400/10 text-emerald-300" : value === "Ditolak" ? "bg-red-400/10 text-red-300" : "bg-amber-400/10 text-amber-300";

function Header({title,subtitle,back}:{title:string;subtitle:string;back:()=>void}) {
  return <div className="mb-5 flex items-center gap-3">
    <button onClick={back} aria-label="Kembali" className="grid size-11 shrink-0 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE] print:hidden"><ArrowLeft size={19}/></button>
    <div className="min-w-0 flex-1"><h1 className="text-xl font-semibold text-white sm:text-2xl">{title}</h1><p className="mt-0.5 text-xs text-[#7E8896]">{subtitle}</p></div>
    <div className="flex items-center gap-1.5 shrink-0 print:hidden">
      <button onClick={() => window.print()} className="grid size-9 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE] hover:bg-white/10 hover:text-white transition" title="Cetak"><Printer size={16}/></button>
      <button onClick={() => window.open(`https://wa.me/?text=${encodeURIComponent(`Data Anggota\n${title}`)}`, '_blank')} className="grid size-9 place-items-center rounded-xl border border-white/[.08] bg-white/[.035] text-[#AAB2BE] hover:bg-[#25D366]/20 hover:text-[#25D366] transition" title="Share WA"><Share2 size={16}/></button>
    </div>
  </div>;
}
function Section({title,children}:{title:string;children:React.ReactNode}) {
  return <section className="rounded-2xl border border-white/[.07] bg-[#0D131A] p-4 sm:p-5"><h2 className="mb-4 text-sm font-semibold text-[#E8C65A]">{title}</h2><div className="grid gap-4 sm:grid-cols-2">{children}</div></section>;
}
function Field({label,value,wide=false}:{label:string;value:React.ReactNode;wide?:boolean}) {
  return <div className={wide?"sm:col-span-2":""}><p className="text-[10px] font-semibold uppercase tracking-[.08em] text-[#687483]">{label}</p><div className="mt-1.5 text-sm leading-6 text-[#E5E9ED]">{value || "—"}</div></div>;
}

export function MemberListPage({day,navigate,back,filterCode}:{day:string;navigate:Navigate;back:()=>void;filterCode?:string}) {
  const [query,setQuery]=useState("");
  const rows=members.filter(item=>item.day===day&&(!filterCode||item.marketingId===filterCode)&&`${item.name} ${item.memberNumber} ${item.phone}`.toLowerCase().includes(query.toLowerCase()));
  return <div className="px-4 pb-24 pt-5 sm:px-6 lg:px-8 lg:pb-8">
    <Header title="Data Anggota" subtitle={`Data anggota yang dicatat marketing pada hari ${day}.`} back={back}/>
    <label className="mb-4 flex min-h-12 items-center gap-3 rounded-xl border border-white/[.08] bg-[#11171F] px-4"><Search size={17} className="text-[#687483]"/><input value={query} onChange={event=>setQuery(event.target.value)} placeholder="Cari nama, nomor anggota, atau nomor HP" className="min-w-0 flex-1 bg-transparent text-sm text-white outline-none"/></label>
    <div className="space-y-3 lg:hidden">{rows.map(item=><button key={item.id} onClick={()=>navigate("AnggotaDetail",{id:item.id})} className="flex w-full items-center gap-3 rounded-2xl border border-white/[.07] bg-[#0D131A] p-4 text-left"><img src={item.memberPhoto} alt={item.name} className="size-12 rounded-xl object-cover"/><span className="min-w-0 flex-1"><strong className="block truncate text-sm text-white">{item.name}</strong><span className="mt-1 block text-xs text-[#7E8896]">{item.memberNumber} · {getMarketingName(item.marketingId)}</span><span className={`mt-2 inline-flex rounded-full px-2 py-1 text-[10px] font-semibold ${statusClass(item.approvalStatus)}`}>{item.approvalStatus}</span></span><ChevronRight size={17} className="text-[#66717E]"/></button>)}</div>
    <div className="hidden overflow-x-auto rounded-2xl border border-white/[.08] lg:block"><table className="w-full min-w-[1100px] text-left text-xs"><thead className="bg-[#151C25] text-[#E8C65A]"><tr>{["No","Nama","No. Anggota","No. Pinjaman","Nomor HP","Usaha","Resort","Marketing","Status","Tanggal Input","Aksi"].map(label=><th key={label} className="px-4 py-3">{label}</th>)}</tr></thead><tbody>{rows.map((item,index)=><tr key={item.id} className="border-t border-white/[.06] text-[#DCE1E6] hover:bg-white/[.025]"><td className="px-4 py-3">{index+1}</td><td className="px-4 py-3 font-semibold text-white">{item.name}</td><td className="px-4 py-3">{item.memberNumber}</td><td className="px-4 py-3">{item.loanNumber}</td><td className="px-4 py-3">{item.phone}</td><td className="px-4 py-3">{item.business}</td><td className="px-4 py-3">{item.resort}</td><td className="px-4 py-3">{getMarketingName(item.marketingId)}</td><td className="px-4 py-3"><span className={`rounded-full px-2 py-1 text-[10px] font-semibold ${statusClass(item.approvalStatus)}`}>{item.approvalStatus}</span></td><td className="px-4 py-3">{dateText(item.date)} · {item.time}</td><td className="px-4 py-3"><button onClick={()=>navigate("AnggotaDetail",{id:item.id})} className="font-semibold text-[#E8C65A]">Detail</button></td></tr>)}</tbody></table></div>
    {!rows.length&&<div className="rounded-2xl border border-dashed border-white/[.1] py-14 text-center text-sm text-[#7E8896]">Belum ada data anggota pada hari {day}.</div>}
  </div>;
}

export function MemberDetailPage({id,back,navigate}:{id:string;back:()=>void;navigate:Navigate}) {
  const item=members.find(row=>row.id===id);
  if(!item)return <><Header title="Detail Anggota" subtitle="Data tidak ditemukan" back={back}/></>;
  const marketing=getMarketingById(item.marketingId);
  return <div className="space-y-4 px-4 pb-8 pt-5 sm:px-6 lg:px-8">
    <Header title={item.name} subtitle={`${item.memberNumber} · ${item.resort}`} back={back}/>
    <Section title="Identitas Anggota"><Field label="Nama" value={item.name}/><Field label="Nomor anggota" value={item.memberNumber}/><Field label="Nomor pinjaman" value={item.loanNumber}/><Field label="Nomor HP" value={item.phone}/><Field label="Usaha" value={item.business}/><Field label="Alamat" value={item.address} wide/></Section>
    <Section title="Informasi Pinjaman"><Field label="Jumlah pinjaman" value={money(item.loanAmount)}/><Field label="Angsuran" value={money(item.installmentAmount)}/><Field label="Asuransi" value={money(item.insuranceAmount)}/><Field label="Jaminan" value={item.collateral}/></Section>
    <Section title="Status Persetujuan"><Field label="Status" value={<span className={`rounded-full px-2.5 py-1 text-xs font-semibold ${statusClass(item.approvalStatus)}`}>{item.approvalStatus}</span>} wide/></Section>
    <Section title="Foto Anggota"><div className="sm:col-span-2"><img src={item.memberPhoto} alt={item.name} className="max-h-80 w-full rounded-xl bg-[#111820] object-contain"/></div></Section>
    <Section title="Lokasi Pengambilan Data"><Field label="Alamat lokasi" value={<span className="inline-flex gap-2"><MapPin size={14}/>{item.locationAddress}</span>} wide/><Field label="Koordinat" value={item.latitude&&item.longitude?`${item.latitude}, ${item.longitude}`:"—"} wide/></Section>
    <Section title="Informasi Marketing"><Field label="Marketing" value={<button onClick={()=>navigate("MarketingDetail",{id:item.marketingId})} className="text-[#E8C65A] hover:underline">{marketing?.code} · {marketing?.name}</button>}/><Field label="Resort" value={item.resort}/></Section>
    <Section title="Informasi Sistem"><Field label="ID data" value={item.id}/><Field label="Waktu input" value={`${dateText(item.date)} · ${item.time}`}/><Field label="Sinkronisasi" value={item.syncStatus}/></Section>
  </div>;
}
