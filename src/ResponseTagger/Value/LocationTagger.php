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
            $this->tagHandler->addTags(['location-' . $value->id]);
        }

        $this->tagHandler->addTags(['parent-' . $value->parentLocationId]);
        $this->tagHandler->addTags(
            array_map(
                function ($pathItem) {
                    return 'path-' . $pathItem;
                },
                $value->path
            )
        );
    }
}
