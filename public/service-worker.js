const CACHE_NAME = 'meu-gestor-v2';

const STATIC_CACHE_FILES = [
    '/manifest.webmanifest',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/apple-touch-icon.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_CACHE_FILES))
    );

    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => cacheName !== CACHE_NAME)
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );

    self.clients.claim();
});

self.addEventListener('fetch', event => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(async () => {
                const offlinePage = await caches.match('/offline');

                return offlinePage ?? new Response(
                    'Sem conexão com o servidor.',
                    {
                        status: 503,
                        headers: {
                            'Content-Type': 'text/plain; charset=utf-8'
                        }
                    }
                );
            })
        );

        return;
    }

    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});