<?php

/**
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
            $tags[] = 'c' . $signal->contentId;
            // reverse relations
            $tags[] = 'r' . $signal->contentId;

            // deprecated
            $tags[] = 'content-' . $signal->contentId;
            $tags[] = 'relation-' . $signal->contentId;
        }

        if (isset($signal->locationId)) {
            // self
            $tags[] = 'l' . $signal->locationId;
            // direct children
            $tags[] = 'pl' . $signal->locationId;
            // reverse location relations
            $tags[] = 'rl' . $signal->locationId;

            // deprecated
            $tags[] = 'location-' . $signal->locationId;
            $tags[] = 'parent-' . $signal->locationId;
            $tags[] = 'relation-location-' . $signal->locationId;
        }

        if (isset($signal->parentLocationId)) {
            // direct parent
            $tags[] = 'l' . $signal->parentLocationId;
            // direct siblings
            $tags[] = 'pl' . $signal->parentLocationId;

            // deprecated
            $tags[] = 'location-' . $signal->parentLocationId;
            $tags[] = 'parent-' . $signal->parentLocationId;
        }

        return $tags;
    }
}
