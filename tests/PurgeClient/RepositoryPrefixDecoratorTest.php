<?php

/**
 * File containing the RepositoryPrefixDecoratorTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Tests\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\RepositoryPrefixDecorator;
use PHPUnit\Framework\TestCase;

class RepositoryPrefixDecoratorTest extends TestCase
{
    public function testPurge()
    {
        $purgeClient = $this->createMock(PurgeClientInterface::class);
        $purgeClient
            ->expects($this->once())
            ->method('purge')
            ->with($this->equalTo(['location-123', 'content-44', 'ez-all']));

        $prefixDecorator = new RepositoryPrefixDecorator($purgeClient, '');
        $prefixDecorator->purge([123, 'content-44', 'ez-all']);
    }

    public function testPurgeWithPrefix()
    {
        $purgeClient = $this->createMock(PurgeClientInterface::class);
        $purgeClient
            ->expects($this->once())
            ->method('purge')
            ->with($this->equalTo(['intranet_location-123', 'intranet_content-44', 'intranet_ez-all']));

        $prefixDecorator = new RepositoryPrefixDecorator($purgeClient, 'intranet');
        $prefixDecorator->purge([123, 'content-44', 'ez-all']);
    }

    public function testPurgeAll()
    {
        $purgeClient = $this->createMock(PurgeClientInterface::class);
        $purgeClient
            ->expects($this->once())
            ->method('purgeAll')
            ->with($this->equalTo('ez-all'));

        $prefixDecorator = new RepositoryPrefixDecorator($purgeClient, '');
        $prefixDecorator->purgeAll();
    }

    public function testPurgeAllWithPrefix()
    {
        $purgeClient = $this->createMock(PurgeClientInterface::class);
        $purgeClient
            ->expects($this->once())
            ->method('purgeAll')
            ->with($this->equalTo('intranet_ez-all'));

        $prefixDecorator = new RepositoryPrefixDecorator($purgeClient, 'intranet');
        $prefixDecorator->purgeAll();
    }
}
