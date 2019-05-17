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
 * A slot handling SwapLocationSignal.
 */
class SwapLocationSlot extends AbstractContentSlot
{
    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\LocationService\SwapLocationSignal $signal
     * @return array
     */
    protected function generateTags(Signal $signal)
    {
        return [
            $this->tagProvider->getTagForContentId($signal->content1Id),
            $this->tagProvider->getTagForPathId($signal->location1Id),
            $this->tagProvider->getTagForLocationId($signal->parentLocation1Id),
            $this->tagProvider->getTagForParentId($signal->parentLocation1Id),
            $this->tagProvider->getTagForContentId($signal->content2Id),
            $this->tagProvider->getTagForPathId($signal->location2Id),
            $this->tagProvider->getTagForLocationId($signal->parentLocation2Id),
            $this->tagProvider->getTagForParentId($signal->parentLocation2Id),
        ];
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\LocationService\SwapLocationSignal;
    }
}
