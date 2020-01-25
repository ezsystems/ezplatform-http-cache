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
            $tags = ['c' . $this->contentId, 'r' . $this->contentId];
        }

        if ($this->locationId) {
            // self(s)
            $tags[] = 'l' . $this->locationId;
            // children
            $tags[] = 'pl' . $this->locationId;
            // reverse location relations
            $tags[] = 'rl' . $this->locationId;
        }

        if ($this->parentLocationId) {
            // parent(s)
            $tags[] = 'l' . $this->parentLocationId;
            // siblings
            $tags[] = 'pl' . $this->parentLocationId;
        }

        return $tags;
    }
}
