<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\LocationService\UpdateLocationSignal;

class UpdateLocationSlotTest extends AbstractContentSlotTest
{
    public function createSignal()
    {
        return new UpdateLocationSignal(['contentId' => $this->contentId]);
    }

    public function getSlotClass()
    {
        return 'EzSystems\PlatformHttpCacheBundle\SignalSlot\UpdateLocationSlot';
    }

    public function getReceivedSignalClasses()
    {
        return ['eZ\Publish\Core\SignalSlot\Signal\LocationService\UpdateLocationSignal'];
    }
}
