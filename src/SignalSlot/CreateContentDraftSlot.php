<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling CreateContentDraftSignal.
 */
class CreateContentDraftSlot extends AbstractSlot
{
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\ContentService\CreateContentDraftSignal;
    }

    /**
     * @param Signal\ContentService\CreateContentDraftSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        return ['cv' . $signal->contentId];
    }
}
