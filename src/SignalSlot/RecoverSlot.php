<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling RecoverSignal.
 */
class RecoverSlot extends AbstractContentSlot
{
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\TrashService\RecoverSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        $tags = parent::generateTags($signal);
        $tags[] = 'l' . $signal->newParentLocationId;
        $tags[] = 'pl' . $signal->newParentLocationId;

        return $tags;
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\TrashService\RecoverSignal;
    }
}
