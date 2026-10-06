module.exports = {
  apps: [{
    name: 'face-api',
    script: '/www/wwwroot/presensi.tiksmkn1beringin.my.id/python/venv/bin/uvicorn',
    args: 'face_api:app --host 127.0.0.1 --port 8005 --workers 4',
    cwd: '/www/wwwroot/presensi.tiksmkn1beringin.my.id/python',
    interpreter: '/www/wwwroot/presensi.tiksmkn1beringin.my.id/python/venv/bin/python',
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      PYTHONUNBUFFERED: "1"
    }
  }]
};
