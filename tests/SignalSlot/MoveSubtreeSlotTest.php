<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\LocationService\MoveSubtreeSignal;

class MoveSubtreeSlotTest extends AbstractContentSlotTest
{
    protected $locationId = 45;
    protected $parentLocationId = 43;
    protected $oldParentLocationId = 2;

    public function createSignal()
    {
        return new MoveSubtreeSignal(
            [
                'locationId' => $this->locationId,
                'newParentLocationId' => $this->parentLocationId,
                'oldParentLocationId' => $this->oldParentLocationId,
            ]
        );
    }

    public function generateTags()
    {
        return ['p' . $this->locationId, 'l' . $this->oldParentLocationId, 'pl' . $this->oldParentLocationId, 'l' . $this->parentLocationId, 'pl' . $this->parentLocationId];
    }

    public function getSlotClass()
    {
        return 'EzSystems\PlatformHttpCacheBundle\SignalSlot\MoveSubtreeSlot';
    }

    public function getReceivedSignalClasses()
    {
        return [MoveSubtreeSignal::class];
    }
}
