<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\Section\AssignSectionEvent;
use eZ\Publish\API\Repository\Events\Section\DeleteSectionEvent;
use eZ\Publish\API\Repository\Events\Section\UpdateSectionEvent;

class SectionEventsSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            AssignSectionEvent::class => 'onAssignSection',
            DeleteSectionEvent::class => 'onDeleteSection',
            UpdateSectionEvent::class => 'onUpdateSection',
        ];
    }

    public function onAssignSection(AssignSectionEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getContentLocationsTags($contentId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onDeleteSection(DeleteSectionEvent $event): void
    {
        $sectionId = $event->getSection()->id;

        $this->purgeClient->purge([
           'section-' . $sectionId,
        ]);
    }

    public function onUpdateSection(UpdateSectionEvent $event): void
    {
        $sectionId = $event->getSection()->id;

        $this->purgeClient->purge([
            'section-' . $sectionId,
        ]);
    }
}
