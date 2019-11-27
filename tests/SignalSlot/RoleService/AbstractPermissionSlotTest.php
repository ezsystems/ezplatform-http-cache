<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\RoleService;

use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

abstract class AbstractPermissionSlotTest extends AbstractSlotTest
{
    public function generateTags()
    {
        return ['ez-user-context-hash'];
    }
}
