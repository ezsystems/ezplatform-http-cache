[![Build Status](https://travis-ci.org/ezsystems/ezplatform-http-cache.svg?branch=master)](https://travis-ci.org/ezsystems/ezplatform-http-cache)

# platform-http-cache

Experimental HTTP cache handling for [eZ Platform][ezplatform].

This package aims at externalizing the HTTP cache handling of [ezpublish-kernel][ezpublish-kernel].
It is by default installed with ezplatform 1.8, but is not enabled in the `AppKernel`, as it is experimental.

## Enabling the package
Add the package to `app/AppKernel.php`, *before* the EzPublishCoreBundle declaration:

```php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),
            new EzSystems\PlatformHttpCacheBundle\EzSystemsPlatformHttpCacheBundle();
            new eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle(),
            // ...
        );
```

The package will replace the services from the kernel, thus enabling the new features, such as multi-tagging.

The application cache class needs to be customized. If you haven't changed the `AppCache` class, you can do so
by setting the `SYMFONY_HTTP_CACHE_CLASS` environment variable:

    export SYMFONY_HTTP_CACHE_CLASS='EzSystems\PlatformHttpCacheBundle\AppCache'

Do not forget to restart your web server.

Alternatively, if you use your own `AppCache` class, you will have to make it to extend from this class instead.

## Features

### `xkey` header on ContentView responses
Responses from `/content/view` will be tagged based on their contents:

```
curl -i -X HEAD 'http://localhost:8000/'

HTTP/1.1 200 OK
Host: localhost:8000
Connection: close
Cache-Control: public, s-maxage=60
Content-Type: text/html; charset=UTF-8
Vary: Cookie
Vary: Authorization
xkey: content-1
xkey: content-type-1
xkey: location-2
xkey: parent-1
xkey: path-1
xkey: path-2
```

### Toggling of cache on ContentView responses
Responses from `/content/view` will be made cachable, and the shared max age will be set if it is enabled.

### Purging of tagged HTTP cache on Repository operations
A set of Slots will send HTTP PURGE requests for each cache tag affected by write operations. 

[ezplatform]: http://github.com/ezsystems/ezplatform
[ezpublish-kernel]: http://github.com/ezsystems/ezpubish-kernel
