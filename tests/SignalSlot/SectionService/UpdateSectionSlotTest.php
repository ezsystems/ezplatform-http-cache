<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\SectionService;

use eZ\Publish\Core\SignalSlot\Signal\SectionService\UpdateSectionSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\SectionService\UpdateSectionSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

class UpdateSectionSlotTest extends AbstractSlotTest
{
    public function createSignal()
    {
        return new UpdateSectionSignal([
            'sectionId' => 2,
        ]);
    }

    public function generateTags()
    {
        return ['s2'];
    }

    public function getReceivedSignalClasses()
    {
        return [
            UpdateSectionSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return UpdateSectionSlot::class;
    }
}
