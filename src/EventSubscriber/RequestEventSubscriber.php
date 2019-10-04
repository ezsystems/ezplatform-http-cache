<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestEventSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $userHashHeaderName;

    public function __construct(string $userHashHeaderName)
    {
        $this->userHashHeaderName = $userHashHeaderName;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequestForward', 15],
            ],
        ];
    }

    public function onKernelRequestForward(RequestEvent $event): void
    {
        if ($event->isMasterRequest()) {
            $request = $event->getRequest();

            if (
                $request->attributes->get('needsForward') &&
                $request->attributes->has('semanticPathinfo') &&
                $request->headers->has($this->userHashHeaderName)
            ) {
                $headersToForward = $request->attributes->get('forwardRequestHeaders', []);
                $headersToForward[$this->userHashHeaderName] = $request->headers->get($this->userHashHeaderName);
                $request->attributes->set('forwardRequestHeaders', $headersToForward);
            }
        }
    }
}
