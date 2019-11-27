<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\UserService\UpdateUserSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\UpdateUserSlot;

class UpdateUserSlotTest extends AbstractPublishSlotTest
{
    public function createSignal()
    {
        return new UpdateUserSignal(['userId' => $this->contentId]);
    }

    public function getSlotClass()
    {
        return UpdateUserSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [UpdateUserSignal::class];
    }
}
