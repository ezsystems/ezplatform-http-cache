<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use eZ\Publish\API\Repository\Values\Content\Location;

class LocationTagger extends AbstractValueTagger
{
    public function tag($value)
    {
        if (!$value instanceof Location) {
            return $this;
        }

        if ($value->id !== $value->contentInfo->mainLocationId) {
            $this->tagHandler->addTags([$this->tagProvider->getTagForLocationId($value->id)]);
        }

        $this->tagHandler->addTags([$this->tagProvider->getTagForParentId($value->parentLocationId)]);
        $this->tagHandler->addTags(
            array_map(
                function ($pathItem) {
                    return $this->tagProvider->getTagForPathId($pathItem);
                },
                $value->path
            )
        );
    }
}
