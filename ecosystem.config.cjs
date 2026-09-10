module.exports = {
  apps: [{
    name: 'face-api',
    script: 'uvicorn',
    args: 'face_api:app --host 127.0.0.1 --port 8000',
    cwd: '/var/www/presensi-sekolah/python',
    interpreter: 'none',
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      PYTHONUNBUFFERED: "1"
    }
  }]
};
