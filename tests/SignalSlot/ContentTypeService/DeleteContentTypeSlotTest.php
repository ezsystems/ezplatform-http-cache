<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\ContentTypeService;

use eZ\Publish\Core\SignalSlot\Signal\ContentTypeService\DeleteContentTypeSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\ContentTypeService\DeleteContentTypeSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

class DeleteContentTypeSlotTest extends AbstractSlotTest
{
    public function createSignal()
    {
        return new DeleteContentTypeSignal([
            'contentTypeId' => 4,
        ]);
    }

    public function generateTags()
    {
        return ['ct4', 't4'];
    }

    public function getReceivedSignalClasses()
    {
        return [
            DeleteContentTypeSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return DeleteContentTypeSlot::class;
    }
}
