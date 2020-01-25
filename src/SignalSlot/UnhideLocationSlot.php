<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling UnhideLocationSignal.
 */
class UnhideLocationSlot extends AbstractContentSlot
{
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\LocationService\UnhideLocationSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        $tags = parent::generateTags($signal);
        $tags[] = 'p' . $signal->locationId;

        return $tags;
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\LocationService\UnhideLocationSignal;
    }
}
