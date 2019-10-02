<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use FOS\HttpCache\TagHeaderFormatter\TagHeaderFormatter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
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
    private $tagHeader;

    public function __construct(
        RepositoryTagPrefix $prefixService,
        string $tagHeader = TagHeaderFormatter::DEFAULT_HEADER_NAME
    ) {
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
     * @param ResponseEvent $event
     */
    public function tagUserContext(ResponseEvent $event)
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
        // NB: We prefix this even if route is not SA aware, but this is the same as with REST. As doc states,
        // it's expected that each repo needs to have own domain so requests against base domain represent same repo.
        $response->headers->set($this->tagHeader, $this->prefixService->getRepositoryPrefix() . 'ez-user-context-hash');
    }
}
