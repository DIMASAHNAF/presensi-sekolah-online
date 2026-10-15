// Service Worker for Presensi Sekolah PWA
const CACHE_NAME = 'presensi-cache-v15';
const STATIC_ASSETS = [
    '/manifest.json',
    '/images/logo.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
];

// Install Event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('Gagal cache sebagian asset:', err);
            });
        })
    );
    self.skipWaiting();
});

// Activate Event - Segera bersihkan seluruh cache lama
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch Event - Dynamic pages are NEVER cached in Service Worker
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    const url = new URL(event.request.url);

    // Dynamic routes: ALWAYS direct network fetch
    if (
        url.pathname.startsWith('/siswa') ||
        url.pathname.startsWith('/kiosk') ||
        url.pathname.startsWith('/dashboard') ||
        url.pathname.startsWith('/login') ||
        url.pathname.includes('/live') ||
        url.pathname.includes('/sesi-aktif')
    ) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Only cache successful static asset responses
                if (response && response.status === 200 && response.type === 'basic') {
                    const ext = url.pathname.split('.').pop().toLowerCase();
                    if (['png', 'jpg', 'jpeg', 'webp', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'json'].includes(ext)) {
                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                }
                return response;
            })
            .catch(async () => {
                const cached = await caches.match(event.request);
                if (cached) return cached;
                return new Response('Sistem Presensi: Jaringan bermasalah atau offline.', {
                    status: 503,
                    headers: { 'Content-Type': 'text/plain; charset=utf-8' }
                });
            })
    );
});
