var localhosts = ["localhost", "127.0.0.1"];
var hostname = location.hostname;

var IS_LOCAL = false;
localhosts.forEach(function(host) {
    if (origin.indexOf(host) !== -1) {
        IS_LOCAL = true;
    }
});

var IS_PROD = !IS_LOCAL;

function getUrls(urlArray) {
    var prefix = IS_PROD ? '/sys' : '';
    return urlArray.map(function(url){
      return prefix + url;
    });
}

var base = "koklumezs-";
var version = "1.0.4";
var cacheName = base + version;

var urlsForCachingStrategies = { 
    cacheOnly: getUrls([
        "/js/select2.js",
        "/js/custom.js",
        "/js/Youtube.min.js",
        "/css/site.css",
        "/css/select2.css",
        "/icon192.png",
        "/icon512.png"
    ]),
    cacheFirst: [
      "/assets/",
      "/css/",
      "/js/",
      "/icon192.png",
      "/icon512.png"
    ],
    staleWhileRevalidate: [
        // "/files/",
    ]
}

function useCacheFirst(url){
    return useCacheStrategy(url, urlsForCachingStrategies.cacheFirst);
}
function useStaleWhileRevalidate(url){
    return useCacheStrategy(url, urlsForCachingStrategies.staleWhileRevalidate);
}

function useCacheStrategy(url, fragments){
    var res = false;

    fragments.every(function(fragment){
        res = url.indexOf(fragment) !== -1;
        return !res;
    });

    return res;
}

self.addEventListener('install', function(e) {
  e.waitUntil(precache());
});

function precache(){
    return caches.open(cacheName).then(function(cache) {
        return cache.addAll(urlsForCachingStrategies.cacheOnly).then(() => self.skipWaiting());
    });
}

self.addEventListener('activate', function(event) {
    clearOldCache(event);
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', function(event) {
    event.respondWith(respond(event.request));
});

function respond(request) {
    if(useCacheFirst(request.url)){
        //console.log("cache first", request.url);
        return cacheFirst(request);
    } else if(useStaleWhileRevalidate(request.url)){
        //console.log("stale while revalidate", request.url);
        return staleWhileRevalidate(request);
    } else {
        //console.log("network first", request.url);
        return networkFirst(request);
    }
}

function cacheFirst(request){
    return caches.open(cacheName)
        .then(function(cache) {
            return cache.match(request)
                .then(function(cacheResponse) {
                    if(cacheResponse) return cacheResponse;
                    else return fetch(request)
                        .then(function(networkResponse) {
                            cache.put(request, networkResponse.clone())
                            return networkResponse;
                        }).catch(function(){
                            return fetch(request);
                        });
                });
        });
}

function networkFirst(request){
    return fetch(request)
        .then(function(response) {
            if(request.ok) cache.put(request, response);
            return response;
        })
        .catch(function() {
            return caches.match(request);
        });
}

function staleWhileRevalidate(request){
    return caches.open(cacheName)
        .then(function(cache) {
            return cache.match(request)
                .then(function(cacheResponse) {
                    fetch(request)
                        .then(function(networkResponse) {
                            cache.put(request, networkResponse);
                            return networkResponse;
                        })

                        if(cacheResponse) return cacheResponse;
                });
        })
}

function clearOldCache(event){
    event.waitUntil(
        caches.keys().then((keyList) => {
        return Promise.all(keyList.map((key) => {
            if (key !== cacheName) {
                console.log("deleting: ", key, cacheName);
                return caches.delete(key);
            }
        }));
        })
    );
}

function cleanResponse(response) {
  const clonedResponse = response.clone();

  // Not all browsers support the Response.body stream, so fall back to reading
  // the entire body into memory as a blob.
  const bodyPromise = 'body' in clonedResponse ?
    Promise.resolve(clonedResponse.body) :
    clonedResponse.blob();

  return bodyPromise.then((body) => {
    // new Response() is happy when passed either a stream or a Blob.
    return new Response(body, {
      headers: clonedResponse.headers,
      status: clonedResponse.status,
      statusText: clonedResponse.statusText,
    });
  });
}