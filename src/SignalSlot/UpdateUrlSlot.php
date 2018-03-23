<?php

/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\SignalSlot;

use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\SPI\Persistence\URL\Handler as UrlHandler;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;

/**
 * A slot handling UpdateUrlSignal.
 */
class UpdateUrlSlot extends AbstractContentSlot
{
    /** @var \eZ\Publish\SPI\Persistence\URL\Handler */
    private $urlHandler;

    /**
     * UpdateUrlSlot constructor.
     *
     * @param PurgeClientInterface $purgeClient
     * @param UrlHandler $urlHandler
     */
    public function __construct(PurgeClientInterface $purgeClient, UrlHandler $urlHandler)
    {
        parent::__construct($purgeClient);

        $this->urlHandler = $urlHandler;
    }

    /**
     * @param \eZ\Publish\Core\SignalSlot\Signal\URLService\UpdateUrlSignal $signal
     */
    public function generateTags(Signal $signal)
    {
        if ($signal->urlChanged) {
            return array_map(function ($contentId) {
                return 'content-' . $contentId;
            }, $this->urlHandler->findUsages($signal->urlId));
        }

        return [];
    }

    protected function supports(Signal $signal)
    {
        return $signal instanceof Signal\URLService\UpdateUrlSignal;
    }
}
