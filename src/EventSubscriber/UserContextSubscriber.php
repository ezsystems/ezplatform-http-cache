<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Tag /_fos_user_context_hash responses, so we can expire/clear it by tag.
 */
class UserContextSubscriber implements EventSubscriberInterface
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix
     */
    private $prefixService;

    /**
     * @var string
     */
    private $tagHeader = 'xkey';

    public function __construct(RepositoryTagPrefix $prefixService, $tagHeader)
    {
        $this->prefixService = $prefixService;
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

        // We need to set tag directly on response here to make sure this does not also get applied to the main request
        // when using Symfony Proxy, as tag handler does not clear tags between requests.
        // OPEN QUESTION: Is SA even loaded for user hash route? If not, using prefix for this won't work.
        // IF so change RepositoryTagPrefix to TagPrefixer->prefixTag($tag) or something so we can skip prefix on tags we know need to be global ("all" and "ez-user-context-hash")
        $response->headers->set($this->tagHeader, $this->prefixService->getRepositoryPrefix() . 'ez-user-context-hash');
    }
}
