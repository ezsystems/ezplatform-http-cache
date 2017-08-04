<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as SPILocationHandler;

class RemoveTranslationSlot extends AbstractContentSlot
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Handler
     */
    protected $locationHandler;

    /**
     * @param \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface $purgeClient
     * @param \eZ\Publish\SPI\Persistence\Content\Location\Handler $spiLocationHandler
     */
    public function __construct(
        PurgeClientInterface $purgeClient,
        SPILocationHandler $spiLocationHandler
    ) {
        parent::__construct($purgeClient);
        $this->locationHandler = $spiLocationHandler;
    }

    /**
     * Checks if $signal is supported by this handler.
     *
     * @param \eZ\Publish\Core\SignalSlot\Signal $signal
     *
     * @return bool
     */
    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\ContentService\RemoveTranslationSignal;
    }

    /**
     * Generate tags for content, relation, locations and parent locations.
     *
     * RemoveTranslationSignal doesn't provide locationId, so Locations need to be fetched
     *
     * @param \eZ\Publish\Core\SignalSlot\Signal $signal
     *
     * @return array
     */
    protected function generateTags(Signal $signal)
    {
        // aligned with PublishVersionSlot as translation removal is essentially publishing new Version.
        $tags = parent::generateTags($signal);
        foreach ($this->locationHandler->loadLocationsByContent($signal->contentId) as $location) {
            // self
            $tags[] = 'location-' . $location->id;
            // children
            $tags[] = 'parent-' . $location->id;
            // parent
            $tags[] = 'location-' . $location->parentId;
            // siblings
            $tags[] = 'parent-' . $location->parentId;
        }

        return $tags;
    }
}
