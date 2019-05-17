<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
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
            $this->tagProviderMock->getTagForContentId($this->contentId),
            $this->tagProviderMock->getTagForRelationId($this->contentId),
            $this->tagProviderMock->getTagForLocationId($this->parentLocationId),
            $this->tagProviderMock->getTagForParentId($this->parentLocationId),
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
