<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\RoleService;

use eZ\Publish\Core\SignalSlot\Signal\RoleService\UnassignRoleFromUserGroupSignal;
use eZ\Publish\Core\SignalSlot\Signal\RoleService\UnassignRoleFromUserSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\RoleService\UnassignRoleSlot;

class UnassignRoleSlotTest extends AbstractPermissionSlotTest
{
    public function createSignal()
    {
        return new UnassignRoleFromUserGroupSignal([
            'roleId' => 2,
        ]);
    }

    public function getReceivedSignalClasses()
    {
        return [
            UnassignRoleFromUserGroupSignal::class,
            UnassignRoleFromUserSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return UnassignRoleSlot::class;
    }
}
