<?php

/**
 * File containing the LocalPurgeClientTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\PurgeClient;

/**
 * Avoid test failure caused by time passing between generating expected & actual object.
 *
 * @return int
 */
function time()
{
    return 1417624982;
}

namespace eZ\Publish\Core\MVC\Symfony\Cache\Tests;

use EzSystems\PlatformHttpCacheBundle\RequestAwarePurger;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\LocalPurgeClient;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class LocalPurgeClientTest extends TestCase
{
    public function testPurge()
    {
        $locationIds = array(123, 456, 789);
        $expectedBanRequest = Request::create('http://localhost', 'PURGE');
        $expectedBanRequest->headers->set('key', 'location-123 location-456 location-789');

        $cacheStore = $this->createMock(RequestAwarePurger::class);
        $cacheStore
            ->expects($this->once())
            ->method('purgeByRequest')
            ->with($this->equalTo($expectedBanRequest));

        $tagProviderMock = $this->createMock(TagProviderInterface::class);

        $tagProviderMock
            ->expects($this->at(0))
            ->method('getTagForLocationId')
            ->with(123)
            ->willReturn('location-123');

        $tagProviderMock
            ->expects($this->at(1))
            ->method('getTagForLocationId')
            ->with(456)
            ->willReturn('location-456');

        $tagProviderMock
            ->expects($this->at(2))
            ->method('getTagForLocationId')
            ->with(789)
            ->willReturn('location-789');

        $purgeClient = new LocalPurgeClient($cacheStore, $tagProviderMock);
        $purgeClient->purge($locationIds);
    }
}
