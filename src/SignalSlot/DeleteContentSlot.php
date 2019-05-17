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
 * A slot handling DeleteContentSignal.
 */
class DeleteContentSlot extends AbstractContentSlot
{
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\ContentService\DeleteContentSignal $signal
     * @return array
     *
     * @todo Missing parent, however it would be cleaner if kernel emitted cascading Delete Location signals on affected
     *       locations instead.
     */
    protected function generateTags(Signal $signal)
    {
        $tags = parent::generateTags($signal);
        foreach ($signal->affectedLocationIds as $locationId) {
            $tags[] = $this->tagProvider->getTagForPathId($locationId);
        }

        return $tags;
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\ContentService\DeleteContentSignal;
    }
}
