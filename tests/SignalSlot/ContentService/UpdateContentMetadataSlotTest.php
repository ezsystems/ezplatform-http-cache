<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\ContentService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractContentSlotTest;

class UpdateContentMetadataSlotTest extends AbstractContentSlotTest
{
    public function createSignal()
    {
        return new Signal\ContentService\UpdateContentMetadataSignal(['contentId' => $this->contentId]);
    }

    public function getSlotClass()
    {
        return SignalSlot\UpdateContentMetadataSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [Signal\ContentService\UpdateContentMetadataSignal::class];
    }
}
