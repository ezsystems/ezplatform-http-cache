<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling MoveSubtreeSignal.
 */
class MoveSubtreeSlot extends AbstractContentSlot
{
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\LocationService\MoveSubtreeSignal $signal
     * @return array
     */
    protected function generateTags(Signal $signal)
    {
        return [
            // The tree being moved
            $this->tagProvider->getTagForPathId($signal->locationId),
            // old parent
            $this->tagProvider->getTagForLocationId($signal->oldParentLocationId),
            // old siblings
            $this->tagProvider->getTagForParentId($signal->oldParentLocationId),
            // new parent
            $this->tagProvider->getTagForLocationId($signal->newParentLocationId),
            // new siblings
            $this->tagProvider->getTagForParentId($signal->newParentLocationId),
        ];
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\LocationService\MoveSubtreeSignal;
    }
}
