<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\UserService\CreateUserGroupSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\CreateUserGroupSlot;

class CreateUserGroupSlotTest extends AbstractPublishSlotTest
{
    public function createSignal()
    {
        return new CreateUserGroupSignal([
            'userGroupId' => $this->contentId,
        ]);
    }

    public function getReceivedSignalClasses()
    {
        return [CreateUserGroupSignal::class];
    }

    public function getSlotClass()
    {
        return CreateUserGroupSlot::class;
    }
}
