<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated Use Tagger class from FOS: FOS\HttpCache\Handler\TagHandler which we overload
 *             (note: class name will change once we move to FosHttpCache 2.x)
 */
interface TagHandlerInterface
{
    public function addTagHeaders(Response $response, array $tags);
}
