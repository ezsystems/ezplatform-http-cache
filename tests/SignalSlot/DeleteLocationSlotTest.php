<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\LocationService\DeleteLocationSignal;

class DeleteLocationSlotTest extends AbstractContentSlotTest
{
    protected $locationId = 45;
    protected $parentLocationId = 43;

    public function createSignal()
    {
        return new DeleteLocationSignal(
            [
                'contentId' => $this->contentId,
                'locationId' => $this->locationId,
                'parentLocationId' => $this->parentLocationId,
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
        return 'EzSystems\PlatformHttpCacheBundle\SignalSlot\DeleteLocationSlot';
    }

    public function getReceivedSignalClasses()
    {
        return ['eZ\Publish\Core\SignalSlot\Signal\LocationService\DeleteLocationSignal'];
    }
}
