<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\ContentService\DeleteContentSignal;

class DeleteContentSlotTest extends AbstractContentSlotTest
{
    public function createSignal()
    {
        return new DeleteContentSignal(['contentId' => $this->contentId, 'affectedLocationIds' => [45, 55]]);
    }

    public function generateTags()
    {
        $tags = parent::generateTags();
        $tags[] = 'p45';
        $tags[] = 'path-45';
        $tags[] = 'p55';
        $tags[] = 'path-55';

        return $tags;
    }

    public function getSlotClass()
    {
        return 'EzSystems\PlatformHttpCacheBundle\SignalSlot\DeleteContentSlot';
    }

    public function getReceivedSignalClasses()
    {
        return ['eZ\Publish\Core\SignalSlot\Signal\ContentService\DeleteContentSignal'];
    }
}
