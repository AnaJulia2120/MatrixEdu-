/* Service worker do MatrixEdu — permite instalar como app e abrir mesmo sem internet. */
const CACHE_NAME = 'matrixedu-cache-v1';
const APP_SHELL = [
  './matrixedu.html',
  './manifest.json',
  './icons/icon-192.png',
  './icons/icon-512.png',
  './icons/icon-512-maskable.png'
];

self.addEventListener('install', function(event){
  event.waitUntil(
    caches.open(CACHE_NAME).then(function(cache){
      return cache.addAll(APP_SHELL);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', function(event){
  event.waitUntil(
    caches.keys().then(function(nomes){
      return Promise.all(
        nomes.filter(function(n){ return n !== CACHE_NAME; })
             .map(function(n){ return caches.delete(n); })
      );
    })
  );
  self.clients.claim();
});

/* Estratégia: tenta a rede primeiro (pra pegar sempre a versão mais nova); se falhar (offline), usa o cache. */
self.addEventListener('fetch', function(event){
  if(event.request.method !== 'GET') return;
  event.respondWith(
    fetch(event.request)
      .then(function(resposta){
        var copia = resposta.clone();
        caches.open(CACHE_NAME).then(function(cache){ cache.put(event.request, copia); });
        return resposta;
      })
      .catch(function(){
        return caches.match(event.request).then(function(match){
          return match || caches.match('./matrixedu.html');
        });
      })
  );
});
