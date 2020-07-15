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
repository index in use. I.e. "0p1", where "0" is the repository prefix._

Varnish or Fastly are highly recommended for medium to large traffic needs. Besides being able to handle much more traffic,
supported for use in cluster setup, they also both support soft purge _(by tags)_, meaning they are able to serve stale
content while it's refreshed in the background on-demand, leading to more stable load on your backend.


## Tags in use in this bundle

### Tags for Content responses

- *Content*:\
  `c<content-id>`:
  _Used on anything that is affected by changes to content, on content itself as well as location and so on._

- *Content Version*:\
  `cv<content-id>`:
  _Used for clearing cache for content version list views, when *not* affecting the published content._

- *Content Type*:\
  `ct<content-type-id>`:
  _For use when content type changes affecting content of its type._

- *Location*:\
  `l<location-id>`:
  _Used for clearing all cache relevant for a given location._

- *Parent Location*:\
  For responses this represent the parent location, on purges it's used in 2 distinct ways:\
  `pl<parent-location-id>`:
  _Used for clearing cache of all siblings of an location._\
  `pl<location-id>`:
  _Used for clearing cache of all the children of a location._

- *Path* _(all path elements of a location)_:\
  `p<location-id>`:
  _For operations that change the tree itself, like move/remove/(..)._

- *Relations*:\
  `r<content-id>` & `rl<location-id>`:
   _For use when updates affect all their reverse relations. ezplatform-http-cache does not add this tag to responses
   automatically, just purges on it if present, response tagging with this is currently done inline in template logic / views
   where relation is actually used for rendering (when using ESI, if inline the Content own tags should be added to response instead, unless you are hitting tag header limits)._
   These differs from `content-` and `location-` by _only_ being purged when relation itself is removed or otherwise affected._

### Tags for Section responses

- `s<section-id>` :
  _For use when section changes affecting section responses (i.e. REST)._


### Tags for ContenType responses

- `t<content-type-id>` :
  _For use when content type changes affecting content type responses (i.e. REST)._

- `tg<content-type-id>` :
  _For use when content type group changes affecting content type group responses (i.e. REST)._

### Misc internal tags

- `ez-user-context-hash`:
   _Internal tag used for tagging /_fos_user_context_hash to expire it on role & role assigment changes._

