<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * A slot handling PublishVersionSignal.
 */
class PublishVersionSlot extends AbstractPublishSlot
{
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\ContentService\PublishVersionSignal;
    }

    protected function getContentId(Signal $signal)
    {
        return $signal->contentId;
    }
}
