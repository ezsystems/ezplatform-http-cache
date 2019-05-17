<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\ContentService\CopyContentSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\CopyContentSlot;

class CopyContentSlotTest extends AbstractContentSlotTest
{
    protected $parentLocationId = 59;

    public function createSignal()
    {
        return new CopyContentSignal(['dstContentId' => $this->contentId, 'dstParentLocationId' => $this->parentLocationId]);
    }

    public function generateTags()
    {
        $this->tagProviderMock
            ->expects($this->at(0))
            ->method('getTagForContentId')
            ->with($this->contentId)
            ->willReturn("content-{$this->contentId}");

        $this->tagProviderMock
            ->expects($this->at(1))
            ->method('getTagForLocationId')
            ->with($this->parentLocationId)
            ->willReturn("location-{$this->parentLocationId}");

        $this->tagProviderMock
            ->expects($this->at(2))
            ->method('getTagForPathId')
            ->with($this->parentLocationId)
            ->willReturn("path-{$this->parentLocationId}");

        return ['content-' . $this->contentId, 'location-' . $this->parentLocationId, 'path-' . $this->parentLocationId];
    }

    public function getSlotClass()
    {
        return CopyContentSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [CopyContentSignal::class];
    }
}
