<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use EzSystems\PlatformHttpCacheBundle\SignalSlot\CopySubtreeSlot;
use eZ\Publish\Core\SignalSlot\Signal\LocationService\CopySubtreeSignal;

class CopySubtreeSlotTest extends AbstractContentSlotTest
{
    private $subtreeId = 67;
    private $targetParentLocationId = 43;
    private $targetNewSubtreeId = 45;

    public function createSignal()
    {
        return new CopySubtreeSignal([
            'subtreeId' => $this->subtreeId,
            'targetParentLocationId' => $this->targetParentLocationId,
            'targetNewSubtreeId' => $this->targetNewSubtreeId,
        ]);
    }

    public function generateTags()
    {
        return [
            'l' . $this->targetParentLocationId,
            'pl' . $this->targetParentLocationId,
        ];
    }

    public function getSlotClass()
    {
        return CopySubtreeSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [CopySubtreeSignal::class];
    }
}
