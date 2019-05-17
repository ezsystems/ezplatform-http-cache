<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\ContentTypeService;

use eZ\Publish\Core\SignalSlot\Signal\ContentTypeService\DeleteContentTypeGroupSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\ContentTypeService\DeleteContentTypeGroupSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

class DeleteContentTypeGroupSlotTest extends AbstractSlotTest
{
    public function createSignal()
    {
        return new DeleteContentTypeGroupSignal([
            'contentTypeGroupId' => 4,
        ]);
    }

    public function generateTags()
    {
        $groupId = 4;
        $tag = 'type-group-' . $groupId;
        $this->tagProviderMock
            ->method('getTagForTypeGroupId')
            ->with($groupId)
            ->willReturn($tag);

        return [$tag];
    }

    public function getReceivedSignalClasses()
    {
        return [
            DeleteContentTypeGroupSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return DeleteContentTypeGroupSlot::class;
    }
}
