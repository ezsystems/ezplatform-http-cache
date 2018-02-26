<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use EzSystems\PlatformHttpCacheBundle\SignalSlot\RemoveTranslationSlot;
use eZ\Publish\Core\SignalSlot\Signal\ContentService\RemoveTranslationSignal;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;

/**
 * Test RemoveTranslationSlot for HttpCache.
 */
class RemoveTranslationSlotTest extends AbstractPublishSlotTest
{
    protected $locationId = 43;
    protected $parentLocationId = 45;

    /**
     * Check if required signal exists due to BC.
     */
    public static function setUpBeforeClass()
    {
        if (!class_exists(RemoveTranslationSignal::class)) {
            self::markTestSkipped('RemoveTranslationSignal does not exist');
        }
    }

    public function createSignal()
    {
        return new RemoveTranslationSignal(
            [
                'contentId' => $this->contentId,
                'languageCode' => 'eng-US',
            ]
        );
    }

    public function getSlotClass()
    {
        return RemoveTranslationSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [RemoveTranslationSignal::class];
    }
}
