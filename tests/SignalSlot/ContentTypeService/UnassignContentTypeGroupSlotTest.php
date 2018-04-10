<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\ContentTypeService;

use eZ\Publish\Core\SignalSlot\Signal\ContentTypeService\UnassignContentTypeGroupSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\ContentTypeService\UnassignContentTypeGroupSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

class UnassignContentTypeGroupSlotTest extends AbstractSlotTest
{
    public function createSignal()
    {
        return new UnassignContentTypeGroupSignal([
            'contentTypeGroupId' => 4,
        ]);
    }

    public function generateTags()
    {
        return ['type-group-4'];
    }

    public function getReceivedSignalClasses()
    {
        return [
            UnassignContentTypeGroupSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return UnassignContentTypeGroupSlot::class;
    }
}