- `ez-invalidate-token`:
  _Internal tag for use by token lookup when in token authentication mode (for setups where IP validation is not possible)._

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
    {{ fos_httpcache_tag('r33') }}

    {# Or using array for several values #}
    {{ fos_httpcache_tag(['r33', 'r44']) }}
```

See: http://foshttpcachebundle.readthedocs.io/en/1.3/features/tagging.html#tagging-from-twig-templates


However for relations, which you typically used prior to a ESI include for some content, rather use one of:
```twig
    {# As of v0.9.3 two twig functions for relation use cases was added, both handling single and array values #}
    {# First one is for relation(s) for Content, as shown by it's id #}
    {{ ez_http_tag_relation_ids(relation_content.id) }}

    {# Second one for relation locations, here shown using array of location id's #}
    {{ ez_http_tag_relation_location_ids(relation_location_ids) }}
```


Alternatively if you have a location(s) that you render _inline_ & want invalidated on any kind of change:
```twig
    {{ ez_http_tag_location( location ) }}
```

TIP: Don't use `ez_http_tag_location` when you are rendering a large amount of content/location items, it will cause tag
     header to become to large. Consider using less tags with for instance `ez_http_tag_relation_(location_)ids`, and account for
     possible stale cache by reducing cache ttl for the given response.
     Also strongly consider to upgrade to ezplatform-http-cache 1.0 or higher which reduces cache tag size.

#### PHP use

For PHP usage, a few options exist _(autowirable classes of '@fos_http_cache.handler.tag_handler')_:
```php
    /**
     * Using low level Tag handler to add tags manually.
     * WARNING: This FOS class name, and service name is different in v3 (FosHttpCache 2.x).
     *
     * @var \FOS\HttpCache\Handler\TagHandler $tagHandler
     */
    $tagHandler->addTags(['r33', 'r44']);

    /**
     * Better option in order to be more future proof when setting specific Ibexa related tags.
     *
     * @var \EzSystems\PlatformHttpCacheBundle\Handler\ContentTagInterface $tagHandler
     */
    $tagHandler->addRelationTags([33, 44]);
```

See: http://foshttpcachebundle.readthedocs.io/en/1.3/features/tagging.html#tagging-from-code

## How purge tagging is done (invalidation) 

This bundle uses Repository API Slots to listen to Signals emitted on repository operations, and depending on the
operation triggers expiry on a specific tag or set of tags.

E.g. on Move Location signal the following tags will be purged:
```php
use EzSystems\PlatformHttpCacheBundle\Handler\ContentTagInterface;

    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\LocationService\MoveSubtreeSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        return [
            // The tree itself being moved (all children will have this tag)
            ContentTagInterface::PATH_PREFIX . $signal->locationId,
            // old parent
            ContentTagInterface::LOCATION_PREFIX . $signal->oldParentLocationId,
            // old siblings
            ContentTagInterface::PARENT_LOCATION_PREFIX . $signal->oldParentLocationId,
            // new parent
            ContentTagInterface::LOCATION_PREFIX . $signal->newParentLocationId,
            // new siblings
            ContentTagInterface::PARENT_LOCATION_PREFIX . $signal->newParentLocationId,
        ];
    }
```

All slots can be found in `src/SignalSlot`

## Troubleshooting

### Understanding tag response headers

Given a header like `Xkey: 0c22 0ct2 (...)`, the following can be understood:
- `Xkey:` Configured tag header, by this bundle config. Understood by Varnish _(with xkey module)_, and by our enhanced version of Symfony HttpCache Proxy "AppCache".
- `0..`: Current repository index, set for you by this bundle. Done in order to support multi repository setups, it's the array key of the current repository is used _(and not the full identifier)_.
- `..c22`: Content id 22
- `..ct2`: Content Type id 2

### Header limits

Even if the tags are kept as short as possible, you might still encounter issue with tag header exceeding
limits in Varnis/Apache/Nginx/Fastly, stopping either caching or invalidation from working as expected.

For handling these cases it's simplest to increase the limits on the service side if possible, and if not look at the
code involved to see if amount of tags can be reduced.

#### Configuring Services

*Varnish* config:
- [http_resp_hdr_len](https://varnish-cache.org/docs/6.0/reference/varnishd.html#http-resp-hdr-len) (default 8k, change to i.e. 32k)
- [http_max_hdr](https://varnish-cache.org/docs/6.0/reference/varnishd.html#http-max-hdr) (default 64, change to i.e. 128)
- [http_resp_size](https://varnish-cache.org/docs/6.0/reference/varnishd.html#http-resp-size) (default 23k, change to i.e. 96k)
- [workspace_backend](https://varnish-cache.org/docs/6.0/reference/varnishd.html#workspace-backend) (default 64k, change to i.e. 128k)

*Fastly* has a `Surrogate-Key` header limit of *16kb*, this can *not* be changed.

*Apache* has a [hard](https://github.com/apache/httpd/blob/5f32ea94af5f1e7ea68d6fca58f0ac2478cc18c5/server/util_script.c#L495) [coded](https://github.com/apache/httpd/blob/7e2d26eac309b2d79e467ef586526c10e0f226f8/include/httpd.h#L299-L303) limit of 8kb, so if you face this issue consider using nginx instead.

*Nginx* has a default limit of 4k/8k when buffering responses from PHP-fpm/fast-cgi:
- https://nginx.org/en/docs/http/ngx_http_proxy_module.html#proxy_buffer_size
- https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_buffer_size


#### Limit tags header output by system

Typical case with too many tags would be when inline rendering a lot of embed content object.
Normally the system will add all the tags for this content, to handle every possible scenario of updates to them.

So if you embed hundreds of content on the same page _(i.e. in richtext, using relations, or using page builder)_, it will explode the tag usage.

However if for instance you just display the content name, image attribute, and/or link, then it would be enough to:
- Just use `r<id>` tag, or preferably the abstractions for it.
- Optionally: Set reduced cache TTL for the given view in order to reduce remote risk of subtree operations affecting the cached page
  without correctly purging the view.

If that is not an option, you can opt-in to set a max length parameter (in bytes) and corresponding ttl (in seconds):
```yaml
parameters:
    # Warning, setting this means you risk losing tag information, risking stale cache. Here set below 8k:
    ezplatform.http_cache.tags.header_max_length: 7900
    # In order to reduce risk of stale cache issues, you should set a lower TTL here then globally (here set as 2h)
    ezplatform.http_cache.tags.header_reduced_ttl: 7200
```
