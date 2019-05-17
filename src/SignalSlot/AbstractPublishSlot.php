<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\SPI\Persistence\Content\Location\Handler;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;

abstract class AbstractPublishSlot extends AbstractContentSlot
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Location\Handler
     */
    private $locationHandler;

    /**
     * @param \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface $purgeClient
     * @param \EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface $tagProvider
     * @param \eZ\Publish\SPI\Persistence\Content\Location\Handler $spiLocationHandler
     */
    public function __construct(PurgeClientInterface $purgeClient, TagProviderInterface $tagProvider, Handler $spiLocationHandler)
    {
        parent::__construct($purgeClient, $tagProvider);
        $this->locationHandler = $spiLocationHandler;
    }

    /**
     * Extracts content id from signal.
     *
     * @param Signal $signal
     * @return mixed
     */
    abstract protected function getContentId(Signal $signal);

    protected function generateTags(Signal $signal)
    {
        $contentId = $this->getContentId($signal);

        $tags = [
            // self in all forms (also without locations)
            $this->tagProvider->getTagForContentId($contentId),
            // reverse relations
            $this->tagProvider->getTagForRelationId($contentId),
        ];

        foreach ($this->locationHandler->loadLocationsByContent($contentId) as $location) {
            // self
            $tags[] = $this->tagProvider->getTagForLocationId($location->id);
            // children
            $tags[] = $this->tagProvider->getTagForParentId($location->id);
            // reverse location relations
            $tags[] = $this->tagProvider->getTagForRelationLocationId($location->id);
            // parent
            $tags[] = $this->tagProvider->getTagForLocationId($location->parentId);
            // siblings
            $tags[] = $this->tagProvider->getTagForParentId($location->parentId);
        }

        return $tags;
    }
}
