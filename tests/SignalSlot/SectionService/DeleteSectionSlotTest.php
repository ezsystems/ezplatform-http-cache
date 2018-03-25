<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\SectionService;

use eZ\Publish\Core\SignalSlot\Signal\SectionService\DeleteSectionSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\SectionService\DeleteSectionSlot;
use EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot\AbstractSlotTest;

class DeleteSectionSlotTest extends AbstractSlotTest
{
    public function createSignal()
    {
        return new DeleteSectionSignal([
            'sectionId' => 2,
        ]);
    }

    public function generateTags()
    {
        return ['section-2'];
    }

    public function getReceivedSignalClasses()
    {
        return [
            DeleteSectionSignal::class,
        ];
    }

    public function getSlotClass()
    {
        return DeleteSectionSlot::class;
    }
}
