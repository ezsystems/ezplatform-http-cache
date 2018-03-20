<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Use ResponseTaggerInterface
 */
interface TagHandlerInterface
{
    public function addTagHeaders(Response $response, array $tags);
}
