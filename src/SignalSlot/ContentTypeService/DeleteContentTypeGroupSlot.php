<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot\ContentTypeService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\AbstractSlot;

/**
 * A slot handling DeleteContentTypeGroupSignal.
 */
class DeleteContentTypeGroupSlot extends AbstractSlot
{
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\ContentTypeService\DeleteContentTypeGroupSignal;
    }

    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\ContentTypeService\DeleteContentTypeGroupSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        return ['tg' . $signal->contentTypeGroupId];
    }
}
