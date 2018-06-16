<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;

class ContentInfoTagger extends AbstractValueTagger
{
    public function tag($value)
    {
        if (!$value instanceof ContentInfo) {
            return $this;
        }

        $this->tagHandler->addTags(['content-' . $value->id, 'content-type-' . $value->contentTypeId]);

        if ($value->mainLocationId) {
            $this->tagHandler->addTags(['location-' . $value->mainLocationId]);
        }
    }
}
