const CACHE_NAME = "ieccp-v1";
const ASSETS = [
  "/",
  "/index.html",
  "/styles/global.css",
  "/styles/home.css",
  "/styles/header.css",
  "/styles/agenda.css",
  "/styles/gallery.css",
  "/scripts/conteudo.js",
  "/scripts/menu.js",
  "/scripts/popup.js",
  "/scripts/tema.js",
  "/img/logo.png",
  "/img/icon-192.png",
];

// Instalação (Cache dos arquivos principais)
self.addEventListener("install", (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS);
    }),
  );
});

// Busca (Se tiver offline, tenta pegar do cache)
self.addEventListener("fetch", (e) => {
  e.respondWith(fetch(e.request).catch(() => caches.match(e.request)));
});
