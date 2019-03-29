<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\ContentService\HideContentSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\HideContentSlot;

class HideContentSlotTest extends AbstractPublishSlotTest
{
    public function createSignal()
    {
        if (!class_exists('eZ\Publish\Core\SignalSlot\Signal\ContentService\HideContentSignal', false)) {
            $this->markTestSkipped("eZ\Publish\Core\SignalSlot\Signal\ContentService\HideContentSignal doesn't exists");
        }

        return new HideContentSignal([
            'contentId' => $this->contentId,
        ]);
    }

    public function getSlotClass()
    {
        return HideContentSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [HideContentSignal::class];
    }
}
