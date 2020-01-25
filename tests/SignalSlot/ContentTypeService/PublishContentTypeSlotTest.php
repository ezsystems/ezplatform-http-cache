<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\ContentTypeService;

use eZ\Publish\Core\SignalSlot\Signal\ContentTypeService\PublishContentTypeDraftSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\ContentTypeService\PublishContentTypeSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

class PublishContentTypeSlotTest extends AbstractSlotTest
{
    public function createSignal()
    {
        return new PublishContentTypeDraftSignal([
            'contentTypeDraftId' => 4,
        ]);
    }

    public function generateTags()
    {
        return ['ct4', 't4'];
    }

    public function getReceivedSignalClasses()
    {
        return [
            PublishContentTypeDraftSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return PublishContentTypeSlot::class;
    }
}
