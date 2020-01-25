<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\LocationService\UnhideLocationSignal;

class UnhideLocationSlotTest extends AbstractContentSlotTest
{
    protected $locationId = 99;

    public function createSignal()
    {
        return new UnhideLocationSignal(
            [
                'contentId' => $this->contentId,
                'locationId' => $this->locationId,
            ]
        );
    }

    public function generateTags()
    {
        $tags = parent::generateTags();
        $tags[] = 'p' . $this->locationId;

        return $tags;
    }

    public function getSlotClass()
    {
        return 'EzSystems\PlatformHttpCacheBundle\SignalSlot\UnhideLocationSlot';
    }

    public function getReceivedSignalClasses()
    {
        return ['eZ\Publish\Core\SignalSlot\Signal\LocationService\UnhideLocationSignal'];
    }
}
