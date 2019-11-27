<?php

/**
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
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class LocalPurgeClientTest extends TestCase
{
    public function testPurge()
    {
        $locationIds = [123, 456, 789];
        $expectedBanRequest = Request::create('http://localhost', 'PURGE');
        $expectedBanRequest->headers->set('key', 'location-123 location-456 location-789');

        $cacheStore = $this->createMock(RequestAwarePurger::class);
        $cacheStore
            ->expects($this->once())
            ->method('purgeByRequest')
            ->with($this->equalTo($expectedBanRequest));

        $purgeClient = new LocalPurgeClient($cacheStore);
        $purgeClient->purge($locationIds);
    }
}
