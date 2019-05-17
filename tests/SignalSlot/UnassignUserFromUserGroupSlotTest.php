<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\UserService\UnAssignUserFromUserGroupSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\UnassignUserFromUserGroupSlot;

class UnassignUserFromUserGroupSlotTest extends AbstractContentSlotTest
{
    public function createSignal()
    {
        return new UnAssignUserFromUserGroupSignal(['userId' => $this->contentId, 'userGroupId' => 99]);
    }

    public function generateTags()
    {
        $this->tagProviderMock
            ->expects($this->at(0))
            ->method('getTagForContentId')
            ->willReturn('content-' . $this->contentId);
        $this->tagProviderMock
            ->expects($this->at(1))
            ->method('getTagForContentId')
            ->willReturn('content-99');
        $this->tagProviderMock
            ->method('getTagForUserContextHash')
            ->willReturn('ez-user-context-hash');

        return ['content-' . $this->contentId, 'content-99', 'ez-user-context-hash'];
    }

    public function getSlotClass()
    {
        return UnassignUserFromUserGroupSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [UnAssignUserFromUserGroupSignal::class];
    }
}
