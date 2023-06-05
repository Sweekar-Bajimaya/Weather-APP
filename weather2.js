const CACHE_NAME = "weather-app-cache-v1";
const urlsToCache = [
  "/",
  "./Weather_2329474_Sweekar_Bajimaya.html",
  "./SamiGautam_2329843_index.php",
  "./SamiGautam_2329843_connection.php",
  "./fonts.googleapis.com/css2?family=Poppins:wght@200&display=swap",
  "./source.unsplash.com/1600x900/?${data.name}",
  "./thunderstorm-3625405__480.jpg",
  "./service-worker.js",
  "https://api.openweathermap.org/data/2.5/weather?q=aberdeenshire&units=metric&appid=c2b776d187c2fb68394be2ffbebed7c6",
  "./openweathermap.org/img/wn/${data.weather[0].icon}.png",
];

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log("Opened cache");
        return cache.addAll(urlsToCache);
      })
      .catch((error) => {
        console.error('Failed to add resources to cache', error);
      })
  );
});
self.addEventListener("fetch", (event) => {
  console.log(`Fetching: ${event.request.url}`);
  
  event.respondWith(
    caches.match(event.request).then((response) => {
      if (response) {
        console.log(`Returning cached response for: ${event.request.url}`);
        return response;
      }

      console.log(`Cache miss. Fetching from network: ${event.request.url}`);
      return fetch(event.request).then((response) => {
        if (!response || response.status !== 200 || response.type !== "basic") {
          console.log(`Unable to fetch: ${event.request.url}`);
          return response;
        }

        console.log(`Caching response for: ${event.request.url}`);
        const responseToCache = response.clone();
        caches.open(CACHE_NAME).then((cache) => {
          cache.put(event.request, responseToCache);
        });

        return response;
      });
    })
  );
});
// For Html
if ("serviceWorker" in navigator) {
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("./service-worker.js").then((registration) => {
      console.log("Service worker registered with scope: ", registration.scope);
    });
  });
}

