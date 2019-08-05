<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\ContentType\AssignContentTypeGroupEvent;
use eZ\Publish\API\Repository\Events\ContentType\DeleteContentTypeEvent;
use eZ\Publish\API\Repository\Events\ContentType\DeleteContentTypeGroupEvent;
use eZ\Publish\API\Repository\Events\ContentType\PublishContentTypeDraftEvent;
use eZ\Publish\API\Repository\Events\ContentType\UnassignContentTypeGroupEvent;
use eZ\Publish\API\Repository\Events\ContentType\UpdateContentTypeGroupEvent;

final class ContentTypeEventsSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            AssignContentTypeGroupEvent::class => 'onAssignContentTypeGroup',
            DeleteContentTypeGroupEvent::class => 'onDeleteContentTypeGroup',
            DeleteContentTypeEvent::class => 'onDeleteContentType',
            PublishContentTypeDraftEvent::class => 'onPublishContentTypeDraft',
            UnassignContentTypeGroupEvent::class => 'onUnassignContentTypeGroup',
            UpdateContentTypeGroupEvent::class => 'onUpdateContentTypeGroup',
        ];
    }

    public function onAssignContentTypeGroup(AssignContentTypeGroupEvent $event): void
    {
        $contentTypeGroupId = $event->getContentTypeGroup()->id;

        $this->purgeClient->purge([
            'type-group-' . $contentTypeGroupId,
        ]);
    }

    public function onDeleteContentTypeGroup(DeleteContentTypeGroupEvent $event): void
    {
        $contentTypeGroupId = $event->getContentTypeGroup()->id;

        $this->purgeClient->purge([
            'type-group-' . $contentTypeGroupId,
        ]);
    }

    public function onDeleteContentType(DeleteContentTypeEvent $event): void
    {
        $contentTypeId = $event->getContentType()->id;

        $this->purgeClient->purge([
            'content-type-' . $contentTypeId,
            'type-' . $contentTypeId,
        ]);
    }

    public function onPublishContentTypeDraft(PublishContentTypeDraftEvent $event): void
    {
        $contentTypeId = $event->getContentTypeDraft()->id;

        $this->purgeClient->purge([
            'content-type-' . $contentTypeId,
            'type-' . $contentTypeId,
        ]);
    }

    public function onUnassignContentTypeGroup(UnassignContentTypeGroupEvent $event): void
    {
        $contentTypeGroupId = $event->getContentTypeGroup()->id;

        $this->purgeClient->purge([
            'type-group-' . $contentTypeGroupId,
        ]);
    }

    public function onUpdateContentTypeGroup(UpdateContentTypeGroupEvent $event): void
    {
        $contentTypeGroupId = $event->getContentTypeGroup()->id;

        $this->purgeClient->purge([
            'type-group-' . $contentTypeGroupId,
        ]);
    }
}
