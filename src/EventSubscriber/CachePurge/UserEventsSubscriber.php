<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber\CachePurge;

use eZ\Publish\API\Repository\Events\User\AssignUserToUserGroupEvent;
use eZ\Publish\API\Repository\Events\User\CreateUserEvent;
use eZ\Publish\API\Repository\Events\User\CreateUserGroupEvent;
use eZ\Publish\API\Repository\Events\User\UnAssignUserFromUserGroupEvent;
use eZ\Publish\API\Repository\Events\User\UpdateUserEvent;
use eZ\Publish\API\Repository\Events\User\UpdateUserGroupEvent;

class UserEventsSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            AssignUserToUserGroupEvent::class => 'onAssignUserToUserGroup',
            CreateUserGroupEvent::class => 'onCreateUserGroup',
            CreateUserEvent::class => 'onCreateUser',
            UnAssignUserFromUserGroupEvent::class => 'onUnAssignUserFromUserGroup',
            UpdateUserGroupEvent::class => 'onUpdateUserGroup',
            UpdateUserEvent::class => 'onUpdateUser',
        ];
    }

    public function onAssignUserToUserGroup(AssignUserToUserGroupEvent $event): void
    {
        $userId = $event->getUser()->id;
        $userGroupId = $event->getUserGroup()->id;

        $this->purgeClient->purge([
            'content-' . $userId,
            'content-' . $userGroupId,
            'ez-user-context-hash',
        ]);
    }

    public function onCreateUserGroup(CreateUserGroupEvent $event): void
    {
        $userGroupId = $event->getUserGroup()->id;

        $tags = array_merge(
            $this->getContentTags($userGroupId),
            $this->getContentLocationsTags($userGroupId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onCreateUser(CreateUserEvent $event): void
    {
        $userId = $event->getUser()->id;

        $tags = array_merge(
            $this->getContentTags($userId),
            $this->getContentLocationsTags($userId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onUnAssignUserFromUserGroup(UnAssignUserFromUserGroupEvent $event): void
    {
        $userId = $event->getUser()->id;
        $userGroupId = $event->getUserGroup()->id;

        $this->purgeClient->purge([
            'content-' . $userId,
            'content-' . $userGroupId,
            'ez-user-context-hash',
        ]);
    }

    public function onUpdateUserGroup(UpdateUserGroupEvent $event): void
    {
        $userGroupId = $event->getUserGroup()->id;

        $tags = array_merge(
            $this->getContentTags($userGroupId),
            $this->getContentLocationsTags($userGroupId)
        );

        $this->purgeClient->purge($tags);
    }

    public function onUpdateUser(UpdateUserEvent $event): void
    {
        $userId = $event->getUser()->id;

        $tags = array_merge(
            $this->getContentTags($userId),
            $this->getContentLocationsTags($userId)
        );

        $this->purgeClient->purge($tags);
    }
}
