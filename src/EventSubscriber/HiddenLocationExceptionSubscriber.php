<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use eZ\Publish\Core\MVC\Exception\HiddenLocationException;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\ContentInfoTagger;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\LocationTagger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class HiddenLocationExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\LocationTagger;
     */
    private $locationTagger;

    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\ContentInfoTagger
     */
    private $contentInfoTagger;

    public function __construct(LocationTagger $locationTagger, ContentInfoTagger $contentInfoTagger)
    {
        $this->locationTagger = $locationTagger;
        $this->contentInfoTagger = $contentInfoTagger;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => ['tagHiddenLocationExceptionResponse', 10]];
    }

    public function tagHiddenLocationExceptionResponse(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof HiddenLocationException) {
            return;
        }

        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $event->getException()->getLocation();
        $this->locationTagger->tag($location);
        $this->contentInfoTagger->tag($location->getContentInfo());
    }
}
