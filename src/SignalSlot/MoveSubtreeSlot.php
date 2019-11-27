<?php

/**
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
     */
    protected function generateTags(Signal $signal)
    {
        return [
            // The tree being moved
            'path-' . $signal->locationId,
            // old parent
            'location-' . $signal->oldParentLocationId,
            // old siblings
            'parent-' . $signal->oldParentLocationId,
            // new parent
            'location-' . $signal->newParentLocationId,
            // new siblings
            'parent-' . $signal->newParentLocationId,
        ];
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\LocationService\MoveSubtreeSignal;
    }
}
