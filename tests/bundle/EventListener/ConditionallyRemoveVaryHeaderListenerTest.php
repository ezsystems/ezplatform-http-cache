<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\HttpCache\EventListener;

use EzSystems\PlatformHttpCacheBundle\EventListener\ConditionallyRemoveVaryHeaderListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ConditionallyRemoveVaryHeaderListenerTest extends TestCase
{
    /** @var EzSystems\PlatformHttpCacheBundle\EventListener\ConditionallyRemoveVaryHeaderListener */
    public $conditionallyRemoveVaryHeaderListener;

    protected function setUp(): void
    {
        $this->conditionallyRemoveVaryHeaderListener = new ConditionallyRemoveVaryHeaderListener(['testroute1', 'testroute2']);
    }

    /**
     * @return iterable<array{varyHeaders: string[], expectedVaryHeaders: string[]}>
     */
    public function onKernelResponseProvider(): iterable
    {
        return [
            [
                'varyHeaders' => ['Cookie', 'vtest'],
                'expectedVaryHeaders' => ['vtest'],
            ],
            [
                'varyHeaders' => ['Authorization'],
                'expectedVaryHeaders' => [],
            ],
            [
                'varyHeaders' => ['cookie'],
                'expectedVaryHeaders' => [],
            ],
            [
                'varyHeaders' => ['authorization'],
                'expectedVaryHeaders' => [],
            ],
            [
                'varyHeaders' => ['Cookie', 'vtest'],
                'expectedVaryHeaders' => ['vtest'],
            ],
            [
                'varyHeaders' => ['vtest', 'Cookie'],
                'expectedVaryHeaders' => ['vtest'],
            ],
            [
                'varyHeaders' => ['cookie', 'vtest'],
                'expectedVaryHeaders' => ['vtest'],
            ],
            [
                'varyHeaders' => ['cookie', 'vtest', 'vtest2', 'vtest3', 'authorization'],
                'expectedVaryHeaders' => ['vtest', 'vtest2', 'vtest3'],
            ],
            [
                'varyHeaders' => ['cookie', 'vtest', 'vtest2', 'vtest3', 'authorization', 'vtest4'],
                'expectedVaryHeaders' => ['vtest', 'vtest2', 'vtest3', 'vtest4'],
            ],
        ];
    }

    /**
     * @dataProvider onKernelResponseProvider
     *
     * @param string[] $varyHeaders
     * @param string[] $expectedVaryHeaders
     */
    public function testOnKernelResponse(array $varyHeaders, array $expectedVaryHeaders): void
    {
        $request = $this->createMock(Request::class);
        $request->method('get')
            ->willReturn('testroute1');

        $response = new Response('test content', 200, ['vary' => $varyHeaders]);

        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response);

        $this->conditionallyRemoveVaryHeaderListener->onKernelResponse($event);
        self::assertNotContains('cookie', $response->headers->all('vary'));
        self::assertNotContains('Cookie', $response->headers->all('vary'));
        self::assertNotContains('authorization', $response->headers->all('vary'));
        self::assertNotContains('Authorization', $response->headers->all('vary'));
        foreach ($expectedVaryHeaders as $expectedVaryHeader) {
            self::assertContains($expectedVaryHeader, $response->headers->all('vary'));
        }
    }
}
