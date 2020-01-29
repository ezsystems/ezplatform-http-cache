<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\ContentService\CopyContentSignal;

class CopyContentSlotTest extends AbstractContentSlotTest
{
    protected $parentLocationId = 59;

    public function createSignal()
    {
        return new CopyContentSignal(['dstContentId' => $this->contentId, 'dstParentLocationId' => $this->parentLocationId]);
    }

    public function generateTags()
    {
        return [
            'c' . $this->contentId,
            'l' . $this->parentLocationId,
            'p' . $this->parentLocationId,

            'content-' . $this->contentId,
            'location-' . $this->parentLocationId,
            'path-' . $this->parentLocationId,
        ];
    }

    public function getSlotClass()
    {
        return 'EzSystems\PlatformHttpCacheBundle\SignalSlot\CopyContentSlot';
    }

    public function getReceivedSignalClasses()
    {
        return ['eZ\Publish\Core\SignalSlot\Signal\ContentService\CopyContentSignal'];
    }
}
