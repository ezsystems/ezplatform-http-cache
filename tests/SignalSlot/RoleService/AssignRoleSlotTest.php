<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\RoleService;

use eZ\Publish\Core\SignalSlot\Signal\RoleService\AssignRoleToUserGroupSignal;
use eZ\Publish\Core\SignalSlot\Signal\RoleService\AssignRoleToUserSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\RoleService\AssignRoleSlot;

class AssignRoleSlotTest extends AbstractPermissionSlotTest
{
    public function createSignal()
    {
        return new AssignRoleToUserGroupSignal([
            'roleId' => 2,
        ]);
    }

    public function getReceivedSignalClasses()
    {
        return [
            AssignRoleToUserGroupSignal::class,
            AssignRoleToUserSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return AssignRoleSlot::class;
    }
}
