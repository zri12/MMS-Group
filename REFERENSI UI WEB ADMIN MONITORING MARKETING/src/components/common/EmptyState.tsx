import { Inbox } from "lucide-react";

export function EmptyState({ title = "Belum ada data", description = "Belum ada data untuk ditampilkan." }: { title?: string; description?: string }) {
  return <div className="flex flex-col items-center px-6 py-14 text-center">
    <span className="grid size-12 place-items-center rounded-2xl border border-[#2A3038] bg-[#1B2128] text-[#69707A]"><Inbox size={22}/></span>
    <p className="mt-4 text-[14px] font-bold text-white">{title}</p>
    <p className="mt-1 max-w-sm text-[12px] leading-relaxed text-[#69707A]">{description}</p>
  </div>;
}
