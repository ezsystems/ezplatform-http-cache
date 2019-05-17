<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\UserService\AssignUserToUserGroupSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\AssignUserToUserGroupSlot;

class AssignUserToUserGroupSlotTest extends AbstractContentSlotTest
{
    public function createSignal()
    {
        return new AssignUserToUserGroupSignal(['userId' => $this->contentId, 'userGroupId' => 99]);
    }

    public function generateTags()
    {
        $this->tagProviderMock
            ->expects($this->at(0))
            ->method('getTagForContentId')
            ->with($this->contentId)
            ->willReturn("content-{$this->contentId}");

        $this->tagProviderMock
            ->expects($this->at(1))
            ->method('getTagForContentId')
            ->with(99)
            ->willReturn('content-99');

        $this->tagProviderMock
            ->expects($this->at(2))
            ->method('getTagForUserContextHash')
            ->willReturn('ez-user-context-hash');

        return ['content-' . $this->contentId, 'content-99', 'ez-user-context-hash'];
    }

    public function getSlotClass()
    {
        return AssignUserToUserGroupSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [AssignUserToUserGroupSignal::class];
    }
}
