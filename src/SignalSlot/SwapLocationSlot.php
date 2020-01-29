<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling SwapLocationSignal.
 */
class SwapLocationSlot extends AbstractContentSlot
{
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\LocationService\SwapLocationSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        return [
            'c' . $signal->content1Id,
            'p' . $signal->location1Id,
            'l' . $signal->parentLocation1Id,
            'pl' . $signal->parentLocation1Id,
            'c' . $signal->content2Id,
            'p' . $signal->location2Id,
            'l' . $signal->parentLocation2Id,
            'pl' . $signal->parentLocation2Id,

            // deprecated
            'content-' . $signal->content1Id,
            'path-' . $signal->location1Id,
            'location-' . $signal->parentLocation1Id,
            'parent-' . $signal->parentLocation1Id,
            'content-' . $signal->content2Id,
            'path-' . $signal->location2Id,
            'location-' . $signal->parentLocation2Id,
            'parent-' . $signal->parentLocation2Id,
        ];
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\LocationService\SwapLocationSignal;
    }
}
