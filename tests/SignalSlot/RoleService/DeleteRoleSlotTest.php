<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\RoleService;

use eZ\Publish\Core\SignalSlot\Signal\RoleService\DeleteRoleSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\RoleService\DeleteRoleSlot;

class DeleteRoleSlotTest extends AbstractPermissionSlotTest
{
    public function createSignal()
    {
        return new DeleteRoleSignal([
            'roleId' => 2,
        ]);
    }

    public function getReceivedSignalClasses()
    {
        return [
            DeleteRoleSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return DeleteRoleSlot::class;
    }
}
