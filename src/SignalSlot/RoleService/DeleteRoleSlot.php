<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot\RoleService;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling delete role signals.
 */
class DeleteRoleSlot extends AbstractPermissionSlot
{
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\RoleService\DeleteRoleSignal;
    }
}
