<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\Role\AssignRoleToUserEvent;
use eZ\Publish\API\Repository\Events\Role\AssignRoleToUserGroupEvent;
use eZ\Publish\API\Repository\Events\Role\DeleteRoleEvent;
use eZ\Publish\API\Repository\Events\Role\PublishRoleDraftEvent;
use eZ\Publish\API\Repository\Events\Role\RemoveRoleAssignmentEvent;
use Symfony\Contracts\EventDispatcher\Event;

final class RoleEventsSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            AssignRoleToUserEvent::class => 'clearUserContextHashCache',
            AssignRoleToUserGroupEvent::class => 'clearUserContextHashCache',
            DeleteRoleEvent::class => 'clearUserContextHashCache',
            PublishRoleDraftEvent::class => 'clearUserContextHashCache',
            RemoveRoleAssignmentEvent::class => 'clearUserContextHashCache',
        ];
    }

    public function clearUserContextHashCache(Event $event)
    {
        $this->purgeClient->purge([
            'ez-user-context-hash',
        ]);
    }
}
