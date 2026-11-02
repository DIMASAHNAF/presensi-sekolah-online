module.exports = {
  apps: [{
    name: 'face-api',
    script: '/www/wwwroot/presensi.tiksmkn1beringin.my.id/python/venv/bin/uvicorn',
    // 2 workers optimal untuk 16-core: 2 × 8 = 16 thread pas, tidak over-subscribe
    args: 'face_api:app --host 127.0.0.1 --port 8005 --workers 2',
    cwd: '/www/wwwroot/presensi.tiksmkn1beringin.my.id/python',
    interpreter: '/www/wwwroot/presensi.tiksmkn1beringin.my.id/python/venv/bin/python',
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      PYTHONUNBUFFERED: "1",
      // Paksa OMP/MKL juga pakai thread count yang sama agar tidak konflik
      OMP_NUM_THREADS: "8",
      MKL_NUM_THREADS: "8",
      OPENBLAS_NUM_THREADS: "8"
    }
  }]
};
