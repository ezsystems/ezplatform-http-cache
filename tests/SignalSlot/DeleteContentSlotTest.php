<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\ContentService\DeleteContentSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\DeleteContentSlot;

class DeleteContentSlotTest extends AbstractContentSlotTest
{
    const AFFECTED_LOCATION_IDS = [45, 55];

    public function createSignal()
    {
        return new DeleteContentSignal(['contentId' => $this->contentId, 'affectedLocationIds' => self::AFFECTED_LOCATION_IDS]);
    }

    public function generateTags()
    {
        $tags = parent::generateTags();

        foreach (self::AFFECTED_LOCATION_IDS as $key => $affectedLocationId) {
            $this->tagProviderMock
                ->method('getTagForPathId')
                ->willReturnCallback(static function ($arg) {
                    return 'path-' . $arg;
                });
        }

        $tags[] = 'path-45';
        $tags[] = 'path-55';

        return $tags;
    }

    public function getSlotClass()
    {
        return DeleteContentSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [DeleteContentSignal::class];
    }
}
