import { useState } from "react";
export function LogoImage({ className = "size-9" }: { className?: string }) {
  const [failed, setFailed] = useState(false);
  if (failed) return <div className={`${className} grid shrink-0 place-items-center rounded-full bg-[#10251d] text-[10px] font-extrabold text-[#65c78f] ring-1 ring-emerald-400/30`}>KSP</div>;
  return <img src="/LOGO-KSP.jpeg" alt="Logo KSP" onError={()=>setFailed(true)} className={`${className} shrink-0 rounded-full object-cover`}/>;
}
