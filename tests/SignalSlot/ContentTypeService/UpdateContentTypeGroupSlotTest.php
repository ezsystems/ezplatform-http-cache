<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\ContentTypeService;

use eZ\Publish\Core\SignalSlot\Signal\ContentTypeService\UpdateContentTypeGroupSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\ContentTypeService\UpdateContentTypeGroupSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

class UpdateContentTypeGroupSlotTest extends AbstractSlotTest
{
    public function createSignal()
    {
        return new UpdateContentTypeGroupSignal([
            'contentTypeGroupId' => 4,
        ]);
    }

    public function generateTags()
    {
        $this->tagProviderMock
            ->expects($this->at(0))
            ->method('getTagForTypeGroupId')
            ->with(4)
            ->willReturn('type-group-4');

        return ['type-group-4'];
    }

    public function getReceivedSignalClasses()
    {
        return [
            UpdateContentTypeGroupSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return UpdateContentTypeGroupSlot::class;
    }
}
