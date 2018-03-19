<?php

namespace EzSystems\PlatformHttpCacheBundle\Handler;

use Symfony\Component\HttpFoundation\Response;

interface TagHandlerInterface
{
    /**
     * @deprecated Use addTags()
     */
    public function addTagHeaders(Response $response, array $tags);

    /**
     * @param array $tags
     *
     * @return $this
     */
    public function addTags(array $tags);

    /**
     * @return bool True if this handler will set at least one tag
     */
    public function hasTags();
}
