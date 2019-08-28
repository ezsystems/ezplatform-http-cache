# FOSHttpCacheBundle usage in eZ

_This doc is aimed at core developers, and explains how this bundle integrates with FOSHttpCacheBundle internally._


eZ Platform HttpCache _(this bundle)_ uses [FOSHttpCacheBundle 1.x][fos] in the following way:
- When `local` purge type is set, the system uses a custom Symfony Proxy tag-aware data store, and hence does not
  use FOSHttpCacheBundle directly. However FOSHttpCache(Bundle) can still be used to manipulate responses, incl tagging.
- When `http` purge type is set, the system uses FOSHttpCache configured with varnish as proxy client.

As HttpCache in eZ Publish/Platform predates FOSHttpCache, we have own abstractions for:
 - purge clients: In this bundle exposed as `ezplatform.http_cache.purge_client` service
 - App Cache: `EzSystems\PlatformHttpCacheBundle\AppCache` app cache class to extend from, this in turn extends
   `FOS\HttpCacheBundle\SymfonyCache\EventDispatchingHttpCache`, however it provides own set of request-aware purgers and
   own handling of user context hash which for BC reasons is called `X-User-Hash` in eZ.
 - Since version 1.0, FOSHttpCache's `X-User-Context-Hash` header is used for user context hash.

_Note: Once FOSHttpCache(Bundle) has full support for tagging, a major version of this bundle might be be refactored to more
directly reuse FOSHttpCache(Bundle). However in the meantime our abstractions have allowed us to ship features across
Symfony Cache and Varnish such as full tagging support not possible with plain FOSHttpCacheBundle._

Some further differences on how HttpCache behaves on specific features in this bundle can be found below.

## Http cache clear
Varnish proxy client from FOSHttpCache lib is used for clearing cache when `http` purge client is configured..
It sends, for each cache tag that needs to be expired, a `PURGE` request with a `key` header to the registered purge servers.

For cache clearing to work properly, you need to use the VCL from the [ezplatform `doc/varnish` directory][varnish_doc].

## User context hash
[FOSHttpCacheBundle *User Context feature* is used][fos_user_context] is activated by default.

As the response can vary on a request header, the base solution is to make the kernel do a sub-request in order to retrieve
the context (the **user context hash**). Once the *user hash* has been retrieved, it is injected into the original request 
as the `X-User-Hash` header, making it possible to *vary* the HTTP response on this header:

> The name of the [user hash header is configurable in FOSHttpCacheBundle][fos_user_context]. 
> By default eZ Publish sets it to `**X-User-Hash**`.

```php
<?php
use Symfony\Component\HttpFoundation\Response;

// ...

// Inside a controller action
$response = new Response();
$response->setVary( 'X-User-Hash' );
```

This solution is [implemented in Symfony reverse proxy (aka *HttpCache*)][fos_symfony_cache] 
and is also accessible to [dedicated reverse proxies like Varnish][fos_varnish_cache].
 

### How it works
Please refer to [FOSHttpCacheBundle documentation on how user context feature works][fos_user_context#how].

### User hash generation
Please refer to [FOSHttpCacheBundle documentation on how user hashes are being generated][fos_user_context#hashes].

eZ Platform already interferes in the hash generation process, by adding current user permissions and limitations.
One can also interfere in this process by [implementing custom context provider(s)][fos_user_context#providers].

### Varnish VCL
While the described behavior comes out of the box with Symfony reverse proxy, Varnish is also supported. Using the documented
[eZ Platform Varnish VCL][_doc].


## Default options for FOSHttpCacheBundle defined in eZ
The following configuration is defined in eZ by default for FOSHttpCacheBundle.
You may override these settings, however for the configured headers changing them will break code both here as well as
in other parts of the ez ecosystem.

```yaml
fos_http_cache:
    proxy_client:
        # "varnish" is used, even when using Symfony HttpCache.
        default: varnish
        varnish:
            # Means http_cache.purge_servers defined for current SiteAccess.
            servers: [$http_cache.purge_servers$]
            
    user_context:
        enabled: true
        # User context hash is cached during 10min
        hash_cache_ttl: 600
        user_hash_header: X-User-Hash
    tags:
        header: xkey
```

[varnish_doc]: https://github.com/ezsystems/ezplatform/blob/master/doc/varnish
[fos]: http://foshttpcachebundle.readthedocs.org/
[fos_user_context]: http://foshttpcachebundle.readthedocs.io/en/1.3/features/user-context.html
[fos_user_context#how]: http://foshttpcachebundle.readthedocs.io/en/1.3/features/user-context.html#how-it-works
[fos_user_context#providers]: http://foshttpcachebundle.readthedocs.io/en/1.3/reference/configuration/user-context.html#custom-context-providers
[fos_user_context_hashes]: http://foshttpcachebundle.readthedocs.io/en/1.3/features/user-context.html#generating-hashes
[fos_symfony_cache]: http://foshttpcachebundle.readthedocs.io/en/1.3/features/symfony-http-cache.html
[fos_varnish_cache]: http://foshttpcache.readthedocs.io/en/1.4/varnish-configuration.html
