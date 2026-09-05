"use client";

/**
 * @title: LoadingSchoolAI (Collaborated Component)
 * @description: Merges LoadingSchoolIcon with Kokonut AI Text Loading
 * @features: Glowing pinging School Logo + Cycling AI gradient sweep text
 */

import React, { useEffect, useState } from "react";
import { AnimatePresence, motion } from "motion/react";
import { cn } from "@/lib/utils";

interface LoadingSchoolAIProps {
  iconSrc?: string;
  texts?: string[];
  className?: string;
  interval?: number;
}

export const LoadingSchoolAI: React.FC<LoadingSchoolAIProps> = ({
  iconSrc = "/icon.png",
  texts = [
    "Memverifikasi Wajah...",
    "Menganalisis Biometrik AI...",
    "Mengecek Koordinat Presensi...",
    "Menghubungkan ke Server...",
    "Hampir Selesai...",
  ],
  className,
  interval = 1800,
}) => {
  const [currentTextIndex, setCurrentTextIndex] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentTextIndex((prevIndex) => (prevIndex + 1) % texts.length);
    }, interval);

    return () => clearInterval(timer);
  }, [interval, texts.length]);

  return (
    <div className={cn("flex flex-col items-center justify-center p-6 space-y-5", className)}>
      {/* 1. School Logo with Glowing Ping & Pulse */}
      <div className="relative flex items-center justify-center">
        <div className="absolute w-[68px] h-[68px] bg-teal-500/25 rounded-full animate-ping pointer-events-none" />
        <div className="absolute w-[84px] h-[84px] bg-emerald-400/15 rounded-full blur-md animate-pulse pointer-events-none" />
        <img
          src={iconSrc}
          alt="School Logo Loading"
          className="relative z-10 w-[60px] h-[60px] object-contain animate-pulse filter drop-shadow-md"
        />
      </div>

      {/* 2. Kokonut AI Text Loading with Shimmering Gradient */}
      <div className="relative w-full max-w-xs text-center px-4 py-2">
        <AnimatePresence mode="wait">
          <motion.div
            key={currentTextIndex}
            initial={{ opacity: 0, y: 12, filter: "blur(4px)" }}
            animate={{
              opacity: 1,
              y: 0,
              filter: "blur(0px)",
              backgroundPosition: ["200% center", "-200% center"],
            }}
            exit={{ opacity: 0, y: -12, filter: "blur(4px)" }}
            transition={{
              opacity: { duration: 0.3 },
              y: { duration: 0.3 },
              backgroundPosition: {
                duration: 2.5,
                ease: "linear",
                repeat: Number.POSITIVE_INFINITY,
              },
            }}
            className="flex justify-center whitespace-nowrap bg-[length:200%_100%] bg-gradient-to-r from-teal-400 via-emerald-200 to-teal-400 bg-clip-text font-bold text-base sm:text-lg text-transparent drop-shadow-sm tracking-wide"
          >
            {texts[currentTextIndex]}
          </motion.div>
        </AnimatePresence>

        <p className="text-[11px] text-slate-400/80 mt-1 font-medium tracking-wider uppercase">
          Sistem Presensi AI SMKN 1 Beringin
        </p>
      </div>
    </div>
  );
};

export default LoadingSchoolAI;
