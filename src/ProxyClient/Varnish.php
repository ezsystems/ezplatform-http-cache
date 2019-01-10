<?php

/**
 * File containing the Varnish class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ProxyClient;

use FOS\HttpCache\ProxyClient\Varnish as FOSVarnish;

/**
 * Class Varnish.
 *
 * We need to support https as Platform.sh uses https by default.
 */
class Varnish extends FOSVarnish
{
    /**
     * {@inheritdoc}
     */
    protected function getAllowedSchemes()
    {
        return array('http', 'https');
    }
}
