<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle;

use eZ\Bundle\EzPublishCoreBundle\HttpCache;
use EzSystems\PlatformHttpCacheBundle\Proxy\TagAwareStore;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Custom AppCache.
 *
 * Enable by setting SYMFONY_HTTP_CACHE_CLASS to 'EzSystems\PlatformHttpCacheBundle\AppCache'
 */
class AppCache extends HttpCache
{
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
