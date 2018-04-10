<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\RoleService;

use eZ\Publish\Core\SignalSlot\Signal\RoleService\RemoveRoleAssignmentSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\RoleService\RemoveRoleAssignmentSlot;

class RemoveRoleAssignmenSlotTest extends AbstractPermissionSlotTest
{
    public function createSignal()
    {
        return new RemoveRoleAssignmentSignal([
            'roleAssignmentId' => 55,
        ]);
    }

    public function getReceivedSignalClasses()
    {
        return [
            RemoveRoleAssignmentSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return RemoveRoleAssignmentSlot::class;
    }
}
