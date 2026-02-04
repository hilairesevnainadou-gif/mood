// Service Worker optimisé pour BHDM - Version corrigée
const CACHE_NAME = 'bhdm-client-v2.0.2';
const API_CACHE_NAME = 'bhdm-api-v1';

// URLs à mettre en cache (ressources critiques uniquement)
const urlsToCache = [
  '/',
  '/connexion',
  '/inscription',
  '/client/dashboard',
  '/client/wallet',
  '/client/documents',
  '/offline',
  '/css/client.css',
  '/js/app.js',
  '/images/logo.png',
  '/images/icons/icon-192.png',
  '/images/icons/icon-512.png'
];

// Fonction pour vérifier si l'URL existe
const checkUrlExists = async (url) => {
  try {
    const response = await fetch(url, { method: 'HEAD' });
    return response.ok;
  } catch {
    return false;
  }
};

// Installation du Service Worker
self.addEventListener('install', event => {
  console.log('[Service Worker] Installation en cours...');

  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('[Service Worker] Mise en cache des ressources critiques');
        return Promise.allSettled(
          urlsToCache.map(url => {
            return cache.add(url).catch(error => {
              console.warn(`[Service Worker] Échec du cache pour ${url}:`, error);
              return Promise.resolve();
            });
          })
        );
      })
      .then(() => {
        console.log('[Service Worker] Installation terminée');
        return self.skipWaiting();
      })
      .catch(error => {
        console.error('[Service Worker] Erreur critique lors de l\'installation:', error);
      })
  );
});

// Activation du Service Worker
self.addEventListener('activate', event => {
  console.log('[Service Worker] Activation en cours...');

  const cacheWhitelist = [CACHE_NAME, API_CACHE_NAME];

  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (!cacheWhitelist.includes(cacheName)) {
            console.log(`[Service Worker] Nettoyage de l'ancien cache: ${cacheName}`);
            return caches.delete(cacheName);
          }
        })
      );
    })
    .then(() => {
      return self.clients.claim();
    })
    .then(() => {
      console.log('[Service Worker] Prêt à intercepter les requêtes');
      // Annoncer qu'une nouvelle version est disponible
      self.clients.matchAll().then(clients => {
        clients.forEach(client => {
          client.postMessage({
            type: 'SERVICE_WORKER_READY',
            data: { version: '2.0.2', timestamp: Date.now() }
          });
        });
      });
    })
    .catch(error => {
      console.error('[Service Worker] Erreur lors de l\'activation:', error);
    })
  );
});

// Stratégie pour les API
const networkFirstWithTimeout = async (request) => {
  const timeout = 5000;

  try {
    const fetchPromise = fetch(request);
    const timeoutPromise = new Promise((_, reject) => {
      setTimeout(() => reject(new Error('Timeout')), timeout);
    });

    const networkResponse = await Promise.race([fetchPromise, timeoutPromise]);

    if (networkResponse && networkResponse.ok) {
      const cache = await caches.open(API_CACHE_NAME);
      cache.put(request, networkResponse.clone()).catch(() => {});
    }

    return networkResponse;
  } catch (error) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      const headers = new Headers(cachedResponse.headers);
      headers.set('X-Cache', 'HIT');

      return new Response(cachedResponse.body, {
        status: cachedResponse.status,
        statusText: cachedResponse.statusText,
        headers: headers
      });
    }

    return new Response(JSON.stringify({
      error: 'hors_ligne',
      message: 'Connectez-vous à Internet pour accéder à cette ressource',
      timestamp: new Date().toISOString()
    }), {
      status: 503,
      headers: {
        'Content-Type': 'application/json',
        'Cache-Control': 'no-store'
      }
    });
  }
};

// Stratégie pour les assets
const cacheFirstWithFallback = async (request) => {
  try {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }

    const networkResponse = await fetch(request);

    if (networkResponse.ok) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, networkResponse.clone()).catch(() => {});
    }

    return networkResponse;
  } catch (error) {
    if (request.url.match(/\.(png|jpg|jpeg|gif|svg|ico)$/)) {
      return caches.match('/images/logo.png');
    }

    throw error;
  }
};

// Interception des requêtes
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Ignorer les requêtes non-GET
  if (request.method !== 'GET') {
    return;
  }

  // Ignorer certaines requêtes
  if (url.protocol === 'chrome-extension:' ||
      url.protocol === 'blob:' ||
      url.hostname.includes('localhost') ||
      url.hostname.includes('127.0.0.1')) {
    return;
  }

  // Traiter les requêtes
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(networkFirstWithTimeout(request));
  } else if (url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i)) {
    event.respondWith(cacheFirstWithFallback(request));
  } else {
    // Pour les pages HTML, essayer le réseau d'abord
    event.respondWith(
      fetch(request)
        .then(response => {
          if (response.ok) {
            const cache = caches.open(CACHE_NAME);
            cache.then(c => c.put(request, response.clone()));
          }
          return response;
        })
        .catch(() => {
          return caches.match(request) || caches.match('/offline');
        })
    );
  }
});

// Gestion des messages
self.addEventListener('message', event => {
  const { type, data } = event.data || {};

  switch (type) {
    case 'SKIP_WAITING':
      self.skipWaiting();
      break;

    case 'CHECK_UPDATE':
      // Vérifier les mises à jour
      self.registration.update();
      break;

    case 'PURGE_CACHE':
      // Purger le cache
      caches.keys().then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            return caches.delete(cacheName);
          })
        );
      });
      break;
  }
});

// Fonction simplifiée pour vérifier la connexion
const checkNetworkStatus = async () => {
  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 3000);

    // Vérifier une ressource qui existe toujours
    const response = await fetch('/css/app.css', {
      method: 'HEAD',
      signal: controller.signal,
      cache: 'no-store'
    });

    clearTimeout(timeoutId);
    return response.ok;
  } catch {
    return false;
  }
};

// Mettre à jour le statut réseau périodiquement (seulement si client actif)
setInterval(async () => {
  try {
    const clients = await self.clients.matchAll();
    if (clients.length === 0) return;

    const isHealthy = await checkNetworkStatus();

    clients.forEach(client => {
      client.postMessage({
        type: 'NETWORK_STATUS',
        data: {
          online: isHealthy,
          timestamp: Date.now(),
          message: isHealthy ? 'Connecté' : 'Hors ligne'
        }
      });
    });
  } catch (error) {
    // Ignorer les erreurs silencieusement
  }
}, 60000); // Vérifier toutes les minutes

console.log('[Service Worker] Version 2.0.2 chargée et prête');
