<?php

/**
 * File containing the ContentTaggingExtension class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Twig;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig content extension for eZ Publish specific usage.
 * Exposes helpers to play with public API objects.
 */
class ContentTaggingExtension extends AbstractExtension
{
    /** @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger */
    protected $responseTagger;

    public function __construct(ResponseTagger $responseTagger)
    {
        $this->responseTagger = $responseTagger;
    }

    /**
     * @return array|\Twig\TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_http_cache_tag_location',
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
