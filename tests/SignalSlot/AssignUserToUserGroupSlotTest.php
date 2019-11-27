<?php

/**
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
