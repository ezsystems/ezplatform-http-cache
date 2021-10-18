<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\RequestAwarePurger;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\LocalPurgeClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\PhpUnit\ClockMock;

class LocalPurgeClientTest extends TestCase
{
    /**
     * @group time-sensitive
     */
    public function testPurge()
    {
        ClockMock::register(Request::class);

        $locationIds = [123, 456, 789];
        $expectedBanRequest = Request::create('http://localhost', 'PURGE');
        $expectedBanRequest->headers->set('key', 'l123 l456 l789');

        $cacheStore = $this->createMock(RequestAwarePurger::class);
        $cacheStore
            ->expects($this->once())
            ->method('purgeByRequest')
            ->with($this->equalTo($expectedBanRequest));

        $purgeClient = new LocalPurgeClient($cacheStore);
        $purgeClient->purge($locationIds);
    }
}
