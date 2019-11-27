<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling CopySubtreeSignal.
 */
class CopySubtreeSlot extends AbstractContentSlot
{
    protected function generateTags(Signal $signal)
    {
        /** @var \eZ\Publish\Core\SignalSlot\Signal\LocationService\CopySubtreeSignal $signal */
        return [
            // parent of the new copied tree
            'location-' . $signal->targetParentLocationId,
            // siblings of the new copied tree
            'parent-' . $signal->targetParentLocationId,
        ];
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\LocationService\CopySubtreeSignal;
    }
}
