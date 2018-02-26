<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as SPILocationHandler;

class RemoveTranslationSlot extends AbstractPublishSlot
{
    /**
     * Checks if $signal is supported by this handler.
     *
     * @param \eZ\Publish\Core\SignalSlot\Signal $signal
     *
     * @return bool
     */
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\ContentService\RemoveTranslationSignal;
    }

    protected function getContentId(Signal $signal)
    {
        return $signal->contentId;
    }
}
