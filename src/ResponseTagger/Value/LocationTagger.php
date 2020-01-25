<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
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
            $this->tagHandler->addTags(['l' . $value->id]);
        }

        $this->tagHandler->addTags(['pl' . $value->parentLocationId]);
        $this->tagHandler->addTags(
            array_map(
                static function ($pathItem) {
                    return 'p' . $pathItem;
                },
                $value->path
            )
        );
    }
}
