<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Use Tagger class from FOS: FOS\HttpCache\Handler\TagHandler which we overload
 *             This is not in use anymore as of 0.7!
 */
interface TagHandlerInterface
{
    public function addTagHeaders(Response $response, array $tags);
}
