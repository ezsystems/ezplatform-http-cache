# Driver support

You may add integration with other http caches using the extension points provided by this bundle.

The following extension points are available
 - PurgeClient
 - TagHandler
 - FOS TagHandler

If you write a new PurgeClient driver, you **must** also create a corresponding TagHandler and vice
versa. Creating a FOS TagHandler is optional.


## PurgeClient

The PurgeClient is responsible for sending purge requests to the http cache when content is about to be invalidated.
The PurgeClient must implement EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface and can be registered
with the following code in services.yml:

```
services:
    ezplatform.http_cache_myhttpcachebundle.purge_client.myhttpcache:
        class: EzSystems\PlatformMyHttpCacheBundle\PurgeClient\MyHttpCachePurgeClient
        arguments: ['@ezplatform.http_cache.cache_manager']
        tags:
            - {name: ezplatform.http_cache.purge_client, purge_type: myhttpcache}
```

Any service which implements the PurgeClientInterface must be tagged with `ezplatform.http_cache.purge_client` in
order to be registered as such.

`purge_type` specifies what the value of the `ezpublish.http_cache.purge_type` setting in `app/config/ezplatform.yml`
should be in order to enable this driver. By default this is set using `%purge_type%` parameter, and can be set in `app/config/parameters.yml` like so:

```
parameters:
    purge_type: myhttpcache
```


## TagHandler

The TagHandler is responsible for tagging responses with headers which the http cache recognizes.
The TagHandler must implement EzSystems\PlatformHttpCacheBundle\Handler\TagHandlerInterface and can be registered with
the following code in services.yml:

```
    ezplatform.http_cache_myhttpcachebundle.tag_handler.myhttpcache:
        class: EzSystems\PlatformMyHttpCacheBundle\Handler\MyHttpCacheTagHandler
        tags:
            - {name: ezplatform.http_cache.tag_handler, purge_type: myhttpcache}

```

Any service which implements the TagHandlerInterface must be tagged with `ezplatform.http_cache.tag_handler` in order
to be registered as such.

## FOS TagHandler

The FOS Http cache bundle also has a TagHandler which is not used by eZ Platform except for one thing, the
`fos:httpcache:invalidate:tag` command. With this command you may explicitly invalidate cache by tag.

Normally, you would not need to implement your own FOS TagHandler as the ezplatform-http-cache bundle ships with a
default one which uses the PurgeClient to invalidate the given tags.
If you need to write your own FOS TagHandler anyway, you may register it with the following code in services.yml:

```
    ezplatform.http_cache_myhttpcachebundle.fos_tag_handler.myhttpcache:
        class: EzSystems\PlatformMyHttpCacheBundle\Handler\MyHttpCacheFosTagHandler
        tags:
            - {name: ezplatform.http_cache.fos_tag_handler, purge_type: myhttpcache}
```
