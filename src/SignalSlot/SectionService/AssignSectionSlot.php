<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot\SectionService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\AbstractPublishSlot;

/**
 * A slot handling AssignSectionSignal.
 */
class AssignSectionSlot extends AbstractPublishSlot
{
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\SectionService\AssignSectionSignal;
    }

    protected function getContentId(Signal $signal)
    {
        return $signal->contentId;
    }
}
