[![Build Status](https://img.shields.io/travis/ezsystems/ezplatform-http-cache.svg?style=flat-square&branch=master)](https://travis-ci.org/ezsystems/ezplatform-http-cache)
[![Downloads](https://img.shields.io/packagist/dt/ezsystems/ezplatform-http-cache.svg?style=flat-square)](https://packagist.org/packages/ezsystems/ezplatform-http-cache)
[![Latest release](https://img.shields.io/github/release/ezsystems/ezplatform-http-cache.svg?style=flat-square)](https://github.com/ezsystems/ezplatform-http-cache/releases)
[![License](https://img.shields.io/packagist/l/ezsystems/ezplatform-http-cache.svg?style=flat-square)](LICENSE)

# platform-http-cache

Default HTTP cache handling for [eZ Platform][ezplatform].

This package externalizes the HTTP cache handling of [ezpublish-kernel][ezpublish-kernel].
It is by default installed with ezplatform 1.8, and has been enabled in the `AppKernel` from 1.12.

## Enabling the package
Add the package to `app/AppKernel.php`, *before* the EzPublishCoreBundle declaration:

```php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),
            new EzSystems\PlatformHttpCacheBundle\EzSystemsPlatformHttpCacheBundle(),
            new eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle(),
            // ...
        );
```

The package will replace the services from the kernel, thus enabling the new features, such as multi-tagging.

The application cache class needs to be customized. If you haven't changed the `AppCache` class, you can do so
by setting the `SYMFONY_HTTP_CACHE_CLASS` environment variable for your PHP or web server user.
If you use your own `AppCache` class, you will have to make it to extend from this class instead
of from the CoreBundle's.

For PHP's internal server you can set it as shell environment variable before starting server:

    export SYMFONY_HTTP_CACHE_CLASS='EzSystems\PlatformHttpCacheBundle\AppCache'

For Apache, with the default eZ Platform virtual host definition, uncomment the `SetEnv` lines for the two
variables above in your virtualhost, and set the values accordingly:

    SetEnv SYMFONY_HTTP_CACHE_CLASS 'EzSystems\PlatformHttpCacheBundle\AppCache'

For Nginx, set the variables using `fastcgi_param`:

    fastcgi_param SYMFONY_HTTP_CACHE_CLASS "EzSystems\PlatformHttpCacheBundle\AppCache";
    
Do not forget to restart your web server.

## Usage with Varnish

For usage with Varnish see the dedicated document in [docs/varnish](docs/varnish/varnish.md)


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
