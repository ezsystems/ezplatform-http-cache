<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\ContentService;

use eZ\Publish\Core\SignalSlot\Signal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

class CreateContentDraftSlotTest extends AbstractSlotTest
{
    public function createSignal()
    {
        return new Signal\ContentService\CreateContentDraftSignal(['contentId' => 55]);
    }

    public function generateTags()
    {
        return ['cv55'];
    }

    public function getSlotClass()
    {
        return SignalSlot\CreateContentDraftSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [Signal\ContentService\CreateContentDraftSignal::class];
    }
}
