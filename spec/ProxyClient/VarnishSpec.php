<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace spec\EzSystems\PlatformHttpCacheBundle\ProxyClient;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use FOS\HttpCache\ProxyClient\Dispatcher;
use Http\Message\RequestFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;

class VarnishSpec extends ObjectBehavior
{
    private const URI = "/";
    private const REQUEST_HEADERS = [
        "X-Some-Header" => "__SOME_HEADER_VALUE__"
    ];

    public function let(
        ConfigResolverInterface $configResolver,
        Dispatcher $httpDispatcher,
        RequestFactory $messageFactory,
        RequestInterface $request

    ) {
        $messageFactory->createRequest(
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->willReturn($request);

        $this->beConstructedWith($configResolver, $httpDispatcher, [], $messageFactory);
    }

    public function it_should_purge_with_additional_token_header_when_configuration_key_with_token_is_not_null(
        ConfigResolverInterface $configResolver,
        RequestFactory $messageFactory
    ) {
        $configResolver->hasParameter('http_cache.varnish_invalidate_token')->willReturn(true);
        $configResolver->getParameter('http_cache.varnish_invalidate_token')->willReturn('__TOKEN__');

        $this->purge(self::URI, self::REQUEST_HEADERS);

        $this->requestShouldHaveBeenCreatedWithHeaders(
            array_merge(self::REQUEST_HEADERS, ["X-Invalidate-Token" => "__TOKEN__"]),
            $messageFactory
        );
    }

    public function it_should_purge_without_additional_token_header_when_configuration_key_with_token_do_not_exist_in_configuration(
        ConfigResolverInterface $configResolver,
        RequestFactory $messageFactory
    ) {
        $configResolver->hasParameter('http_cache.varnish_invalidate_token')->willReturn(false);

        $this->purge(self::URI, self::REQUEST_HEADERS);

        $this->requestShouldHaveBeenCreatedWithHeaders(
            self::REQUEST_HEADERS,
            $messageFactory
        );
    }

    public function it_should_purge_without_additional_token_header_when_configuration_key_with_token_exists_but_is_null(
        ConfigResolverInterface $configResolver,
        RequestFactory $messageFactory
    ) {
        $configResolver->hasParameter('http_cache.varnish_invalidate_token')->willReturn(true);
        $configResolver->getParameter('http_cache.varnish_invalidate_token')->willReturn(null);

        $this->purge(self::URI, self::REQUEST_HEADERS);

        $this->requestShouldHaveBeenCreatedWithHeaders(
            self::REQUEST_HEADERS,
            $messageFactory
        );
    }

    private function requestShouldHaveBeenCreatedWithHeaders($headers, RequestFactory $messageFactory)
    {
        $messageFactory->createRequest(
            'PURGE',
            self::URI,
            $headers,
            Argument::any()
        )->shouldHaveBeenCalled();
    }
}
