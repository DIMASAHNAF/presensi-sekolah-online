// Service Worker for Presensi Sekolah PWA
const CACHE_NAME = 'presensi-cache-v1';
const STATIC_ASSETS = [
    '/',
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

// Activate Event
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

// Fetch Event - Network First with Cache Fallback for dynamic app
self.addEventListener('fetch', (event) => {
    // Abaikan permintaan non-GET atau API / sesi polling / POST
    if (event.request.method !== 'GET') return;
    const url = new URL(event.request.url);

    // Skip dynamic API calls and socket/polling
    if (url.pathname.includes('/live') || url.pathname.includes('/sesi-aktif')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Clone and update cache if ok
                if (response && response.status === 200 && response.type === 'basic') {
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request);
            })
    );
});
