<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * An abstract HTTP Cache purging Slot that purges cache for a Content.
 *
 * Will by default use the contentId property of the signal object, as it is the most common. Set generateTags()
 * method in case of different signals or need to clear more then the defaults.
 */
abstract class AbstractContentSlot extends AbstractSlot
{
    /**
     * Default provides tags to clear content, relation, location, parent and sibling cache.
     *
     * Overload and call parent for tree operations where you also need to clear whole path.
     *
     * @param \eZ\Publish\Core\SignalSlot\Signal $signal
     *
     * @return array
     */
    protected function generateTags(Signal $signal)
    {
        $tags = [];

        if (isset($signal->contentId)) {
            // self in all forms (also without locations)
            $tags[] = $this->tagProvider->getTagForContentId($signal->contentId);
            // reverse relations
            $tags[] = $this->tagProvider->getTagForRelationId($signal->contentId);
        }

        if (isset($signal->locationId)) {
            // self
            $tags[] = $this->tagProvider->getTagForLocationId($signal->locationId);
            // direct children
            $tags[] = $this->tagProvider->getTagForParentId($signal->locationId);
            // reverse location relations
            $tags[] = $this->tagProvider->getTagForRelationLocationId($signal->locationId);
        }

        if (isset($signal->parentLocationId)) {
            // direct parent
            $tags[] = $this->tagProvider->getTagForLocationId($signal->parentLocationId);
            // direct siblings
            $tags[] = $this->tagProvider->getTagForParentId($signal->parentLocationId);
        }

        return $tags;
    }
}
