<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot\SectionService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\AbstractSlot;

/**
 * A slot handling UpdateSectionSignal.
 */
class UpdateSectionSlot extends AbstractSlot
{
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\SectionService\UpdateSectionSignal;
    }

    /**
     * @param Signal\SectionService\UpdateSectionSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        return ['s' . $signal->sectionId];
    }
}
