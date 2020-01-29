<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

abstract class AbstractContentSlotTest extends AbstractSlotTest
{
    protected $contentId = 42;
    protected $locationId = null;
    protected $parentLocationId = null;

    /**
     * @return array
     */
    public function generateTags()
    {
        $tags = [];
        if ($this->contentId) {
            // self in all forms (also without locations)
            $tags[] = 'c' . $this->contentId;
            // reverse relations
            $tags[] = 'r' . $this->contentId;

            // deprecated
            $tags[] = 'content-' . $this->contentId;
            $tags[] = 'relation-' . $this->contentId;
        }

        if ($this->locationId) {
            // self(s)
            $tags[] = 'l' . $this->locationId;
            // children
            $tags[] = 'pl' . $this->locationId;
            // reverse location relations
            $tags[] = 'rl' . $this->locationId;

            // deprecated
            $tags[] = 'location-' . $this->locationId;
            $tags[] = 'parent-' . $this->locationId;
            $tags[] = 'relation-location-' . $this->locationId;
        }

        if ($this->parentLocationId) {
            // parent(s)
            $tags[] = 'l' . $this->parentLocationId;
            // siblings
            $tags[] = 'pl' . $this->parentLocationId;

            // deprecated
            $tags[] = 'location-' . $this->parentLocationId;
            $tags[] = 'parent-' . $this->parentLocationId;
        }

        return $tags;
    }
}
