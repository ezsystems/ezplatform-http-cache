<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling HideContentSlot.
 */
class HideContentSlot extends AbstractPublishSlot
{
    /**
     * {@inheritdoc}
     */
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\ContentService\HideContentSignal;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContentId(Signal $signal)
    {
        return $signal->contentId;
    }
}
