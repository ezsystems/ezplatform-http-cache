<?php

namespace EzSystems\PlatformHttpCacheBundle\Handler;

use Symfony\Component\HttpFoundation\Response;

interface TagHandlerInterface
{
    public function addTagHeaders(Response $response, array $tags);
}
