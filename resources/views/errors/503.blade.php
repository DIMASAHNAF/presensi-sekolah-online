<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Maintenance — Presensi Sekolah</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #064e3b 50%, #022c22 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            overflow: hidden;
            position: relative;
        }

        /* Background decorative blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            pointer-events: none;
        }
        .blob-1 { width: 600px; height: 600px; background: #0d9488; top: -200px; right: -200px; animation: float 8s ease-in-out infinite; }
        .blob-2 { width: 400px; height: 400px; background: #10b981; bottom: -150px; left: -150px; animation: float 10s ease-in-out infinite reverse; }
        .blob-3 { width: 300px; height: 300px; background: #34d399; top: 40%; left: 10%; animation: float 6s ease-in-out infinite 2s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        /* Main card */
        .card {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 3rem 2.5rem;
            max-width: 560px;
            width: 90%;
        }

        /* Gear animation */
        .gear-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 2.5rem;
        }
        .gear-icon {
            font-size: 80px;
            color: #2dd4bf;
            display: block;
            animation: gear-spin 4s linear infinite;
            filter: drop-shadow(0 0 20px rgba(45,212,191,0.5));
        }
        .gear-icon-small {
            position: absolute;
            bottom: -5px;
            right: -10px;
            font-size: 44px;
            color: #34d399;
            animation: gear-spin 2.5s linear infinite reverse;
            filter: drop-shadow(0 0 12px rgba(52,211,153,0.5));
        }
        @keyframes gear-spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        .error-code {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #2dd4bf;
            background: rgba(45,212,191,0.1);
            border: 1px solid rgba(45,212,191,0.2);
            border-radius: 100px;
            padding: 0.35rem 1.25rem;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        h1 {
            font-size: 2.25rem;
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff, #99f6e4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        p.desc {
            font-size: 1rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.75;
            max-width: 400px;
            margin: 0 auto 2rem;
        }

        /* Progress bar */
        .progress-bar-wrap {
            background: rgba(255,255,255,0.08);
            border-radius: 100px;
            height: 6px;
            margin: 2rem auto;
            max-width: 320px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            border-radius: 100px;
            background: linear-gradient(90deg, #0d9488, #10b981, #34d399);
            background-size: 200% 100%;
            animation: progress-shimmer 2s linear infinite;
        }
        @keyframes progress-shimmer {
            0%   { background-position: 100% 0; width: 30%; }
            50%  { background-position: 0% 0;   width: 80%; }
            100% { background-position: 100% 0; width: 30%; }
        }

        /* Info chips */
        .chips {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }
        .chip {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.75);
            backdrop-filter: blur(8px);
        }
        .chip i { color: #2dd4bf; }

        /* Countdown */
        #countdown-wrap { color: rgba(255,255,255,0.5); font-size:0.8rem; margin-top:0.5rem; }

        footer {
            position: absolute;
            bottom: 2rem;
            left: 0; right: 0;
            text-align: center;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.3);
            z-index: 10;
        }
        footer span { color: rgba(255,255,255,0.55); font-weight: 600; }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="card">
        {{-- Animated gears --}}
        <div class="gear-wrapper">
            <i class="fas fa-gear gear-icon"></i>
            <i class="fas fa-gear gear-icon-small"></i>
        </div>

        <div class="error-code">503 — Maintenance Mode</div>

        <h1>Sistem Sedang<br>Dipelihara</h1>

        <p class="desc">
            Sistem Presensi Sekolah sedang dalam proses pemeliharaan dan peningkatan.
            Kami akan kembali secepatnya. Mohon tunggu sebentar.
        </p>

        {{-- Animated progress --}}
        <div class="progress-bar-wrap">
            <div class="progress-bar"></div>
        </div>

        {{-- Info chips --}}
        <div class="chips">
            <div class="chip">
                <i class="fas fa-clock"></i>
                <span>Estimasi: 10-30 Menit</span>
            </div>
        </div>

        <div id="countdown-wrap">
            <i class="fas fa-rotate-right"></i> Auto-refresh dalam <span id="timer">30</span> detik
        </div>
        
        {{-- Contact Info Alert --}}
        <div style="margin-top: 1.5rem; background: rgba(20, 184, 166, 0.15); border: 1px solid rgba(20, 184, 166, 0.3); padding: 1rem; border-radius: 12px; font-size: 0.875rem; text-align: left; display: flex; align-items: flex-start; gap: 0.75rem;">
            <i class="fas fa-circle-info" style="font-size: 1.25rem; color: #2dd4bf; margin-top: 0.125rem;"></i>
            <div>
                <strong style="display: block; margin-bottom: 0.25rem; font-weight: 600; color: #ffffff;">Informasi Lebih Lanjut</strong>
                <span style="color: rgba(255,255,255,0.85);">Silakan hubungi administrator: <strong style="color: #ffffff;">+62 822-6116-8543</strong></span>
            </div>
        </div>
    </div>

    <footer>
        Sistem Presensi Online &copy; 2026 SMKN1 BERINGIN
    </footer>

    <script>
        // Auto-refresh countdown
        let seconds = 30;
        const timerEl = document.getElementById('timer');
        setInterval(() => {
            seconds--;
            if (timerEl) timerEl.textContent = seconds;
            if (seconds <= 0) {
                window.location.reload();
            }
        }, 1000);
    </script>
</body>
</html>
