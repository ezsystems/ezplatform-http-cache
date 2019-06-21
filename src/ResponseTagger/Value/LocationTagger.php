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
            $this->responseTagger->addTags(['location-' . $value->id]);
        }

        $this->responseTagger->addTags(['parent-' . $value->parentLocationId]);
        $this->responseTagger->addTags(
            array_map(
                function ($pathItem) {
                    return 'path-' . $pathItem;
                },
                $value->path
            )
        );
    }
}
