<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Controller;

use EzSystems\PlatformHttpCacheBundle\SymfonyResponseTagger;
use FOS\HttpCacheBundle\CacheManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use FOS\HttpCacheBundle\Handler\TagHandler;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class InvalidateTokenController
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var \EzSystems\PlatformHttpCacheBundle\SymfonyResponseTagger
     */
    private $responseTagger;

    /**
     * TokenController constructor.
     * @param ConfigResolverInterface $configResolver
     * @param int $ttl
     * @param \EzSystems\PlatformHttpCacheBundle\SymfonyResponseTagger $responseTagger
     * @internal param string $invalidatetoken
     */
    public function __construct(ConfigResolverInterface $configResolver, $ttl, SymfonyResponseTagger $responseTagger)
    {
        $this->configResolver = $configResolver;
        $this->ttl = $ttl;
        $this->responseTagger = $responseTagger;
    }

    /**
     * Request::isFromTrustedProxy is private in Symfony <3.1, so this is a re-implementation of it.
     *
     * @param Request $request
     * @return bool
     */
    private function isFromTrustedProxy(Request $request)
    {
        return $request->getTrustedProxies() && IpUtils::checkIp($request->server->get('REMOTE_ADDR'), $request->getTrustedProxies());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function tokenAction(Request $request)
    {
        $response = new Response();

        if (!$this->isFromTrustedProxy($request)) {
            $response->setStatusCode('401', 'Unauthorized');

            return $response;
        }

        // Important to keep this condition, as .vcl rely on this to prevent everyone from being able to fetch the token.
        if ($request->headers->get('accept') !== 'application/vnd.ezplatform.invalidate-token') {
            $response->setStatusCode('400', 'Bad request');

            return $response;
        }
        $this->responseTagger->addTags(['ez-invalidate-token']);

        $headers = $response->headers;
        $headers->set('Content-Type', 'application/vnd.ezplatform.invalidate-token');
        $headers->set('X-Invalidate-Token', $this->configResolver->getParameter('http_cache.varnish_invalidate_token'));
        $response->setSharedMaxAge($this->ttl);
        $response->setVary('Accept', true);

        return $response;
    }
}
