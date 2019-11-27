<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\UserService\CreateUserSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\CreateUserSlot;

class CreateUserSlotTest extends AbstractPublishSlotTest
{
    public function createSignal()
    {
        return new CreateUserSignal([
            'userId' => $this->contentId,
        ]);
    }

    public function getReceivedSignalClasses()
    {
        return [CreateUserSignal::class];
    }

    public function getSlotClass()
    {
        return CreateUserSlot::class;
    }
}
