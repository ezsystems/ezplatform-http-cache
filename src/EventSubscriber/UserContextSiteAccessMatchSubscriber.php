<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use eZ\Publish\Core\MVC\Symfony\EventListener\SiteAccessMatchListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserContextSiteAccessMatchSubscriber implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\MVC\Symfony\EventListener\SiteAccessMatchListener */
    protected $innerSubscriber;

    /** @var \Symfony\Component\HttpFoundation\RequestMatcherInterface */
    private $userContextRequestMatcher;

    public function __construct(
        SiteAccessMatchListener $innerSubscriber,
        RequestMatcherInterface $userContextRequestMatcher
    ) {
        $this->innerSubscriber = $innerSubscriber;
        $this->userContextRequestMatcher = $userContextRequestMatcher;
    }

    public static function getSubscribedEvents()
    {
        return [
            // Should take place just after FragmentListener (priority 48) in order to get rebuilt request attributes in case of subrequest
            KernelEvents::REQUEST => ['checkIfRequestForUserContextHash', 45],
        ];
    }

    public function checkIfRequestForUserContextHash(RequestEvent $event)
    {
        $request = $event->getRequest();

        // Don't try to match when it's request for user hash . SiteAccess is irrelevant in this case.
        if ($this->userContextRequestMatcher->matches($request) && !$request->attributes->has('_ez_original_request')) {
            return;
        }

        $this->innerSubscriber->onKernelRequest($event);
    }
}
