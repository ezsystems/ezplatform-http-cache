<?php

/**
 * File containing the FOSPurgeClientTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\VarnishPurgeClient;
use FOS\HttpCache\ProxyClient\ProxyClient;
use FOS\HttpCacheBundle\CacheManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VarnishPurgeClientTest extends TestCase
{
    /** @var \FOS\HttpCacheBundle\CacheManager */
    private $cacheManager;

    /** @var \EzSystems\PlatformHttpCacheBundle\PurgeClient\VarnishPurgeClient */
    private $purgeClient;

    protected function setUp()
    {
        parent::setUp();
        $this->cacheManager = $this->getMockBuilder(CacheManager::class)
            ->setConstructorArgs(
                array(
                    $this->createMock(ProxyClient::class),
                    $this->createMock(
                        UrlGeneratorInterface::class
                    ),
                )
            )
            ->getMock();

        $this->purgeClient = new VarnishPurgeClient(
            $this->cacheManager,
        );
    }

    public function testPurgeNoLocationIds()
    {
        $this->cacheManager
            ->expects($this->never())
            ->method('invalidate');

        $this->purgeClient->purge([]);
    }

    /**
     * @dataProvider purgeTestProvider
     */
    public function testPurge(array $locationIds)
    {
        $keys = array_map(static function ($id) {
            return "location-$id";
        },
            $locationIds
        );

        $this->cacheManager
            ->expects($this->once())
            ->method('invalidateTags')
            ->with($keys);

        $this->purgeClient->purge($keys);
    }

    public function purgeTestProvider()
    {
        return [
            [[123]],
            [[123, 456]],
            [[123, 456, 789]],
        ];
    }

    public function testPurgeAll()
    {
        $this->cacheManager
            ->expects($this->once())
            ->method('invalidateTags')
            ->with(['ez-all']);

        $this->purgeClient->purgeAll();
    }
}
