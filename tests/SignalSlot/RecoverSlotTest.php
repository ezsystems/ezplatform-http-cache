<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\TrashService\RecoverSignal;

class RecoverSlotTest extends AbstractContentSlotTest
{
    protected $locationId = 43;
    protected $parentLocationId = 45;

    public function createSignal()
    {
        return new RecoverSignal(
            [
                'contentId' => $this->contentId,
                'newLocationId' => $this->locationId,
                'newParentLocationId' => $this->parentLocationId,
            ]
        );
    }

    public function generateTags()
    {
        return [
            'c' . $this->contentId,
            'r' . $this->contentId,
            'l' . $this->parentLocationId,
            'pl' . $this->parentLocationId,
        ];
    }

    public function getSlotClass()
    {
        return 'EzSystems\PlatformHttpCacheBundle\SignalSlot\RecoverSlot';
    }

    public function getReceivedSignalClasses()
    {
        return ['eZ\Publish\Core\SignalSlot\Signal\TrashService\RecoverSignal'];
    }
}
