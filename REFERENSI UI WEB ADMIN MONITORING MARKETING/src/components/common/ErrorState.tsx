import { AlertTriangle } from "lucide-react";

export function ErrorState({ message, onRetry }: { message: string; onRetry?: () => void }) {
  return <div role="alert" className="m-4 rounded-2xl border border-red-500/20 bg-red-500/[0.08] p-4 text-[13px] text-red-300">
    <div className="flex items-center gap-2 font-semibold"><AlertTriangle size={17}/>{message}</div>
    {onRetry&&<button onClick={onRetry} className="mt-3 min-h-11 rounded-xl border border-red-400/20 bg-red-500/10 px-3 font-bold text-red-200 hover:bg-red-500/15">Coba lagi</button>}
  </div>;
}
