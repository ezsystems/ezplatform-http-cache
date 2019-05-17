<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

abstract class AbstractContentSlotTest extends AbstractSlotTest
{
    protected $contentId = 42;
    protected $locationId = null;
    protected $parentLocationId = null;

    /**
     * @return array
     */
    public function generateTags()
    {
        $tags = [];

        if ($this->contentId) {
            $this->tagProviderMock
                ->method('getTagForContentId')
                ->willReturnCallback(static function ($arg) {
                    return 'content-' . $arg;
                });
            $tags[] = 'content-' . $this->contentId;

            $this->tagProviderMock
                ->method('getTagForRelationId')
                ->willReturnCallback(static function ($arg) {
                    return 'relation-' . $arg;
                });
            $tags[] = 'relation-' . $this->contentId;
        }

        if ($this->locationId) {
            // self(s)
            $this->tagProviderMock
                ->method('getTagForLocationId')
                ->willReturnCallback(static function ($arg) {
                    return 'location-' . $arg;
                });
            $tags[] = 'location-' . $this->locationId;

            // children
            $this->tagProviderMock
                ->method('getTagForParentId')
                ->willReturnCallback(static function ($arg) {
                    return 'parent-' . $arg;
                });
            $tags[] = 'parent-' . $this->locationId;

            // reverse location relations
            $this->tagProviderMock
                ->method('getTagForRelationLocationId')
                ->willReturnCallback(static function ($arg) {
                    return 'relation-location-' . $arg;
                });
            $tags[] = 'relation-location-' . $this->locationId;
        }

        if ($this->parentLocationId) {
            // parent(s)
            $this->tagProviderMock
                ->method('getTagForLocationId')
                ->willReturnCallback(static function ($arg) {
                    return 'location-' . $arg;
                });
            $tags[] = 'location-' . $this->parentLocationId;

            // siblings
            $this->tagProviderMock
                ->method('getTagForParentId')
                ->willReturnCallback(static function ($arg) {
                    return 'parent-' . $arg;
                });
            $tags[] = 'parent-' . $this->parentLocationId;
        }

        return $tags;
    }
}
