<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\Location\CopySubtreeEvent;
use eZ\Publish\API\Repository\Events\Location\CreateLocationEvent;
use eZ\Publish\API\Repository\Events\Location\DeleteLocationEvent;
use eZ\Publish\API\Repository\Events\Location\HideLocationEvent;
use eZ\Publish\API\Repository\Events\Location\MoveSubtreeEvent;
use eZ\Publish\API\Repository\Events\Location\SwapLocationEvent;
use eZ\Publish\API\Repository\Events\Location\UnhideLocationEvent;
use eZ\Publish\API\Repository\Events\Location\UpdateLocationEvent;

class LocationEventsSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            CopySubtreeEvent::class => 'onCopySubtree',
            CreateLocationEvent::class => 'onCreateLocation',
            DeleteLocationEvent::class => 'onDeleteLocation',
            HideLocationEvent::class => 'onHideLocation',
            MoveSubtreeEvent::class => 'onMoveSubtree',
            SwapLocationEvent::class => 'onSwapLocation',
            UnhideLocationEvent::class => 'onUnhideLocation',
            UpdateLocationEvent::class => 'onUpdateLocation',
        ];
    }

    public function onCopySubtree(CopySubtreeEvent $event): void
    {
        $locationId = $event->getTargetParentLocation()->id;

        $this->purgeClient->purge(
            $this->getParentLocationTags($locationId)
        );
    }

    public function onCreateLocation(CreateLocationEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;
        $locationId = $event->getLocation()->id;
        $parentLocationId = $event->getLocation()->parentLocationId;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getLocationTags($locationId),
            $this->getParentLocationTags($parentLocationId),
        );

        $this->purgeClient->purge($tags);
    }

    public function onDeleteLocation(DeleteLocationEvent $event): void
    {
        $contentId = $event->getLocation()->contentId;
        $locationId = $event->getLocation()->id;
        $parentLocationId = $event->getLocation()->parentLocationId;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getLocationTags($locationId),
            $this->getParentLocationTags($parentLocationId),
            [
                'path-' . $locationId,
            ]
        );

        $this->purgeClient->purge($tags);
    }

    public function onHideLocation(HideLocationEvent $event): void
    {
        $contentId = $event->getLocation()->contentId;
        $locationId = $event->getLocation()->id;
        $parentLocationId = $event->getLocation()->parentLocationId;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getLocationTags($locationId),
            $this->getParentLocationTags($parentLocationId),
            [
                'path-' . $locationId,
            ]
        );

        $this->purgeClient->purge($tags);
    }

    public function onMoveSubtree(MoveSubtreeEvent $event): void
    {
        $locationId = $event->getLocation()->id;
        $oldParentLocationId = $event->getLocation()->parentLocationId;
        $newParentLocationId = $event->getNewParentLocation()->id;

        $tags = array_merge(
            $this->getParentLocationTags($oldParentLocationId),
            $this->getParentLocationTags($newParentLocationId),
            [
                'path-' . $locationId,
            ]
        );

        $this->purgeClient->purge($tags);
    }

    public function onSwapLocation(SwapLocationEvent $event): void
    {
        $sourceContentId = $event->getLocation1()->contentId;
        $sourceLocationId = $event->getLocation1()->id;
        $sourceParentLocationId = $event->getLocation1()->parentLocationId;
        $targetContentId = $event->getLocation2()->contentId;
        $targetLocationId = $event->getLocation2()->id;
        $targetParentLocationId = $event->getLocation2()->parentLocationId;

        $tags = array_merge(
            $this->getParentLocationTags($sourceParentLocationId),
            $this->getParentLocationTags($targetParentLocationId),
            [
                'content-' . $sourceContentId,
                'path-' . $sourceLocationId,
                'content-' . $targetContentId,
                'path-' . $targetLocationId,
            ]
        );

        $this->purgeClient->purge($tags);
    }

    public function onUnhideLocation(UnhideLocationEvent $event): void
    {
        $contentId = $event->getLocation()->contentId;
        $locationId = $event->getLocation()->id;
        $parentLocationId = $event->getLocation()->parentLocationId;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getLocationTags($locationId),
            $this->getParentLocationTags($parentLocationId),
            [
                'path-' . $locationId,
            ]
        );

        $this->purgeClient->purge($tags);
    }

    public function onUpdateLocation(UpdateLocationEvent $event): void
    {
        $contentId = $event->getLocation()->contentId;
        $locationId = $event->getLocation()->id;
        $parentLocationId = $event->getLocation()->parentLocationId;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getLocationTags($locationId),
            $this->getParentLocationTags($parentLocationId),
            );

        $this->purgeClient->purge($tags);
    }
}
