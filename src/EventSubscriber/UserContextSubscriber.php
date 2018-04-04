<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use FOS\HttpCache\Handler\TagHandler;

/**
 * Tag /_fos_user_context_hash responses, so we can expire/clear it by tag.
 */
class UserContextSubscriber implements EventSubscriberInterface
{
    /** @var \FOS\HttpCache\Handler\TagHandler */
    private $tagHandler;

    public function __construct(TagHandler $tagHandler)
    {
        $this->tagHandler = $tagHandler;
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

        $this->tagHandler->addTags(['ez-user-context-hash']);
    }
}
