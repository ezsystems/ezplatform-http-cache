<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;

class LocationValueViewTagger implements ResponseTagger
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger
     */
    private $locationTagger;

    public function __construct(ResponseTagger $locationTagger)
    {
        $this->locationTagger = $locationTagger;
    }

    public function tag($view)
    {
        if (!$view instanceof LocationValueView || !($location = $view->getLocation()) instanceof Location) {
            return $this;
        }

        $this->locationTagger->tag($location);
    }
}
