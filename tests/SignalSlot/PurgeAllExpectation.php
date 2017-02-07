<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

/**
 * If a test implements this interface, it will be verified that purgeAll() is never called.
 */
interface PurgeAllExpectation
{
    public function testReceivePurgesAll($signal);
}
