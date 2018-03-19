<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling UpdateContentSignal.
 */
class UpdateContentSlot extends AbstractSlot
{
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\ContentService\UpdateContentSignal;
    }

    /**
     * @param Signal\ContentService\UpdateContentSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        return ['content-versions-' . $signal->contentId];
    }
}
