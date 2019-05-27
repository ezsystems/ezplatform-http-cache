<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use EzSystems\PlatformHttpCacheBundle\Handler\TagHandler;
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

    /** @var \EzSystems\PlatformHttpCacheBundle\Handler\TagHandler */
    private $tagHandler;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(TagHandler $tagHandler, Repository $repository)
    {
        $this->tagHandler = $tagHandler;
        $this->repository = $repository;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => ['rewriteCacheHeader', 10]];
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

        // Map the tags, even if not officially supported, handle comma separated values as was possible with Varnish
        $tags = [];
        foreach (explode(',', $response->headers->get(static::LOCATION_ID_HEADER)) as $id) {
            $id = trim($id);
            try {
                /** @var $location \eZ\Publish\API\Repository\Values\Content\Location */
                $location = $this->repository->sudo(function (Repository $repository) use ($id) {
                    return $repository->getLocationService()->loadLocation($id);
                });

                $tags[] = 'location-' . $location->id;
                $tags[] = 'parent-' . $location->parentLocationId;

                foreach ($location->path as $pathItem) {
                    $tags[] = 'path-' . $pathItem;
                }

                $contentInfo = $location->getContentInfo();
                $tags[] = 'content-' . $contentInfo->id;
                $tags[] = 'content-type-' . $contentInfo->contentTypeId;

                if ($contentInfo->mainLocationId !== $location->id) {
                    $tags[] = 'location-' . $contentInfo->mainLocationId;
                }
            } catch (NotFoundException $e) {
                $tags[] = "location-$id";
                $tags[] = "path-$id";
            }
        }

        $this->tagHandler->addTags($tags);
        $response->headers->remove(static::LOCATION_ID_HEADER);
    }
}
