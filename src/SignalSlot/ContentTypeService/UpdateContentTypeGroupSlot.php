<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot\ContentTypeService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\AbstractSlot;

/**
 * A slot handling UpdateContentTypeGroupSignal.
 */
class UpdateContentTypeGroupSlot extends AbstractSlot
{
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\ContentTypeService\UpdateContentTypeGroupSignal;
    }

    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\ContentTypeService\UpdateContentTypeGroupSignal $signal
     */
    protected function generateTags(Signal $signal)
    {
        // @todo Do we need to purge type? (aka: do we need to tag group on type)
        return ['type-group-' . $signal->contentTypeGroupId];
    }
}
