<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\SPI\Persistence\Content\Location\Handler;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;

abstract class AbstractPublishSlot extends AbstractContentSlot
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Location\Handler
     */
    private $locationHandler;

    /**
     * @param \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface $purgeClient
     * @param \eZ\Publish\SPI\Persistence\Content\Location\Handler $spiLocationHandler
     */
    public function __construct(PurgeClientInterface $purgeClient, Handler $spiLocationHandler)
    {
        parent::__construct($purgeClient);
        $this->locationHandler = $spiLocationHandler;
    }

    /**
     * Extracts content id from signal.
     *
     * @param Signal $signal
     *
     * @return mixed
     */
    abstract protected function getContentId(Signal $signal);

    protected function generateTags(Signal $signal)
    {
        $contentId = $this->getContentId($signal);

        $tags = [
            // self in all forms (also without locations)
            'c' . $contentId,
            // reverse relations
            'r' . $contentId,
        ];

        foreach ($this->locationHandler->loadLocationsByContent($contentId) as $location) {
            // self
            $tags[] = 'l' . $location->id;
            // children
            $tags[] = 'pl' . $location->id;
            // reverse location relations
            $tags[] = 'rl' . $location->id;
            // parent
            $tags[] = 'l' . $location->parentId;
            // siblings
            $tags[] = 'pl' . $location->parentId;
        }

        return $tags;
    }
}
