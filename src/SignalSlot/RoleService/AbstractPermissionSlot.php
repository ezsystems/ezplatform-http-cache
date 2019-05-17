<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot\RoleService;

use EzSystems\PlatformHttpCacheBundle\SignalSlot\AbstractSlot;
use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A abstract slot covering common functions needed for permission based slots.
 */
abstract class AbstractPermissionSlot extends AbstractSlot
{
    protected function generateTags(Signal $signal)
    {
        // On permission changes we simply clear user hash cache so next requests will vary on updated hash if a given
        // user was afected. This avoids us having to clear all cache as most or some users might still have same cache.
        return [$this->tagProvider->getTagForUserContextHash()];
    }
}
