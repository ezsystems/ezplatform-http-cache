[![Build Status](https://img.shields.io/travis/ezsystems/ezplatform-http-cache.svg?style=flat-square&branch=master)](https://travis-ci.org/ezsystems/ezplatform-http-cache)
[![Downloads](https://img.shields.io/packagist/dt/ezsystems/ezplatform-http-cache.svg?style=flat-square)](https://packagist.org/packages/ezsystems/ezplatform-http-cache)
[![Latest release](https://img.shields.io/github/release/ezsystems/ezplatform-http-cache.svg?style=flat-square)](https://github.com/ezsystems/ezplatform-http-cache/releases)
[![License](https://img.shields.io/github/license/ezsystems/ezplatform-http-cache.svg?style=flat-square)](LICENSE)

# platform-http-cache

Provides HTTP cache handling for [eZ Platform][ezplatform], by default since version 1.12. From [ezpublish-kernel](ezpublish-kernel),
it adds support for mult-tagging for Symfony Proxy, Varnish _(using [xkey][Varnish-xkey])_. Support for Fastly is part of the
eZ Platform Cloud Enterprise offer as of 1.13 LTS.


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
Vary: X-User-Hash
xkey: content-1 content-type-1 location-2 parent-1 path-1 path-2 ez-all
```

_As of v0.7, if you have several repositories configured, the tags will be prefixed by
repository name on non default repository. E.g. "intranet_path-1"._

For further reading on tags see [docs/using_tags.md](docs/using_tags.md).

### Toggling of cache on ContentView responses
Responses from `/content/view` will be made cachable, and the shared max age will be set if it is enabled.

### Purging of tagged HTTP cache on Repository operations
A set of Slots will send HTTP PURGE requests for each cache tag affected by write operations. 


## Configuration

### Drivers

This bundle lets you configure drivers for handling HTTP cache. The following exists from eZ:
- `local`: extended Symfony Proxy to support tagging and varying by user rights _(available in this bundle)_
- `http` aka `varnish`: Varnish proxy using and customizing FosHttpCache for purging _(available in this bundle)_
- `fastly`: Fastly CDN proxy _(available with eZ Platform Enterprise and documented separately)_


Configuring these is done using global `ezpublish.http_cache.purge_type` config. By default it is set to use
`%purge_type%` parameter, and can be set in `app/config/parameters.yml` like so:

```
parameters:
    purge_type: varnish
```

For further reading on drivers see [docs/drivers.md](docs/drivers.md).


### Tags


For further reading on tags see [docs/using_tags.md](docs/using_tags.md).


[ezplatform]: http://github.com/ezsystems/ezplatform
[ezpublish-kernel]: http://github.com/ezsystems/ezpubish-kernel
[Varnish-xkey]: https://github.com/varnish/varnish-modules/blob/master/docs/vmod_xkey.rst
