<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;

class ContentValueViewTagger implements ResponseTagger
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger
     */
    private $contentInfoTagger;

    public function __construct(ResponseTagger $contentInfoTagger)
    {
        $this->contentInfoTagger = $contentInfoTagger;
    }

    public function tag($view)
    {
        if (!$view instanceof ContentValueView || !($content = $view->getContent()) instanceof Content) {
            return $this;
        }

        $contentInfo = $content->getVersionInfo()->getContentInfo();
        $this->contentInfoTagger->tag($contentInfo);
    }
}
