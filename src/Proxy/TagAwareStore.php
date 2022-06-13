<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Proxy;

use EzSystems\PlatformHttpCacheBundle\RequestAwarePurger;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * TagAwareStore implements all the logic for storing cache metadata regarding tags (locations, content type, ..).
 */
class TagAwareStore extends Store implements RequestAwarePurger
{
    const TAG_CACHE_DIR = 'ez';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fs;

    /**
     * Injects a Filesystem instance
     * For unit tests only.
     *
     * @internal
     *
     * @param \Symfony\Component\Filesystem\Filesystem $fs
     */
    public function setFilesystem(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    private function getFilesystem()
    {
        if (!isset($this->fs)) {
            $this->fs = new Filesystem();
        }

        return $this->fs;
    }

    /**
     * Writes a cache entry to the store for the given Request and Response.
     *
     * Existing entries are read and any that match the response are removed. This
     * method calls write with the new list of cache entries.
     *
     * @param Request  $request  A Request instance
     * @param Response $response A Response instance
     *
     * @return string The key under which the response is stored
     *
     * @throws \RuntimeException
     */
    public function write(Request $request, Response $response)
    {
        $key = parent::write($request, $response);

        // Get tags in order to save them
        $digest = $response->headers->get('X-Content-Digest');
        $tags = $response->headers->get('xkey', null, false);

        if (count($tags) === 1) {
            // Handle string based header (her gotten as single item array with space separated string)
            $tags = explode(' ', $tags[0]);
        }

        foreach (array_unique($tags) as $tag) {
            if (false === $this->saveTag($tag, $digest)) {
                throw new \RuntimeException('Unable to store the cache tag meta information.');
            }
        }

        return $key;
    }

    /**
     * Save digest for the given tag.
     *
     * @param string $tag    The tag key
     * @param string $digest The digest hash to store representing the cache item.
     *
     * @return bool
     */
    private function saveTag($tag, $digest)
    {
        $path = $this->getTagPath($tag) . \DIRECTORY_SEPARATOR . $digest;
        if (!is_dir(dirname($path)) && false === @mkdir(dirname($path), 0777, true) && !is_dir(dirname($path))) {
            return false;
        }

        $tmpFile = tempnam(dirname($path), basename($path));
        if (false === $fp = @fopen($tmpFile, 'wb')) {
            return false;
        }
        @fwrite($fp, $digest);
        @fclose($fp);

        if ($digest != file_get_contents($tmpFile)) {
            return false;
        }

        if (false === @rename($tmpFile, $path)) {
            return false;
        }

        @chmod($path, 0666 & ~umask());

        return true;
    }

    /**
     * Purges data from $request.
     * If key or X-Location-Id (deprecated) header is present, the store will purge cache for given locationId or group of locationIds.
     * If not, regular purge by URI will occur.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool True if purge was successful. False otherwise
     */
    public function purgeByRequest(Request $request)
    {
        if (!$request->headers->has('X-Location-Id') && !$request->headers->has('key')) {
            return $this->purge($request->getUri());
        }

        // For BC with older purge code covering most use cases.
        $locationId = $request->headers->get('X-Location-Id');
        if ($locationId === '*' || $locationId === '.*') {
            return $this->purgeAllContent();
        }

        if ($request->headers->has('key')) {
            $tags = explode(' ', $request->headers->get('key'));
        } elseif ($locationId[0] === '(' && substr($locationId, -1) === ')') {
            // Deprecated: (123|456|789) => Purge for #123, #456 and #789 location IDs.
            $tags = array_map(
                static function ($id) {return 'l' . $id;},
                explode('|', substr($locationId, 1, -1))
            );
        } else {
            $tags = ['l' . $locationId];
        }

        if (empty($tags)) {
            return false;
        }

        foreach ($tags as $tag) {
            $this->purgeByCacheTag($tag);
        }

        return true;
    }

    /**
     * Purge all cache.
     */
    protected function purgeAllContent()
    {
        $this->getFilesystem()->remove((new Finder())->in($this->root)->depth(0));
    }

    /**
     * Purges cache for tag.
     *
     * @param string $tag
     */
    private function purgeByCacheTag($tag)
    {
        $cacheTagsCacheDir = $this->getTagPath($tag);
        if (!file_exists($cacheTagsCacheDir) || !is_dir($cacheTagsCacheDir)) {
            return;
        }

        $files = (new Finder())->files()->in($cacheTagsCacheDir);
        foreach ($files as $file) {
            // @todo Change to be able to reuse parent::invalidate() or parent::purge() ?
            if ($digest = file_get_contents($file->getRealPath())) {
                @unlink($this->getPath($digest));
            }
            @unlink($file);
        }
        // We let folder stay in case another process have just written new cache tags.
    }

    /**
     * Returns cache dir for $tag.
     *
     * This method is public only for unit tests.
     * Use it only if you know what you are doing.
     *
     * @internal
     *
     * @param int $tag
     *
     * @return string
     */
    public function getTagPath($tag = null)
    {
        $path = $this->root . \DIRECTORY_SEPARATOR . static::TAG_CACHE_DIR;
        if ($tag) {
            // Flip the tag so we put id first so it gets sliced into folders.
            // (otherwise we would easily reach inode limits on file system)
            $tag = strrev($tag);
            $length = strlen($tag);
            $path .= \DIRECTORY_SEPARATOR . substr($tag, 0, 2);

            if ($length > 2) {
                $path .= \DIRECTORY_SEPARATOR . substr($tag, 2, 2);
            }

            if ($length > 4) {
                $path .= \DIRECTORY_SEPARATOR . substr($tag, 4);
            }
        }

        return $path;
    }
}
