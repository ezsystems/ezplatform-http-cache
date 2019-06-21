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

use EzSystems\PlatformHttpCacheBundle\PurgeClient\LocalPurgeClient;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\VarnishPurgeClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Toflar\Psr6HttpCacheStore\Psr6StoreInterface;

class LocalPurgeClientTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|\Toflar\Psr6HttpCacheStore\Psr6StoreInterface */
    private $store;

    protected function setUp()
    {
        parent::setUp();
        $this->store = $this->createMock(Psr6StoreInterface::class);
    }

    public function testPurge()
    {
        $keys = array_map(static function ($id) {
            return "location-$id";
        },
            [123, 456, 789]
        );

        $this->store
            ->expects($this->once())
            ->method('invalidateTags')
            ->with($keys);

        $purgeClient = new LocalPurgeClient($this->store);
        $purgeClient->purge($keys);
    }
}
