<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\SectionService;

use eZ\Publish\Core\SignalSlot\Signal\SectionService\AssignSectionSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\SectionService\AssignSectionSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractPublishSlotTest;

class AssignSectionSlotTest extends AbstractPublishSlotTest
{
    public function createSignal()
    {
        return new AssignSectionSignal(['contentId' => $this->contentId]);
    }

    public function getSlotClass()
    {
        return AssignSectionSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [AssignSectionSignal::class];
    }
}
