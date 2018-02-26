<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\ObjectStateService\SetContentStateSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\SetContentStateSlot;

class SetContentStateSlotTest extends AbstractPublishSlotTest
{
    public function createSignal()
    {
        return new SetContentStateSignal(['contentId' => $this->contentId]);
    }

    public function getSlotClass()
    {
        return SetContentStateSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [SetContentStateSignal::class];
    }
}
