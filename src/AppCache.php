<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle;

use EzSystems\PlatformHttpCacheBundle\Proxy\TagAwareStore;
use EzSystems\PlatformHttpCacheBundle\Proxy\UserContextListener;
use FOS\HttpCache\SymfonyCache\EventDispatchingHttpCache;
use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Custom AppCache.
 *
 * "deprecated" This and classes used here will be removed once this package moves to FosHttpCache 2.x.
 */
class AppCache extends HttpCache
{
    use EventDispatchingHttpCache;

    public function __construct(KernelInterface $kernel, $cacheDir = null)
    {
        parent::__construct($kernel, $cacheDir);
        $this->addSubscriber(new UserContextListener(['session_name_prefix' => 'eZSESSID']));
    }

    /**
     * {@inheritdoc}
     */
    protected function createStore()
    {
        return new TagAwareStore($this->cacheDir ?: $this->kernel->getCacheDir() . '/http_cache');
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = parent::handle($request, $type, $catch);

        if (!$this->getKernel()->isDebug()) {
            $this->cleanupHeadersForProd($response);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function invalidate(Request $request, $catch = false)
    {
        if ($request->getMethod() !== 'PURGE' && $request->getMethod() !== 'BAN') {
            return parent::invalidate($request, $catch);
        }

        // Reject all non-authorized clients
        if (!in_array($request->getClientIp(), $this->getInternalAllowedIPs())) {
            return new Response('', 405);
        }

        $response = new Response();
        $store = $this->getStore();
        if ($store instanceof RequestAwarePurger) {
            $result = $store->purgeByRequest($request);
        } else {
            $result = $store->purge($request->getUri());
        }

        if ($result === true) {
            $response->setStatusCode(200, 'Purged');
        } else {
            $response->setStatusCode(404, 'Not purged');
        }

        return $response;
    }

    /**
     * Returns an array of allowed IPs for Http PURGE requests.
     *
     * @return array
     */
    protected function getInternalAllowedIPs()
    {
        return ['127.0.0.1', '::1'];
    }

    /**
     * Perform cleanup of reponse.
     *
     * @param Response $response
     */
    protected function cleanupHeadersForProd(Response $response)
    {
        // remove headers that identify the content or internal digest info
        $response->headers->remove('xkey');
        $response->headers->remove('x-content-digest');

        // remove vary by X-User-Hash header
        $varyValues = [];
        $variesByUser = false;
        foreach ($response->getVary() as $value) {
            if ($value === 'X-User-Hash') {
                $variesByUser = true;
            } else {
                $varyValues[] = $value;
            }
        }

        // update resulting vary header in normalized form (comma separated)
        if (empty($varyValues)) {
            $response->headers->remove('Vary');
        } else {
            $response->setVary(implode(', ', $varyValues));
        }

        // If cache varies by user hash, then make sure other proxies don't cache this
        if ($variesByUser) {
            $response->setPrivate();
            $response->headers->removeCacheControlDirective('s-maxage');
            $response->headers->addCacheControlDirective('no-cache');
        }
    }
}
