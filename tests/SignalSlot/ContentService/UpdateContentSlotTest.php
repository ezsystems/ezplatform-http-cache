<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\ContentService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

class UpdateContentSlotTest extends AbstractSlotTest
{
    public function createSignal()
    {
        return new Signal\ContentService\UpdateContentSignal(['contentId' => 55]);
    }

    public function generateTags()
    {
        $tag = 'content-versions-55';
        $this->tagProviderMock
            ->method('getTagForContentVersions')
            ->with(55)
            ->willReturn($tag);

        return [$tag];
    }

    public function getSlotClass()
    {
        return SignalSlot\UpdateContentSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [Signal\ContentService\UpdateContentSignal::class];
    }
}
