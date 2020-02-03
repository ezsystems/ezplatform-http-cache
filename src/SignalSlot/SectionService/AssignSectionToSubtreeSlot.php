<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot\SectionService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\AbstractContentSlot;

/**
 * A slot handling AssignSectionToSubtreeSignal.
 */
class AssignSectionToSubtreeSlot extends AbstractContentSlot
{
    /**
     * {@inheritdoc}
     */
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\SectionService\AssignSectionToSubtreeSignal;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateTags(Signal $signal)
    {
        $tags = parent::generateTags($signal);
        $tags[] = 'p' . $signal->locationId;

        return $tags;
    }
}
