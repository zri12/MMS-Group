export function LoadingState({ label = "Memuat data..." }: { label?: string }) {
  return <div className="space-y-3 p-5" aria-label={label} aria-busy="true">
    <span className="block h-4 w-32 animate-pulse rounded-lg bg-[#2A3038]"/>
    <span className="block h-20 w-full animate-pulse rounded-2xl border border-[#2A3038] bg-[#151A20]"/>
    <span className="block h-20 w-full animate-pulse rounded-2xl border border-[#2A3038] bg-[#151A20]"/>
  </div>;
}
