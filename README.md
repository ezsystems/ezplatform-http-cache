[![Build Status](https://img.shields.io/travis/ezsystems/ezplatform-http-cache.svg?style=flat-square&branch=master)](https://travis-ci.org/ezsystems/ezplatform-http-cache)
[![Downloads](https://img.shields.io/packagist/dt/ezsystems/ezplatform-http-cache.svg?style=flat-square)](https://packagist.org/packages/ezsystems/ezplatform-http-cache)
[![Latest release](https://img.shields.io/github/release/ezsystems/ezplatform-http-cache.svg?style=flat-square)](https://github.com/ezsystems/ezplatform-http-cache/releases)
[![License](https://img.shields.io/github/license/ezsystems/ezplatform-http-cache.svg?style=flat-square)](LICENSE)

# Ibexa HTTP Cache

Provides HTTP cache handling for [Ibexa DXP](https://www.ibexa.co/products) and Ibexa Open Source
(formerly eZ Platform), by default since version 1.12. It adds support for multi-tagging for Symfony
Proxy, Varnish _(using [xkey][Varnish-xkey])_. Support for Fastly is part of
the [Ibexa Cloud](https://www.ibexa.co/products/ibexa-cloud) offer.


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
Vary: X-User-Context-Hash
xkey: ez-all c1 ct1 l2 pl1 p1 p2
```

_If you have several repositories configured, the tags will be prefixed by
repository "index" on non default repository. E.g. "1p1", where repository is the second repository in the system (default repository has index 0 but does not have prefix)._

For further reading on tags see [docs/using_tags.md](docs/using_tags.md).

### Toggling of cache on ContentView responses
Responses from `/content/view` will be made cachable, and the shared max age will be set if it is enabled.

### Purging of tagged HTTP cache on Repository operations
A set of Slots will send HTTP PURGE requests for each cache tag affected by write operations. 


## Configuration

### Drivers

This bundle lets you configure drivers for handling HTTP cache. The following exists from eZ:
- `local`: extended Symfony Proxy to support tagging and varying by user rights _(available in this bundle)_
- `varnish`: Varnish proxy using and customizing FosHttpCache for purging _(available in this bundle)_
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
[ezplatform-kernel]: http://github.com/ezsystems/ezplatform-kernel
[Varnish-xkey]: https://github.com/varnish/varnish-modules/blob/master/docs/vmod_xkey.rst

## COPYRIGHT
Copyright (C) 1999-2021 Ibexa AS (formerly eZ Systems AS). All rights reserved.

## LICENSE
This source code is available separately under the following licenses:

A - Ibexa Business Use License Agreement (Ibexa BUL),
version 2.4 or later versions (as license terms may be updated from time to time)
Ibexa BUL is granted by having a valid Ibexa DXP (formerly eZ Platform Enterprise) subscription,
as described at: https://www.ibexa.co/product
For the full Ibexa BUL license text, please see:
https://www.ibexa.co/software-information/licenses-and-agreements (latest version applies)

AND

B - GNU General Public License, version 2
Grants an copyleft open source license with ABSOLUTELY NO WARRANTY. For the full GPL license text, please see:
https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
