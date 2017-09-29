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
class RemoveTranslationSlotTest extends AbstractContentSlotTest
{
    protected $locationId = 43;
    protected $parentLocationId = 45;

    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Location\Handler|\PHPUnit_Framework_MockObject_MockObject
     */
    private $locationHandlerMock;

    /**
     * Check if required signal exists due to BC.
     */
    public static function setUpBeforeClass()
    {
        if (!class_exists(RemoveTranslationSignal::class)) {
            self::markTestSkipped('RemoveTranslationSignal does not exist');
        }
    }

    public function setUp()
    {
        $this->locationHandlerMock = $this->createMock(LocationHandler::class);
        parent::setUp();
    }

    /**
     * @dataProvider getReceivedSignals
     * @param \eZ\Publish\Core\SignalSlot\Signal $signal
     */
    public function testReceivePurgesCacheForTags($signal)
    {
        $this->locationHandlerMock
            ->expects($this->once())
            ->method('loadLocationsByContent')
            ->with($this->contentId)
            ->willReturn(
                [
                    new Location(
                        [
                            'id' => $this->locationId,
                            'parentId' => $this->parentLocationId,
                        ]
                    ),
                ]
            );

        parent::testReceivePurgesCacheForTags($signal);
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

    public function generateTags()
    {
        return [
            'content-' . $this->contentId,
            'relation-' . $this->contentId,
            'location-' . $this->locationId,
            'parent-' . $this->locationId,
            'location-' . $this->parentLocationId,
            'parent-' . $this->parentLocationId,
        ];
    }

    public function getSlotClass()
    {
        return RemoveTranslationSlot::class;
    }

    public function getReceivedSignalClasses()
    {
        return [RemoveTranslationSignal::class];
    }

    protected function createSlot()
    {
        return new RemoveTranslationSlot(
            $this->purgeClientMock,
            $this->locationHandlerMock
        );
    }
}
