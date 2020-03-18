<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\RepositoryPrefixDecorator;
use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use PHPUnit\Framework\TestCase;

class RepositoryPrefixDecoratorTest extends TestCase
{
    /**
     * @var PurgeClientInterface
     */
    private $purgeClientMock;

    /**
     * @var RepositoryTagPrefix
     */
    private $tagPrefixMock;

    /**
     * @var RepositoryPrefixDecorator
     */
    private $prefixDecorator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->purgeClientMock = $this->createMock(PurgeClientInterface::class);
        $this->tagPrefixMock = $this->createMock(RepositoryTagPrefix::class);
        $this->prefixDecorator = new RepositoryPrefixDecorator($this->purgeClientMock, $this->tagPrefixMock);
    }

    protected function tearDown(): void
    {
        unset($this->purgeClientMock, $this->tagPrefixMock, $this->prefixDecorator);

        parent::tearDown();
    }

    public function testPurge()
    {
        $this->purgeClientMock
            ->expects($this->once())
            ->method('purge')
            ->with($this->equalTo(['l123', 'c44', 'ez-all']));

        $this->tagPrefixMock
            ->expects($this->once())
            ->method('getRepositoryPrefix')
            ->willReturn('');

        $this->prefixDecorator->purge(['l123', 'c44', 'ez-all']);
    }

    public function testPurgeWithPrefix()
    {
        $this->purgeClientMock
            ->expects($this->once())
            ->method('purge')
            ->with($this->equalTo(['0l123', '0c44', '0ez-all']));

        $this->tagPrefixMock
            ->expects($this->once())
            ->method('getRepositoryPrefix')
            ->willReturn('0');

        $this->prefixDecorator->purge(['l123', 'c44', 'ez-all']);
    }

    public function testPurgeAll()
    {
        $this->purgeClientMock
            ->expects($this->once())
            ->method('purgeAll');

        $this->tagPrefixMock
            ->expects($this->never())
            ->method('getRepositoryPrefix');

        $this->prefixDecorator->purgeAll();
    }
}
