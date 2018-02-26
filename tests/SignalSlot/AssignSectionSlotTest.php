<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\SectionService\AssignSectionSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\AssignSectionSlot;

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
