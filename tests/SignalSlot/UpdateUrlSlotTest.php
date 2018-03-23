<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal\URLService\UpdateUrlSignal;
use EzSystems\PlatformHttpCacheBundle\SignalSlot\UpdateUrlSlot;
use eZ\Publish\SPI\Persistence\URL\Handler;

class UpdateUrlSlotTest extends AbstractSlotTest
{
    const URL_ID = 63;

    const CONTENT_IDS = [
       2, 3, 5, 7, 11
    ];

    /** @var \eZ\Publish\SPI\Persistence\URL\Handler|\PHPUnit_Framework_MockObject_MockObject */
    private $spiUrlHandlerMock = null;

    /**
     * Check if required signal exists due to BC.
     */
    public static function setUpBeforeClass()
    {
        if (!class_exists(UpdateUrlSignal::class)) {
            self::markTestSkipped('UpdateUrlSignal does not exist');
        }
    }

    protected function createSlot()
    {
        $class = $this->getSlotClass();
        if ($this->spiUrlHandlerMock === null) {
            $this->spiUrlHandlerMock = $this->createMock(Handler::class);
            $this->spiUrlHandlerMock
                ->expects($this->any())
                ->method('findUsages')
                ->with(self::URL_ID)
                ->willReturn(self::CONTENT_IDS);
        }

        return new $class($this->purgeClientMock, $this->spiUrlHandlerMock);
    }

    public function createSignal()
    {
        return new UpdateUrlSignal([
            'urlId' => self::URL_ID,
            'urlChanged' => true
        ]);
    }

    public function generateTags()
    {
        return array_map(function($id) {
            return 'content-' . $id;
        }, self::CONTENT_IDS);
    }

    public function getReceivedSignalClasses()
    {
        return [
            UpdateUrlSignal::class
        ];
    }

    public function getSlotClass()
    {
        return UpdateUrlSlot::class;
    }
}
