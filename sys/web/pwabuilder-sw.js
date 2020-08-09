// This is the "Offline copy of assets" service worker

// const CACHE = "pwabuilder-offline";

// importScripts('https://storage.googleapis.com/workbox-cdn/releases/5.0.0/workbox-sw.js');

self.addEventListener("message", (event) => {
    if (event.data && event.data.type === "SKIP_WAITING") {
        self.skipWaiting();
    }
});

let deferredPrompt;

self.addEventListener('beforeinstallprompt', (e) => {
    // Prevent the mini-infobar from appearing on mobile
    e.preventDefault();
    // Stash the event so it can be triggered later.
    deferredPrompt = e;
    // Update UI notify the user they can install the PWA
    showInstallPromotion();
    deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
            console.log('User accepted the install prompt');
        } else {
            console.log('User dismissed the install prompt');
        }
    });
});

self.addEventListener('install', function (e) {    
    e.waitUntil(
        caches.open('airhorner').then(function (cache) {
            return cache.addAll([
                '/css/site.css',
                '/css/select2.css'
            ]);
        })
    );
    caches.open('airhorner').then(data => console.log(data));
});

self.addEventListener('fetch', function (event) {
    console.log(event.request.url);

    event.respondWith(
        caches.match(event.request).then(function (response) {
            return response || fetch(event.request);
        })
    );
});

// workbox.routing.registerRoute(
//     new RegExp('/*'),
//     new workbox.strategies.StaleWhileRevalidate({
//         cacheName: CACHE
//     })
// );