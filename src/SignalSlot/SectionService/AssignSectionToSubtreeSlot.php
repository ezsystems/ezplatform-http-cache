<?php

namespace EzSystems\PlatformHttpCacheBundle\SignalSlot\SectionService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\AbstractSlot;

/**
 * A slot handling AssignSectionToSubtreeSignal.
 */
class AssignSectionToSubtreeSlot extends AbstractSlot
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
        $tags[] = 'path-' . $signal->locationId;

        return $tags;
    }
}
