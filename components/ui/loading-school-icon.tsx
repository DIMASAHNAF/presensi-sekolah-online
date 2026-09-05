import React from "react";

const LoadingSchoolIcon: React.FC = () => {
  return (
    <div className="flex items-center justify-center h-full w-full p-4">
      <div className="relative flex items-center justify-center">
        {/* Efek glowing di belakang logo */}
        <div className="absolute w-[60px] h-[60px] bg-primary/20 rounded-full animate-ping"></div>
        
        {/* Logo sekolah (icon.png) */}
        <img
          src="/icon.png"
          alt="School Logo Loading"
          className="relative z-10 w-[60px] h-[60px] object-contain animate-pulse"
        />
      </div>
    </div>
  );
};

export default LoadingSchoolIcon;
