const CACHE_NAME = 'sibem-cache-v2';
const OFFLINE_URL = '/offline.html';

const ASSETS_TO_CACHE = [
    OFFLINE_URL,
    '/assets/images/favicon.svg',
    '/assets/images/icon-192x192.png',
    '/assets/images/icon-512x512.png',
    '/assets/images/logo_white.png',
    '/assets/css/style.css',
    '/assets/css/style-preset.css',
    '/assets/css/ccb-theme.css'
];

// Install Event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[Service Worker] Caching offline page and assets');
            return cache.addAll(ASSETS_TO_CACHE);
        }).then(() => self.skipWaiting())
    );
});

// Activate Event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    // Handle HTML page navigation requests
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    console.log('[Service Worker] Fetch failed; returning offline page.');
                    return caches.match(OFFLINE_URL);
                })
        );
        return;
    }

    // For other assets (CSS, JS, Fonts, Images), use Stale-While-Revalidate strategy
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                // Fetch in background to update cache
                fetch(event.request).then((networkResponse) => {
                    if (networkResponse.status === 200) {
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, networkResponse);
                        });
                    }
                }).catch(() => { /* Ignore background fetch failures */ });
                
                return cachedResponse;
            }

            return fetch(event.request);
        })
    );
});
