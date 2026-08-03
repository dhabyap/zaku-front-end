const CACHE = 'zaku-v1';
const ASSETS = [
  '/',
  '/login',
  '/dashboard',
  '/build/assets/app.css',
  '/build/assets/app.js',
];

self.addEventListener('install', (e) => {
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(clients.claim());
});

self.addEventListener('fetch', (e) => {
  e.respondWith(
    fetch(e.request).catch(() => caches.match(e.request))
  );
});
