<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\Trash\RecoverEvent;
use eZ\Publish\API\Repository\Events\Trash\TrashEvent;

final class TrashEventsSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            TrashEvent::class => 'onTrash',
            RecoverEvent::class => 'onRecover',
        ];
    }

    public function onTrash(TrashEvent $event): void
    {
        $contentId = $event->getLocation()->contentId;
        $locationId = $event->getLocation()->id;
        $parentLocationId = $event->getLocation()->parentLocationId;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getLocationTags($locationId),
            $this->getParentLocationTags($parentLocationId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onRecover(RecoverEvent $event): void
    {
        $contentId = $event->getLocation()->contentId;
        $parentLocationId = $event->getLocation()->parentLocationId;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getParentLocationTags($parentLocationId)
        );

        $this->purgeClient->purge($tags);
    }
}
