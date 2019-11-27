<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling CreateLocationSignal.
 */
class CreateLocationSlot extends AbstractContentSlot
{
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal $signal
     */
    public function supports(Signal $signal)
    {
        return $signal instanceof Signal\LocationService\CreateLocationSignal;
    }
}
