<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\Content\CopyContentEvent;
use eZ\Publish\API\Repository\Events\Content\CreateContentDraftEvent;
use eZ\Publish\API\Repository\Events\Content\DeleteContentEvent;
use eZ\Publish\API\Repository\Events\Content\DeleteVersionEvent;
use eZ\Publish\API\Repository\Events\Content\HideContentEvent;
use eZ\Publish\API\Repository\Events\Content\PublishVersionEvent;
use eZ\Publish\API\Repository\Events\Content\RevealContentEvent;
use eZ\Publish\API\Repository\Events\Content\UpdateContentEvent;
use eZ\Publish\API\Repository\Events\Content\UpdateContentMetadataEvent;
use EzSystems\PlatformHttpCacheBundle\Handler\ContentTagInterface;

final class ContentEventsSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            CopyContentEvent::class => 'onCopyContentEvent',
            CreateContentDraftEvent::class => 'onCreateContentDraftEvent',
            DeleteContentEvent::class => 'onDeleteContentEvent',
            DeleteVersionEvent::class => 'onDeleteVersionEvent',
            HideContentEvent::class => 'onHideContentEvent',
            PublishVersionEvent::class => 'onPublishVersionEvent',
            RevealContentEvent::class => 'onRevealContentEvent',
            UpdateContentEvent::class => 'onUpdateContentEvent',
            UpdateContentMetadataEvent::class => 'onUpdateContentMetadataEvent',
        ];
    }

    public function onCopyContentEvent(CopyContentEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;
        $parentLocationId = $event->getDestinationLocationCreateStruct()->parentLocationId;

        $this->purgeClient->purge([
            ContentTagInterface::CONTENT_PREFIX . $contentId,
            ContentTagInterface::LOCATION_PREFIX . $parentLocationId,
            ContentTagInterface::PATH_PREFIX . $parentLocationId,
        ]);
    }

    public function onCreateContentDraftEvent(CreateContentDraftEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $this->purgeClient->purge([
            ContentTagInterface::CONTENT_VERSION_PREFIX . $contentId,
        ]);
    }

    public function onDeleteContentEvent(DeleteContentEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $tags = $this->getContentTags($contentId);

        foreach ($event->getLocations() as $locationId) {
            $tags[] = ContentTagInterface::PATH_PREFIX . $locationId;
        }

        $this->purgeClient->purge($tags);
    }

    public function onDeleteVersionEvent(DeleteVersionEvent $event): void
    {
        $contentId = $event->getVersionInfo()->getContentInfo()->id;

        $this->purgeClient->purge([
            ContentTagInterface::CONTENT_VERSION_PREFIX . $contentId,
        ]);
    }

    public function onHideContentEvent(HideContentEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getContentLocationsTags($contentId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onPublishVersionEvent(PublishVersionEvent $event): void
    {
        $contentId = $event->getContent()->getVersionInfo()->getContentInfo()->id;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getContentLocationsTags($contentId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onRevealContentEvent(RevealContentEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getContentLocationsTags($contentId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onUpdateContentEvent(UpdateContentEvent $event): void
    {
        $contentId = $event->getContent()->getVersionInfo()->getContentInfo()->id;

        $this->purgeClient->purge([
            ContentTagInterface::CONTENT_VERSION_PREFIX . $contentId,
        ]);
    }

    public function onUpdateContentMetadataEvent(UpdateContentMetadataEvent $event): void
    {
        $contentId = $event->getContent()->getVersionInfo()->getContentInfo()->id;

        $this->purgeClient->purge(
            $this->getContentTags($contentId)
        );
    }
}
