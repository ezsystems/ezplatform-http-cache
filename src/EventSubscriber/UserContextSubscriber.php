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
 * Tag /_fos_user_context_hash responses, so we can expire/clear it by tag.
 */
class UserContextSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $tagHeader = 'xkey';

    public function __construct($tagHeader)
    {
        $this->tagHeader = $tagHeader;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => ['tagUserContext', 10]];
    }

    /**
     * Tag vnd.fos.user-context-hash responses if they are set to cached.
     *
     * @param FilterResponseEvent $event
     */
    public function tagUserContext(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        if (!$response->isCacheable()) {
            return;
        }

        if ($response->headers->get('Content-Type') !== 'application/vnd.fos.user-context-hash') {
            return;
        }

        if (!$response->getTtl()) {
            return;
        }

        // We need to set tag directly on repsonse here to make sure this does not also get applied to the main request
        // when using Symfony Proxy, as tag handler does not clear tags between requests.
        $response->headers->set($this->tagHeader, 'ez-user-context-hash');
    }
}
