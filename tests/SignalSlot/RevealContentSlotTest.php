<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\ContentService\RevealContentSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\RevealContentSlot;

class RevealContentSlotTest extends AbstractPublishSlotTest
{
    public function createSignal()
    {
        if (!class_exists('eZ\Publish\Core\SignalSlot\Signal\ContentService\RevealContentSignal', false)) {
            $this->markTestSkipped("eZ\Publish\Core\SignalSlot\Signal\ContentService\RevealContentSignal doesn't exists");
        }

        return new RevealContentSignal([
            'contentId' => $this->contentId,
        ]);
    }

    public function getSlotClass()
    {
        return RevealContentSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [RevealContentSignal::class];
    }
}
