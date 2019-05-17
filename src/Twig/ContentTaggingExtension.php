<?php

/**
 * File containing the ContentTaggingExtension class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Twig;

use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\PlatformHttpCacheBundle\Handler\TagHandler;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;
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

    /** @var \EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface */
    private $tagProvider;

    /**b@var \EzSystems\PlatformHttpCacheBundle\Handler\TagHandler */
    private $tagHandler;

    public function __construct(ResponseTagger $responseTagger, TagProviderInterface $tagProvider, TagHandler $tagHandler)
    {
        $this->responseTagger = $responseTagger;
        $this->tagProvider = $tagProvider;
        $this->tagHandler = $tagHandler;
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
            new Twig_SimpleFunction(
                'ez_httpcache_tag_*',
                [$this, 'tagHttpCacheFor']
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

    /**
     * @param $target
     * @param $id
     */
    public function tagHttpCacheFor($target, $id)
    {
        $method = 'getTagFor' . \strtolower(\trim($target)) . 'Id';
        if (\method_exists($this->tagProvider, $method)) {
            $this->tagHandler->addTags([$this->tagProvider->{$method}((int)$id)]);
        }
    }
}
