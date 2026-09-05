import React from "react";
import LoadingSchoolIcon from "@/components/ui/loading-school-icon";
import AITextLoading from "@/components/ui/ai-text-loading";
import LoadingSchoolAI from "@/components/ui/loading-school-ai";

export default function Demo() {
  return (
    <div className="min-h-screen bg-slate-950 flex flex-col items-center justify-center p-8 gap-8">
      {/* 1. Standalone School Icon */}
      <div className="p-6 rounded-2xl bg-white/5 border border-white/10 w-full max-w-sm">
        <p className="text-xs text-slate-400 text-center mb-2 font-mono">1. Original: LoadingSchoolIcon</p>
        <LoadingSchoolIcon />
      </div>

      {/* 2. Standalone Kokonut AI Text Loading */}
      <div className="p-6 rounded-2xl bg-white/5 border border-white/10 w-full max-w-sm">
        <p className="text-xs text-slate-400 text-center mb-2 font-mono">2. Kokonut: AITextLoading</p>
        <AITextLoading />
      </div>

      {/* 3. Collaborated Component */}
      <div className="p-6 rounded-2xl bg-white/5 border border-teal-500/30 w-full max-w-sm shadow-xl shadow-teal-950/50">
        <p className="text-xs text-teal-400 text-center mb-2 font-mono">3. Collab: LoadingSchoolAI</p>
        <LoadingSchoolAI />
      </div>
    </div>
  );
}
