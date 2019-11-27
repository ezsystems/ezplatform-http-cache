<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\LocationService\CreateLocationSignal;

class CreateLocationSlotTest extends AbstractContentSlotTest
{
    protected $locationId = 45;
    protected $parentLocationId = 43;

    public function createSignal()
    {
        return new CreateLocationSignal(['contentId' => $this->contentId, 'locationId' => $this->locationId, 'parentLocationId' => $this->parentLocationId]);
    }

    public function getSlotClass()
    {
        return 'EzSystems\PlatformHttpCacheBundle\SignalSlot\CreateLocationSlot';
    }

    public function getReceivedSignalClasses()
    {
        return [CreateLocationSignal::class];
    }
}
