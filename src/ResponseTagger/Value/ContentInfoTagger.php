<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;

class ContentInfoTagger extends AbstractValueTagger
{
    public function tag($value)
    {
        if (!$value instanceof ContentInfo) {
            return $this;
        }

        $this->tagHandler->addTags(['c' . $value->id, 'ct' . $value->contentTypeId]);

        if ($value->mainLocationId) {
            $this->tagHandler->addTags(['l' . $value->mainLocationId]);
        }
    }
}
