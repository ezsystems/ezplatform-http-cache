<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\ObjectState\SetContentStateEvent;

class ObjectStateEventsSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            SetContentStateEvent::class => 'onSetContentState',
        ];
    }

    public function onSetContentState(SetContentStateEvent $event): void
    {
        $contentId = $event->getContentInfo()->id;

        $tags = array_merge(
            $this->getContentTags($contentId),
            $this->getContentLocationsTags($contentId)
        );

        $this->purgeClient->purge($tags);
    }
}
