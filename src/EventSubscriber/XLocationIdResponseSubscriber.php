<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Rewrites the X-Location-Id HTTP header.
 *
 * This is a BC layer for custom controllers (including REST server) still
 * using X-Location-Id header which is now deprecated. For
 * full value of tagging, see docs/using_tags.md for how to take advantage of the
 * system.
 */
class XLocationIdResponseSubscriber implements EventSubscriberInterface
{
    const LOCATION_ID_HEADER = 'X-Location-Id';

    /**
     * @var string
     */
    private $tagHeader;

    public function __construct($tagHeader)
    {
        $this->tagHeader = $tagHeader;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => ['rewriteCacheHeader', -5]];
    }

    public function rewriteCacheHeader(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        if (!$response->headers->has(static::LOCATION_ID_HEADER)) {
            return;
        }

        @trigger_error(
            'X-Location-Id is no longer preferred way to tag content responses, see ezplatform-http-cache/docs/using_tags.md',
            E_USER_DEPRECATED
        );

        // Map the tag, even if not officially supported, handle comma separated values as was possible with Varnish
        $tags = array_map(
            function ($id) {
                return 'location-' . trim($id);
            },
            explode(',', $response->headers->get(static::LOCATION_ID_HEADER))
        );

        if ($response->headers->has($this->tagHeader)) {
            $tags = array_merge($response->headers->get($this->tagHeader, null, false) , $tags);
        }

        // @todo we need to use abstract tag writer to also be able to support Fastly
        // FOS has some stuff around this in 2.x but not in a good way in 1.x
        $response->headers->set($this->tagHeader, array_unique($tags));
        $response->headers->remove(static::LOCATION_ID_HEADER);
    }
}
