<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\UserService\UpdateUserGroupSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\UpdateUserGroupSlot;

class UpdateUserGroupSlotTest extends AbstractPublishSlotTest
{
    public function createSignal()
    {
        return new UpdateUserGroupSignal([
            'userGroupId' => $this->contentId,
        ]);
    }

    public function getSlotClass()
    {
        return UpdateUserGroupSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [UpdateUserGroupSignal::class];
    }
}
