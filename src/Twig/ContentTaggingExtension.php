<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Twig;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Twig content extension for eZ Publish specific usage.
 * Exposes helpers to play with public API objects.
 */
class ContentTaggingExtension extends Twig_Extension
{
    /** @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger */
    protected $responseTagger;

    public function __construct(ResponseTagger $responseTagger)
    {
        $this->responseTagger = $responseTagger;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'ez_http_tag_location',
                [$this, 'tagHttpCacheForLocation']
            ),
        ];
    }

    /**
     * Adds tags to current response.
     *
     * @internal Function is only for use within this class (and implicit by Twig).
     *
     * @param Location $location
     */
    public function tagHttpCacheForLocation(Location $location)
    {
        $this->responseTagger->tag($location);
        $this->responseTagger->tag($location->getContentInfo());
    }
}
