<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot\SectionService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\AbstractSlot;

/**
 * A slot handling DeleteSectionSignal.
 */
class DeleteSectionSlot extends AbstractSlot
{
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\SectionService\DeleteSectionSignal;
    }

    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\SectionService\DeleteSectionSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        return ['section-' . $signal->sectionId];
    }
}
