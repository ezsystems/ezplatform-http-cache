<?php

/**
 * File containing the FOSPurgeClientTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\VarnishPurgeClient;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use FOS\HttpCache\ProxyClient\ProxyClientInterface;
use FOS\HttpCacheBundle\CacheManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VarnishPurgeClientTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheManager;

    /**
     * @var VarnishPurgeClient
     */
    private $purgeClient;

    /**
     * @var ConfigResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configResolver;

    protected function setUp()
    {
        parent::setUp();
        $this->cacheManager = $this->getMockBuilder(CacheManager::class)
            ->setConstructorArgs(
                array(
                    $this->createMock(ProxyClientInterface::class),
                    $this->createMock(
                        UrlGeneratorInterface::class
                    ),
                )
            )
            ->getMock();
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->purgeClient = new VarnishPurgeClient(
            $this->cacheManager,
            $this->configResolver
        );
    }

    public function testPurgeNoLocationIds()
    {
        $this->cacheManager
            ->expects($this->never())
            ->method('invalidate');

        $this->purgeClient->purge(array());
    }

    public function testPurgeOneLocationId()
    {
        $locationId = 123;

        $this->cacheManager
            ->expects($this->once())
            ->method('invalidatePath')
            ->with('/', ['key' => "location-$locationId", 'Host' => 'varnishpurgehost']);

        $this->configResolver
            ->expects($this->exactly(1))
            ->method('hasParameter')
            ->withAnyParameters()
            ->willReturn(true);

        $this->configResolver
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->withConsecutive(['http_cache.purge_servers'], [VarnishPurgeClient::INVALIDATE_TOKEN_PARAM])
            ->willReturnOnConsecutiveCalls(['https://varnishpurgehost'], null);

        $this->purgeClient->purge($locationId);
    }

    public function testPurgeOneLocationIdWithAuthHeaderAndKey()
    {
        $locationId = 123;
        $tokenName = 'x-purge-token';
        $token = 'secret-token-key';

        $this->cacheManager
            ->expects($this->once())
            ->method('invalidatePath')
            ->with('/', ['key' => "location-$locationId", 'Host' => 'varnishpurgehost', $tokenName => $token]);

        $this->configResolver
            ->expects($this->exactly(1))
            ->method('hasParameter')
            ->withAnyParameters()
            ->willReturn(true);

        $this->configResolver
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->withConsecutive(['http_cache.purge_servers'], [VarnishPurgeClient::INVALIDATE_TOKEN_PARAM])
            ->willReturnOnConsecutiveCalls(['https://varnishpurgehost'], $token);

        $this->purgeClient->purge($locationId);
    }

    /**
     * @dataProvider purgeTestProvider
     */
    public function testPurge(array $locationIds)
    {
        foreach ($locationIds as $key => $locationId) {
            $this->configResolver
                ->expects($this->at($key * 3))
                ->method('getParameter')
                ->with('http_cache.purge_servers')
                ->willReturn(['https://varnishpurgehost']);

            $this->configResolver
                ->expects($this->at($key * 3 + 1))
                ->method('hasParameter')
                ->with(VarnishPurgeClient::INVALIDATE_TOKEN_PARAM)
                ->willReturn(true);

            $this->configResolver
                ->expects($this->at($key * 3 + 2))
                ->method('getParameter')
                ->with(VarnishPurgeClient::INVALIDATE_TOKEN_PARAM)
                ->willReturn(null);

            $this->cacheManager
                ->expects($this->at($key))
                ->method('invalidatePath')
                ->with('/', ['key' => "location-$locationId", 'Host' => 'varnishpurgehost']);
        }

        $this->purgeClient->purge($locationIds);
    }

    /**
     * @dataProvider purgeTestProvider
     */
    public function testPurgeWithAuthHeaderAndKey(array $locationIds = [])
    {
        $tokenName = 'x-purge-token';
        $token = 'secret-token-key';

        foreach ($locationIds as $key => $locationId) {
            $this->configResolver
                ->expects($this->at($key * 3))
                ->method('getParameter')
                ->with('http_cache.purge_servers')
                ->willReturn(['https://varnishpurgehost']);

            $this->configResolver
                ->expects($this->at($key * 3 + 1))
                ->method('hasParameter')
                ->with(VarnishPurgeClient::INVALIDATE_TOKEN_PARAM)
                ->willReturn(true);

            $this->configResolver
                ->expects($this->at($key * 3 + 2))
                ->method('getParameter')
                ->with(VarnishPurgeClient::INVALIDATE_TOKEN_PARAM)
                ->willReturn($token);

            $this->cacheManager
                ->expects($this->at($key))
                ->method('invalidatePath')
                ->with('/', ['key' => "location-$locationId", 'Host' => 'varnishpurgehost', $tokenName => $token]);
        }

        $this->purgeClient->purge($locationIds);
    }

    public function purgeTestProvider()
    {
        return array(
            array(array(123)),
            array(array(123, 456)),
            array(array(123, 456, 789)),
        );
    }

    public function testPurgeAll()
    {
        $this->cacheManager
            ->expects($this->once())
            ->method('invalidatePath')
            ->with('/', ['key' => 'ez-all', 'Host' => 'varnishpurgehost']);

        $this->configResolver
            ->expects($this->exactly(1))
            ->method('hasParameter')
            ->withAnyParameters()
            ->willReturn(true);

        $this->configResolver
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->withConsecutive(['http_cache.purge_servers'], [VarnishPurgeClient::INVALIDATE_TOKEN_PARAM])
            ->willReturnOnConsecutiveCalls(['https://varnishpurgehost'], null);

        $this->purgeClient->purgeAll();
    }

    public function testPurgeAllWithAuthHeaderAndKey()
    {
        $tokenName = 'x-purge-token';
        $token = 'secret-token-key';

        $this->cacheManager
            ->expects($this->once())
            ->method('invalidatePath')
            ->with('/', ['key' => 'ez-all', 'Host' => 'varnishpurgehost', $tokenName => $token]);

        $this->configResolver
            ->expects($this->exactly(1))
            ->method('hasParameter')
            ->withAnyParameters()
            ->willReturn(true);

        $this->configResolver
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->withConsecutive(['http_cache.purge_servers'], [VarnishPurgeClient::INVALIDATE_TOKEN_PARAM])
            ->willReturnOnConsecutiveCalls(['https://varnishpurgehost'], $token);

        $this->purgeClient->purgeAll();
    }
}
