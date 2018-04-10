<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\RoleService;

use eZ\Publish\Core\SignalSlot\Signal\RoleService\PublishRoleDraftSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\RoleService\PublishRoleDraftSlot;

class PublishRoleDraftSlotTest extends AbstractPermissionSlotTest
{
    public function createSignal()
    {
        return new PublishRoleDraftSignal([
            'roleId' => 2,
        ]);
    }

    public function getReceivedSignalClasses()
    {
        return [
            PublishRoleDraftSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return PublishRoleDraftSlot::class;
    }
}
