<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\URL\UpdateUrlEvent;

class UrlEventsSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            UpdateUrlEvent::class => 'onUrlUpdate',
        ];
    }

    public function onUrlUpdate(UpdateUrlEvent $event): void
    {
        $urlId = $event->getUpdatedUrl()->id;

        if ($event->getStruct()->url !== null) {
            $this->purgeClient->purge(
                $this->getContentUrlTags($urlId)
            );
        }
    }
}
