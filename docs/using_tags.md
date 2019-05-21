# Using Tags

Understanding tags is the key to making the most of `ezplatform-http-cache`.

They work in a similar way as [persistence cache tags in eZ Platform v2](https://github.com/ezsystems/ezpublish-kernel/tree/7.0/doc/specifications/cache/persistence):
- A set of secondary keys set on every cache item, on top of "primary key" which in this case is the URI
- Like an index in a database it is typically used for anything relevant that represent the given cache item
- Used for cache invalidation

It works across all supported proxies _(see ["drivers"](drivers.md))_ by eZ Platform:
- Symfony Proxy _(PHP based for single server usage, primarily for smaller web sites)_
- [Varnish](https://varnish-cache.org/) with [xkey module (0.10.2+)](https://github.com/varnish/varnish-modules) _or_ [Varnish Plus](https://www.varnish-software.com/products/varnish-plus/) _(High performance reverse proxy)_
- [Fastly](https://www.fastly.com/) _(High performance reverse proxy, originally based on Varnish, worldwide as a CDN, driver available in eZ Platform Enterprise)_

_In order to support several repositories on one installation, tags will be prefixed by
repository name on non default repositories. E.g. "intranet_path-1"._

Varnish or Fastly are highly recommended for medium to large traffic needs. Besides being able to handle much more traffic, supported for use in cluster setup, they also both support soft purge _(by tags)_, meaning they are able to serve stale content while it's refreshed in the background on-demand, leading to more stable load on your backend.

## Tags in use in this bundle

### Tags for Content responses

- `content-<content-id>` :
  _Used on anything that is affected by changes to content, on content itself as well as location and so on._

- `content-versions-<content-id>` :
  _Used for clearing cache for content version list views, when not affecting the published content._

- `content-type-<content-type-id>` :
  _For use when content type changes affecting content of its type._

- `location-<location-id>` :
  _Used for clearing all cache relevant for a given location._

- `parent-<parent-location-id>` :
  _Used for clearing cache of all siblings of an location._

- `parent-<location-id>` :
  _Used for clearing cache of all the children of a location._

- `path-<location-id>` :
  _For operations that change the tree itself, like move/remove/(..)._

- `relation-<content-id>` :
- `relation-location-<location-id>` :
   _For use when updates affect all their reverse relations. ezplatform-http-cache does not add this tag to responses
   automatically, just purges on it if present, response tagging with this is currently done inline in template logic / views
   where relation is actually used for rendering (when using ESI, if inline it's own tags will be added to response).
   ezpublish-kernel add these as of v6.13.2/v7.1.0 on default relation templates)_

### Tags for Section responses

- `section-<section-id>` :
  _For use when section changes affecting section reponses (i.e. REST)._


### Tags for ContenType responses

- `type-<content-type-id>` :
  _For use when content type changes affecting content type reponses (i.e. REST)._

- `type-group-<content-type-id>` :
  _For use when content type group changes affecting content type group reponses (i.e. REST)._

### Misc

- `ez-user-context-hash`
   _Internal tag used for tagging /_fos_user_context_hash to expire it on role & role assigment changes._

- `ez-all`:
   _Internal tag used for being able to clear all cache. Main use case is being able to expire (soft purge) all cache on
   deployment of new versions of your installation which for instance changes representation / design dramatically._

## How Response tagging is done


### For Content View

For Content View there is a dedicated response listener `HttpCacheResponseSubscriber` that triggers a set of [Response
taggers](docs/response_taggers.md) responsible for translating info from the objects involved in generating the view to
corresponding tags as listed above. These can be found in `src/ResponseTagger`.


### For responses with X-Location-Id

For custom or eZ controllers _(like REST at the time of writing)_ still using `X-Location-Id`, a dedicated response
listener `XLocationIdResponseSubscriber` handles translating this to tags so the cache can be properly invalidated by
this bundle. It supports comma separated location id values which was only partially supported in earlier versions:

```php
    /** @var \Symfony\Component\HttpFoundation\Response $response */
    $response->headers->set('X-Location-Id', 123);
    
    // Alternatively using several location id values, requires ezplatform-http-cache to work across all supported proxies
    $response->headers->set('X-Location-Id', '123,212,42');
```

*NOTE: This is currently marked as Deprecated, and for rendering eZ content it is thus adviced to refactor to use Content
View. For other needs there is an FOS tag handler for Twig and PHP that can be used, see below for further info.*


### For custom needs using FOSHttpCache (tagging relations and more)

For custom needs, including template logic for eZ content relations which is here used for examples, there are two ways
to tag your responses.

#### Twig use

For twig usage, you can make sure response is tagged correctly by using the following twig operator in your template:
```twig
    {{ fos_httpcache_tag('relation-33') }}

    {# Or using array for several values #}
    {{ fos_httpcache_tag(['relation-33', 'relation-44']) }}
```

See: http://foshttpcachebundle.readthedocs.io/en/1.3/features/tagging.html#tagging-from-twig-templates

Alternatively if you have a location(s) that you render inline & want invalidated on any kind of change:
```twig
    {{ ez_http_tag_location( location ) }}
```

#### PHP use

Fo PHP usage, FOSHttpCache exposes `fos_http_cache.handler.tag_handler` service which lets you add tags to a response:
```php
    /** @var \FOS\HttpCache\Handler\TagHandler $tagHandler */
    $tagHandler->addTags(['relation-33', 'relation-44']);
```

See: http://foshttpcachebundle.readthedocs.io/en/1.3/features/tagging.html#tagging-from-code

*WARNING: Be aware service name and type hint will somewhat change once we move to FOSHttpCache 2.x, so in this case
you can alternatively consider to add tag in twig template or stay with usage of `X-Location-Id` for the time being.*

## How purge tagging is done (invalidation) 

This bundle uses Repository API Slots to listen to Signals emitted on repository operations, and depending on the
operation triggers expiry on a specific tag or set of tags.

E.g. on Move Location signal the following tags will be purged:
```php
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\LocationService\MoveSubtreeSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        return [
            // The tree itself being moved (all children will have this tag)
            'path-' . $signal->locationId,
            // old parent
            'location-' . $signal->oldParentLocationId,
            // old siblings
            'parent-' . $signal->oldParentLocationId,
            // new parent
            'location-' . $signal->newParentLocationId,
            // new siblings
            'parent-' . $signal->newParentLocationId,
        ];
    }
```

All slots can be found in `src/SignalSlot`.


## Troubleshooting

One common issue to encounter is that the tagging headers exceed limits in Varnish, stopping either caching or invalidation from happening:
- [http_resp_hdr_len](https://varnish-cache.org/docs/6.0/reference/varnishd.html#http-resp-hdr-len) (e.g. 32k)
- [http_max_hdr](https://varnish-cache.org/docs/6.0/reference/varnishd.html#http-max-hdr) (e.g. 128)
- [http_resp_size](https://varnish-cache.org/docs/6.0/reference/varnishd.html#http-resp-size) (e.g. 64k)

For more up-to-date info see online doc: https://doc.ezplatform.com/en/latest/guide/http_cache/#available-tags
